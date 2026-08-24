<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('order_statuses')->where('code', 'refunded')->exists()) {
            return;
        }

        DB::table('order_statuses')->insert([
            'name' => 'Refunded',
            'code' => 'refunded',
            'description' => 'Progress pesanan dihentikan permanen karena dana telah diproses sebagai refund.',
            'color' => '#c2410c',
            'sequence' => 96,
            'status_type' => 'failed',
            'scope' => 'member_order',
            'is_initial' => false,
            'is_final' => true,
            'is_active' => true,
            'locks_order_editing' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $statusId = DB::table('order_statuses')->where('code', 'refunded')->value('id');

        if (! $statusId) {
            return;
        }

        $isUsed = DB::table('member_orders')->where('override_status_id', $statusId)->exists()
            || DB::table('order_items')->where('override_status_id', $statusId)->exists()
            || DB::table('status_histories')->where('old_status_id', $statusId)->orWhere('new_status_id', $statusId)->exists();

        if (! $isUsed) {
            DB::table('order_statuses')->where('id', $statusId)->delete();
        }
    }
};
