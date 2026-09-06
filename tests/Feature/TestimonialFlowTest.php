<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestimonialFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_asked_to_login_before_seeing_testimonial_products(): void
    {
        $this->get(route('testimonials.index'))
            ->assertOk()
            ->assertSee('Masuk dulu untuk melihat jajananmu.')
            ->assertDontSee('Riwayat jajananmu');
    }

    public function test_member_only_sees_items_from_their_own_purchase_history(): void
    {
        [$member, $user] = $this->memberAndUser('Pemilik Jajanan');
        [$otherMember] = $this->memberAndUser('Customer Lain');

        $ownItem = $this->orderItemFor($member, 'Album Milik Sendiri');
        $this->orderItemFor($otherMember, 'Photocard Rahasia Orang Lain');

        $this->actingAs($user)
            ->get(route('testimonials.index'))
            ->assertOk()
            ->assertSee($ownItem->item_name)
            ->assertDontSee('Photocard Rahasia Orang Lain');
    }

    public function test_member_cannot_open_another_members_testimonial_form(): void
    {
        [, $user] = $this->memberAndUser('Pemilik Akun');
        [$otherMember] = $this->memberAndUser('Pemilik Pesanan');
        $otherItem = $this->orderItemFor($otherMember, 'Produk Bukan Milikku');

        $this->actingAs($user)
            ->get(route('testimonials.create', $otherItem))
            ->assertForbidden();
    }

    public function test_member_can_submit_one_testimonial_for_an_owned_order_item(): void
    {
        Storage::fake('public');

        [$member, $user] = $this->memberAndUser('Naya Customer');
        $item = $this->orderItemFor($member, 'Album Testimonial');

        $this->actingAs($user)
            ->post(route('testimonials.store', $item), [
                'rating' => 5,
                'content' => 'Paketnya sampai dengan aman dan albumnya cantik sekali.',
                'photo' => UploadedFile::fake()->image('paket.jpg'),
            ])
            ->assertRedirect(route('testimonials.index'));

        $this->assertDatabaseHas('testimonials', [
            'member_id' => $member->id,
            'order_item_id' => $item->id,
            'rating' => 5,
            'is_published' => true,
        ]);

        $testimonial = $item->testimonial()->firstOrFail();
        Storage::disk('public')->assertExists($testimonial->photo_path);

        $this->actingAs($user)
            ->get(route('testimonials.create', $item))
            ->assertRedirect(route('testimonials.index'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Paketnya sampai dengan aman dan albumnya cantik sekali.')
            ->assertSee('Naya Customer');
    }

    private function memberAndUser(string $name): array
    {
        $member = Member::factory()->create(['display_name' => $name]);
        $user = User::factory()->create([
            'member_id' => $member->id,
            'line_user_id' => 'U-'.str()->random(20),
        ]);

        return [$member, $user];
    }

    private function orderItemFor(Member $member, string $itemName): OrderItem
    {
        $order = MemberOrder::factory()->create([
            'member_id' => $member->id,
            'batch_id' => Batch::factory()->create()->id,
        ]);

        return OrderItem::factory()->create([
            'member_order_id' => $order->id,
            'product_id' => null,
            'item_name' => $itemName,
            'variant' => 'Blue ver.',
            'quantity' => 1,
        ]);
    }
}
