<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use App\Services\LegacySpreadsheetImportService;
use App\Services\StatusTransitionService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderRecapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_status(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.order-statuses.store'), [
            'name' => 'Menunggu Pelunasan',
            'code' => 'menunggu-pelunasan',
            'color' => '#2563eb',
            'sequence' => 11,
            'status_type' => 'process',
            'scope' => 'member_order',
            'is_active' => '1',
        ])->assertRedirect(route('admin.order-statuses.index'));

        $this->assertDatabaseHas('order_statuses', ['code' => 'menunggu-pelunasan']);
    }

    public function test_used_status_is_deactivated_instead_of_deleted(): void
    {
        $admin = User::factory()->create();
        $status = OrderStatus::factory()->create(['scope' => 'batch']);
        Batch::factory()->create(['current_status_id' => $status->id]);

        $this->actingAs($admin)->delete(route('admin.order-statuses.destroy', $status))->assertRedirect();

        $this->assertDatabaseHas('order_statuses', ['id' => $status->id, 'is_active' => false]);
    }

    public function test_inactive_status_does_not_appear_as_new_choice(): void
    {
        $admin = User::factory()->create();
        OrderStatus::factory()->create(['name' => 'Status Aktif', 'scope' => 'member_order', 'is_active' => true]);
        OrderStatus::factory()->create(['name' => 'Status Nonaktif', 'scope' => 'member_order', 'is_active' => false]);

        $this->actingAs($admin)->get(route('admin.member-orders.create'))
            ->assertOk()
            ->assertSee('Status Aktif')
            ->assertDontSee('Status Nonaktif');
    }

    public function test_admin_can_select_managed_payment_status_for_order(): void
    {
        $admin = User::factory()->create();
        $member = Member::factory()->create();
        $batch = Batch::factory()->create();
        $product = Product::factory()->create();
        $paymentStatus = OrderStatus::factory()->create([
            'name' => 'Lunas',
            'scope' => 'payment',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.member-orders.store'), [
            'order_code' => 'ORD-PAYMENT-001',
            'member_id' => $member->id,
            'batch_id' => $batch->id,
            'payment_status_id' => $paymentStatus->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('member_orders', [
            'order_code' => 'ORD-PAYMENT-001',
            'payment_status_id' => $paymentStatus->id,
        ]);
    }

    public function test_progress_status_cannot_be_used_as_payment_status(): void
    {
        $admin = User::factory()->create();
        $member = Member::factory()->create();
        $batch = Batch::factory()->create();
        $product = Product::factory()->create();
        $progressStatus = OrderStatus::factory()->create([
            'scope' => 'member_order',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.member-orders.store'), [
            'order_code' => 'ORD-PAYMENT-INVALID',
            'member_id' => $member->id,
            'batch_id' => $batch->id,
            'payment_status_id' => $progressStatus->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
            ]],
        ])->assertSessionHasErrors('payment_status_id');

        $this->assertDatabaseMissing('member_orders', ['order_code' => 'ORD-PAYMENT-INVALID']);
    }

    public function test_status_transition_creates_history(): void
    {
        $admin = User::factory()->create();
        $old = OrderStatus::factory()->create(['scope' => 'batch']);
        $new = OrderStatus::factory()->create(['scope' => 'batch']);
        $batch = Batch::factory()->create(['current_status_id' => $old->id]);

        app(StatusTransitionService::class)->transition($batch, $new, $admin, 'Tiba di gudang.');

        $this->assertDatabaseHas('status_histories', [
            'trackable_type' => Batch::class,
            'trackable_id' => $batch->id,
            'old_status_id' => $old->id,
            'new_status_id' => $new->id,
            'changed_by' => $admin->id,
        ]);
    }

    public function test_member_order_follows_batch_status_by_default(): void
    {
        $status = OrderStatus::factory()->create(['scope' => 'batch']);
        $batch = Batch::factory()->create(['current_status_id' => $status->id]);
        $order = MemberOrder::factory()->create(['batch_id' => $batch->id, 'override_status_id' => null]);

        $order->load('batch.currentStatus', 'overrideStatus');

        $this->assertTrue($order->effective_status->is($status));
    }

    public function test_member_order_override_replaces_batch_status(): void
    {
        $batchStatus = OrderStatus::factory()->create(['scope' => 'batch']);
        $override = OrderStatus::factory()->create(['scope' => 'member_order']);
        $batch = Batch::factory()->create(['current_status_id' => $batchStatus->id]);
        $order = MemberOrder::factory()->create(['batch_id' => $batch->id, 'override_status_id' => $override->id]);

        $order->load('batch.currentStatus', 'overrideStatus');

        $this->assertTrue($order->effective_status->is($override));
    }

    public function test_item_override_only_affects_that_item(): void
    {
        $batchStatus = OrderStatus::factory()->create(['scope' => 'batch']);
        $itemStatus = OrderStatus::factory()->create(['scope' => 'order_item']);
        $batch = Batch::factory()->create(['current_status_id' => $batchStatus->id]);
        $order = MemberOrder::factory()->create(['batch_id' => $batch->id]);
        $specialItem = OrderItem::factory()->create(['member_order_id' => $order->id, 'override_status_id' => $itemStatus->id]);
        $normalItem = OrderItem::factory()->create(['member_order_id' => $order->id, 'override_status_id' => null]);

        $specialItem->load('overrideStatus', 'order.batch.currentStatus', 'order.overrideStatus');
        $normalItem->load('overrideStatus', 'order.batch.currentStatus', 'order.overrideStatus');

        $this->assertTrue($specialItem->effective_status->is($itemStatus));
        $this->assertTrue($normalItem->effective_status->is($batchStatus));
    }

    public function test_clearing_override_returns_to_parent_status(): void
    {
        $admin = User::factory()->create();
        $batchStatus = OrderStatus::factory()->create(['scope' => 'batch']);
        $override = OrderStatus::factory()->create(['scope' => 'member_order']);
        $batch = Batch::factory()->create(['current_status_id' => $batchStatus->id]);
        $order = MemberOrder::factory()->create(['batch_id' => $batch->id, 'override_status_id' => $override->id]);

        app(StatusTransitionService::class)->clearOverride($order, $admin);
        $order->load('batch.currentStatus', 'overrideStatus');

        $this->assertTrue($order->effective_status->is($batchStatus));
    }

    public function test_member_cannot_view_other_member_order(): void
    {
        $memberA = Member::factory()->create();
        $memberB = Member::factory()->create();
        $orderB = MemberOrder::factory()->create(['member_id' => $memberB->id]);

        $this->get(route('tracking.order', [$memberA->member_code, $orderB]))->assertForbidden();
    }

    public function test_public_can_track_one_order_with_order_code(): void
    {
        $order = MemberOrder::factory()->create(['order_code' => 'ORD-PUBLIC-001']);
        OrderItem::factory()->create(['member_order_id' => $order->id, 'item_name' => 'NCT Wish Album']);

        $this->post(route('tracking.lookup'), ['lookup' => 'ORD-PUBLIC-001'])
            ->assertRedirect(route('tracking.progress', 'ORD-PUBLIC-001'));

        $this->get(route('tracking.progress', 'ORD-PUBLIC-001'))
            ->assertOk()
            ->assertSee('ORD-PUBLIC-001')
            ->assertSee('NCT Wish Album')
            ->assertDontSee($order->member->email);
    }

    public function test_customer_can_find_all_order_history_with_registered_email(): void
    {
        $member = Member::factory()->create(['email' => 'buyer@example.com']);
        $order = MemberOrder::factory()->create([
            'member_id' => $member->id,
            'order_code' => 'ORD-HISTORY-001',
        ]);

        $this->post(route('tracking.history.lookup'), ['email' => ' Buyer@Example.com '])
            ->assertRedirect(route('tracking.member', $member->member_code));

        $this->get(route('tracking.member', $member->member_code))
            ->assertOk()
            ->assertSee('Riwayat Pembelian')
            ->assertSee($order->order_code);
    }

    public function test_unregistered_email_does_not_show_history(): void
    {
        $this->from(route('tracking.index'))
            ->post(route('tracking.history.lookup'), ['email' => 'missing@example.com'])
            ->assertRedirect(route('tracking.index'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_create_customer_contact_data(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.members.store'), [
            'display_name' => 'Kim Buyer',
            'email' => 'KIM@EXAMPLE.COM',
            'phone' => '081234567890',
            'address' => 'Jl. Kpop No. 7, Jakarta',
            'is_active' => '1',
        ])->assertRedirect(route('admin.members.index'));

        $this->assertDatabaseHas('members', [
            'display_name' => 'Kim Buyer',
            'email' => 'kim@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Kpop No. 7, Jakarta',
        ]);

        $this->assertNotNull(Member::where('email', 'kim@example.com')->value('member_code'));
    }

    public function test_order_form_hydrates_item_from_selected_product(): void
    {
        $admin = User::factory()->create();
        $member = Member::factory()->create();
        $batch = Batch::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Wish Bakery Plush',
            'variant' => 'Sakuya',
            'default_price' => 395000,
        ]);

        $this->actingAs($admin)->post(route('admin.member-orders.store'), [
            'order_code' => 'ORD-PRODUCT-001',
            'member_id' => $member->id,
            'batch_id' => $batch->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => '',
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'item_name' => 'Wish Bakery Plush',
            'variant' => 'Sakuya',
            'quantity' => 2,
            'unit_price' => 395000,
            'subtotal' => 790000,
        ]);
    }

    public function test_member_and_batch_combination_cannot_duplicate(): void
    {
        $member = Member::factory()->create();
        $batch = Batch::factory()->create();
        MemberOrder::factory()->create(['member_id' => $member->id, 'batch_id' => $batch->id]);

        $this->expectException(QueryException::class);
        MemberOrder::factory()->create(['member_id' => $member->id, 'batch_id' => $batch->id]);
    }

    public function test_spreadsheet_import_preview_carries_previous_member_name(): void
    {
        $preview = app(LegacySpreadsheetImportService::class)->preview([
            'A-E' => [
                ['NAMA', 'BATCH'],
                ['Fadhia', '1604'],
                ['', '1666'],
                ['', '1729'],
            ],
        ]);

        $this->assertSame(3, $preview['stats']['valid']);
        $this->assertSame('Fadhia', $preview['rows'][1]['member_name']);
        $this->assertSame('1666', $preview['rows'][1]['batch_number']);
    }

    public function test_spreadsheet_import_does_not_duplicate_rows(): void
    {
        $result = app(LegacySpreadsheetImportService::class)->import([
            'A-E' => [
                ['NAMA', 'BATCH'],
                ['Fadhia', '1604'],
                ['', '1604'],
            ],
        ]);

        $this->assertSame(1, $result['stats']['valid']);
        $this->assertSame(1, $result['stats']['duplicates']);
        $this->assertDatabaseCount('member_orders', 1);
    }

    public function test_only_admin_can_access_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_scope_status_cannot_be_selected(): void
    {
        $batch = Batch::factory()->create();
        $itemStatus = OrderStatus::factory()->create(['scope' => 'order_item']);

        $this->expectException(ValidationException::class);
        app(StatusTransitionService::class)->transition($batch, $itemStatus);
    }

    public function test_invalid_status_transition_is_atomic(): void
    {
        $old = OrderStatus::factory()->create(['scope' => 'batch']);
        $wrong = OrderStatus::factory()->create(['scope' => 'order_item']);
        $batch = Batch::factory()->create(['current_status_id' => $old->id]);

        try {
            app(StatusTransitionService::class)->transition($batch, $wrong);
        } catch (ValidationException) {
            //
        }

        $this->assertSame($old->id, $batch->refresh()->current_status_id);
        $this->assertDatabaseCount('status_histories', 0);
    }
}
