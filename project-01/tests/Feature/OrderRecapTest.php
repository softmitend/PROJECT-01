<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\OrderStatus;
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
