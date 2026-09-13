<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::table('member_orders', function (Blueprint $table) {
            $table->decimal('ems_tax_amount', 14, 2)->nullable()->after('total_amount');
            $table->string('ems_tax_status', 20)->default('not_billed')->after('ems_tax_amount');
            $table->date('ems_tax_due_date')->nullable()->after('ems_tax_status');
            $table->text('ems_tax_notes')->nullable()->after('ems_tax_due_date');
            $table->index('ems_tax_status');
        });
    }

    public function down(): void
    {
        Schema::table('member_orders', function (Blueprint $table) {
            $table->dropIndex(['ems_tax_status']);
            $table->dropColumn(['ems_tax_amount', 'ems_tax_status', 'ems_tax_due_date', 'ems_tax_notes']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });
    }
};
