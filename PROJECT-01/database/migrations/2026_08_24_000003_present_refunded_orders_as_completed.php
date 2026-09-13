<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_statuses')
            ->where('code', 'refunded')
            ->update([
                'name' => 'Selesai (Refund)',
                'description' => 'Pesanan selesai dan seluruh progress ditutup karena dana telah dikembalikan.',
                'is_final' => true,
                'locks_order_editing' => true,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('order_statuses')
            ->where('code', 'refunded')
            ->update([
                'name' => 'Refunded',
                'description' => 'Progress pesanan dihentikan permanen karena dana telah diproses sebagai refund.',
                'updated_at' => now(),
            ]);
    }
};
