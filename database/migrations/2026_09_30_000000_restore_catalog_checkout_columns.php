<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Commit "remove katalog" (72d1336) deleted the migrations that created these
 * columns, but the models, factories, seeders, views and tests still use them.
 * On a fresh database (sqlite for tests, or any new deploy) the schema is missing
 * them and inserts fail. This re-adds them idempotently: it is a no-op on the
 * existing production database (columns already present) and additive elsewhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('member_orders')) {
            Schema::table('member_orders', function (Blueprint $table) {
                if (! Schema::hasColumn('member_orders', 'order_source')) {
                    $table->string('order_source', 20)->default('admin')->index();
                }
                if (! Schema::hasColumn('member_orders', 'payment_type')) {
                    $table->string('payment_type', 20)->nullable()->index();
                }
                if (! Schema::hasColumn('member_orders', 'payment_amount')) {
                    $table->decimal('payment_amount', 14, 2)->nullable();
                }
                if (! Schema::hasColumn('member_orders', 'payment_proof_path')) {
                    $table->string('payment_proof_path')->nullable();
                }
                if (! Schema::hasColumn('member_orders', 'payment_submitted_at')) {
                    $table->timestamp('payment_submitted_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                if (! Schema::hasColumn('batches', 'catalog_image_path')) {
                    $table->string('catalog_image_path')->nullable();
                }
                if (! Schema::hasColumn('batches', 'catalog_image_disk')) {
                    $table->string('catalog_image_disk', 40)->nullable();
                }
                if (! Schema::hasColumn('batches', 'qris_image_path')) {
                    $table->string('qris_image_path')->nullable();
                }
                if (! Schema::hasColumn('batches', 'ordering_deadline')) {
                    $table->timestamp('ordering_deadline')->nullable();
                }
                if (! Schema::hasColumn('batches', 'is_catalog_visible')) {
                    $table->boolean('is_catalog_visible')->default(false)->index();
                }
            });
        }

        if (Schema::hasTable('batch_product')) {
            Schema::table('batch_product', function (Blueprint $table) {
                if (! Schema::hasColumn('batch_product', 'dp_price')) {
                    $table->decimal('dp_price', 14, 2)->nullable();
                }
                if (! Schema::hasColumn('batch_product', 'full_price')) {
                    $table->decimal('full_price', 14, 2)->nullable();
                }
                if (! Schema::hasColumn('batch_product', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0);
                }
                if (! Schema::hasColumn('batch_product', 'is_available')) {
                    $table->boolean('is_available')->default(true)->index();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('batch_product')) {
            Schema::table('batch_product', function (Blueprint $table) {
                foreach (['dp_price', 'full_price', 'sort_order', 'is_available'] as $column) {
                    if (Schema::hasColumn('batch_product', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                foreach (['catalog_image_path', 'catalog_image_disk', 'qris_image_path', 'ordering_deadline', 'is_catalog_visible'] as $column) {
                    if (Schema::hasColumn('batches', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('member_orders')) {
            Schema::table('member_orders', function (Blueprint $table) {
                foreach (['order_source', 'payment_type', 'payment_amount', 'payment_proof_path', 'payment_submitted_at'] as $column) {
                    if (Schema::hasColumn('member_orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
