<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\MemberOrder;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\OrderStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OrderStatusSeeder::class);
    }

    public function test_visible_batch_appears_on_dedicated_catalog_page(): void
    {
        [$batch, $product] = $this->catalogBatch();

        $this->get(route('tracking.index'))
            ->assertOk()
            ->assertDontSee('Pilih batch yang sedang dibuka.')
            ->assertDontSee($batch->batch_name);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Pilih batch yang sedang dibuka.')
            ->assertSee($batch->batch_name)
            ->assertSee(route('catalog.show', $batch), false);

        $this->get(route('catalog.show', $batch))
            ->assertOk()
            ->assertSee($batch->batch_name)
            ->assertSee('Daftar variasi yang tersedia')
            ->assertSee($product->variant)
            ->assertSee('data-catalog-variant', false)
            ->assertSee('data-catalog-variant-modal', false)
            ->assertSee('Pilih variasi')
            ->assertSee('Bayar DP')
            ->assertSee('Bayar Lunas');
    }

    public function test_payment_proof_is_required_before_catalog_order_can_be_paid(): void
    {
        [$batch, $product] = $this->catalogBatch();

        $this->from(route('catalog.show', $batch))->post(route('catalog.checkout', $batch), [
            'customer_name' => 'Athen',
            'customer_username' => 'athen.go',
            'payment_type' => 'dp',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertSessionHasErrors('payment_proof');

        $this->assertDatabaseCount('member_orders', 0);
    }

    public function test_dp_checkout_creates_customer_order_and_private_payment_proof(): void
    {
        Storage::fake('local');
        [$batch, $product] = $this->catalogBatch();

        $response = $this->post(route('catalog.checkout', $batch), [
            'customer_name' => 'Athen',
            'customer_username' => '@Athen.Go',
            'payment_type' => 'dp',
            'payment_proof' => UploadedFile::fake()->image('proof.jpg', 800, 800),
            'notes' => 'ALT Ver.',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $order = MemberOrder::with(['member', 'paymentStatus', 'items'])->sole();

        $response->assertRedirect(route('catalog.show', $batch));
        $this->assertSame('athen.go', $order->member->username);
        $this->assertSame('catalog', $order->order_source);
        $this->assertSame('dp', $order->payment_type);
        $this->assertSame('menunggu-pelunasan', $order->paymentStatus->code);
        $this->assertSame(200000.0, (float) $order->payment_amount);
        $this->assertSame(360000.0, (float) $order->total_amount);
        $this->assertSame(2, $order->items->sum('quantity'));
        Storage::disk('local')->assertExists($order->payment_proof_path);
    }

    public function test_full_checkout_marks_order_as_paid_in_full(): void
    {
        Storage::fake('local');
        [$batch, $product] = $this->catalogBatch();

        $this->post(route('catalog.checkout', $batch), [
            'customer_name' => 'Luna',
            'customer_username' => 'luna.line',
            'payment_type' => 'full',
            'payment_proof' => UploadedFile::fake()->image('paid.png', 800, 800),
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('catalog.show', $batch));

        $order = MemberOrder::with('paymentStatus')->sole();
        $this->assertSame('full', $order->payment_type);
        $this->assertSame('lunas', $order->paymentStatus->code);
        $this->assertSame(180000.0, (float) $order->payment_amount);
        $this->assertSame(180000.0, (float) $order->total_amount);
    }

    public function test_admin_can_create_catalog_batch_with_optional_images_and_variants(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.batches.store'), [
            'batch_name' => 'JENNIE - Fallen Angel EP',
            'description' => 'Open pre-order album.',
            'ordering_deadline' => now()->addWeek()->format('Y-m-d H:i:s'),
            'is_catalog_visible' => '1',
            'catalog_image' => UploadedFile::fake()->image('catalog.jpg', 1200, 1200),
            'qris_image' => UploadedFile::fake()->image('qris.png', 800, 800),
            'variants' => [
                ['name' => 'ALT Ver.', 'dp_price' => 154500, 'full_price' => 257400, 'is_available' => '1'],
                ['name' => 'FALLEN ANGEL Ver.', 'dp_price' => 154500, 'full_price' => 257400, 'is_available' => '1'],
            ],
        ])->assertRedirect();

        $batch = Batch::with('products')->sole();
        $this->assertTrue($batch->is_catalog_visible);
        $this->assertCount(2, $batch->products);
        $this->assertSame('154500.00', $batch->products->first()->pivot->dp_price);
        Storage::disk('public')->assertExists($batch->catalog_image_path);
        Storage::disk('public')->assertExists($batch->qris_image_path);
    }

    public function test_batch_photo_can_be_replaced_without_leaving_the_old_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        Storage::disk('public')->put('catalog/batches/old-photo.jpg', 'old-photo');
        $batch = Batch::factory()->create([
            'catalog_image_path' => 'catalog/batches/old-photo.jpg',
        ]);

        $this->actingAs($admin)->put(route('admin.batches.update', $batch), [
            'batch_name' => $batch->batch_name,
            'catalog_image' => UploadedFile::fake()->image('replacement.jpg', 1200, 1200),
            'remove_catalog_image' => '0',
        ])->assertRedirect();

        $batch->refresh();
        $this->assertNotSame('catalog/batches/old-photo.jpg', $batch->catalog_image_path);
        Storage::disk('public')->assertExists($batch->catalog_image_path);
        Storage::disk('public')->assertMissing('catalog/batches/old-photo.jpg');
    }

    public function test_batch_photo_is_only_deleted_after_admin_explicitly_removes_it(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        Storage::disk('public')->put('catalog/batches/saved-photo.jpg', 'saved-photo');
        $batch = Batch::factory()->create([
            'catalog_image_path' => 'catalog/batches/saved-photo.jpg',
        ]);

        $this->actingAs($admin)->put(route('admin.batches.update', $batch), [
            'batch_name' => $batch->batch_name,
            'remove_catalog_image' => '1',
        ])->assertRedirect();

        $this->assertNull($batch->fresh()->catalog_image_path);
        Storage::disk('public')->assertMissing('catalog/batches/saved-photo.jpg');
    }

    public function test_new_batch_photo_uses_configured_cloud_disk_and_only_saves_its_location(): void
    {
        Storage::fake('catalog_cloud');
        config()->set('filesystems.catalog_upload_disk', 'catalog_cloud');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.batches.store'), [
            'batch_name' => 'Cloud Batch Photo',
            'catalog_image' => UploadedFile::fake()->image('cloud-cover.jpg', 1200, 1200),
        ])->assertRedirect();

        $batch = Batch::where('batch_name', 'Cloud Batch Photo')->sole();
        $this->assertSame('catalog_cloud', $batch->catalog_image_disk);
        $this->assertStringStartsWith('catalog/batches/', $batch->catalog_image_path);
        Storage::disk('catalog_cloud')->assertExists($batch->catalog_image_path);
    }

    private function catalogBatch(): array
    {
        $batch = Batch::factory()->create([
            'batch_name' => 'JENNIE - Fallen Angel EP',
            'is_catalog_visible' => true,
            'ordering_deadline' => now()->addWeek(),
            'qris_image_path' => 'catalog/qris/demo.png',
        ]);
        $product = Product::factory()->create([
            'name' => $batch->batch_name,
            'variant' => 'ALT Ver.',
            'default_price' => 180000,
        ]);
        $batch->products()->attach($product, [
            'dp_price' => 100000,
            'full_price' => 180000,
            'sort_order' => 0,
            'is_available' => true,
        ]);

        return [$batch, $product];
    }
}
