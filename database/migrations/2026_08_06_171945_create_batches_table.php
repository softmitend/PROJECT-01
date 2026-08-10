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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('batch_name')->nullable();
            $table->unsignedBigInteger('current_status_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_archived')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
