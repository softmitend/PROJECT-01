<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_profile_keeps_the_customer_layout_with_an_empty_avatar_and_login_label(): void
    {
        $this->get(route('profile.show'))
            ->assertOk()
            ->assertSee('profile-photo is-empty', false)
            ->assertSee('>Login</strong>', false)
            ->assertSee('MASUK DENGAN LINE')
            ->assertDontSee('DATA MILIKMU')
            ->assertDontSee('data-profile-settings-button', false)
            ->assertSee(route('line-auth.redirect'), false);
    }

    public function test_order_list_is_removed_from_profile_and_available_on_the_private_history_page(): void
    {
        $member = Member::factory()->create(['display_name' => 'Caca Member']);
        $otherMember = Member::factory()->create(['display_name' => 'Member Lain']);
        $user = User::factory()->create([
            'role' => 'member',
            'member_id' => $member->id,
            'line_user_id' => 'U-profile-owner',
        ]);

        foreach (range(1, 6) as $index) {
            $order = MemberOrder::factory()->create([
                'member_id' => $member->id,
                'batch_id' => Batch::factory()->create([
                    'batch_name' => 'Batch Milik Caca '.$index,
                ])->id,
                'order_code' => 'OWN-ORDER-'.$index,
                'payment_type' => 'dp',
                'payment_amount' => 100000,
                'total_amount' => 200000,
            ]);

            OrderItem::factory()->create([
                'member_order_id' => $order->id,
                'item_name' => 'Album Caca '.$index,
                'variant' => 'Versi '.$index,
                'quantity' => 1,
                'unit_price' => 200000,
                'subtotal' => 200000,
            ]);
        }

        MemberOrder::factory()->create([
            'member_id' => $otherMember->id,
            'batch_id' => Batch::factory()->create(['batch_name' => 'Batch Rahasia Member Lain'])->id,
            'order_code' => 'OTHER-PRIVATE-ORDER',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Caca Member')
            ->assertSee('data-profile-settings-button', false)
            ->assertSee('data-profile-language-switch', false)
            ->assertSee('data-profile-theme-switch', false)
            ->assertSee('Keluar dari akun')
            ->assertSee(route('orders.history'), false)
            ->assertDontSee('DATA MILIKMU')
            ->assertDontSee('OWN-ORDER-1')
            ->assertDontSee('Album Caca 6');

        $this->actingAs($user)
            ->get(route('orders.history'))
            ->assertOk()
            ->assertSee('Riwayat pesanan')
            ->assertSee('OWN-ORDER-1')
            ->assertSee('OWN-ORDER-6')
            ->assertSee('Album Caca 6')
            ->assertSee('Versi 6')
            ->assertDontSee('OTHER-PRIVATE-ORDER')
            ->assertDontSee('Batch Rahasia Member Lain');
    }

    public function test_member_can_only_open_their_own_payment_proof(): void
    {
        Storage::fake('local');

        $member = Member::factory()->create();
        $otherMember = Member::factory()->create();
        $user = User::factory()->create(['role' => 'member', 'member_id' => $member->id]);

        $ownOrder = MemberOrder::factory()->create([
            'member_id' => $member->id,
            'payment_proof_path' => 'catalog/payment-proofs/own.jpg',
        ]);
        $otherOrder = MemberOrder::factory()->create([
            'member_id' => $otherMember->id,
            'payment_proof_path' => 'catalog/payment-proofs/other.jpg',
        ]);

        Storage::disk('local')->put($ownOrder->payment_proof_path, 'own-proof');
        Storage::disk('local')->put($otherOrder->payment_proof_path, 'other-proof');

        $this->actingAs($user)
            ->get(route('profile.orders.payment-proof', $ownOrder))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('profile.orders.payment-proof', $otherOrder))
            ->assertForbidden();
    }
}
