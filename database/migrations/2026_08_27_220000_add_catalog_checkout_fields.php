<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->string('catalog_image_path')->nullable()->after('notes');
            $table->string('qris_image_path')->nullable()->after('catalog_image_path');
            $table->timestamp('ordering_deadline')->nullable()->after('qris_image_path');
            $table->boolean('is_catalog_visible')->default(false)->index()->after('ordering_deadline');
        });

        Schema::table('batch_product', function (Blueprint $table) {
            $table->decimal('dp_price', 14, 2)->nullable();
            $table->decimal('full_price', 14, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_available')->default(true)->index();
        });

        Schema::table('member_orders', function (Blueprint $table) {
            $table->string('order_source', 20)->default('admin')->index()->after('payment_status_id');
            $table->string('payment_type', 20)->nullable()->index()->after('order_source');
            $table->decimal('payment_amount', 14, 2)->nullable()->after('payment_type');
            $table->string('payment_proof_path')->nullable()->after('payment_amount');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('member_orders', function (Blueprint $table) {
            $table->dropIndex(['order_source']);
            $table->dropIndex(['payment_type']);
            $table->dropColumn([
                'order_source',
                'payment_type',
                'payment_amount',
                'payment_proof_path',
                'payment_submitted_at',
            ]);
        });

        Schema::table('batch_product', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
            $table->dropColumn(['dp_price', 'full_price', 'sort_order', 'is_available']);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['is_catalog_visible']);
            $table->dropColumn(['catalog_image_path', 'qris_image_path', 'ordering_deadline', 'is_catalog_visible']);
        });
    }
};
