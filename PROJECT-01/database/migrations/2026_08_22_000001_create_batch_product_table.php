<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_product', function (Blueprint $table) {
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['batch_id', 'product_id']);
        });

        DB::table('order_items')
            ->join('member_orders', 'member_orders.id', '=', 'order_items.member_order_id')
            ->whereNotNull('order_items.product_id')
            ->select('member_orders.batch_id', 'order_items.product_id')
            ->distinct()
            ->orderBy('member_orders.batch_id')
            ->each(fn ($item) => DB::table('batch_product')->insertOrIgnore([
                'batch_id' => $item->batch_id,
                'product_id' => $item->product_id,
            ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_product');
    }
};
