<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('override_status_id')->nullable()->constrained('order_statuses')->nullOnDelete();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->string('payment_status')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_orders');
    }
};
