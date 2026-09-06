<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('line_user_id', 64)->nullable()->unique()->after('username');
            $table->text('avatar_url')->nullable()->after('line_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('line_user_id', 64)->nullable()->unique()->after('email');
            $table->text('avatar_url')->nullable()->after('line_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropUnique(['line_user_id']);
            $table->dropColumn(['member_id', 'line_user_id', 'avatar_url']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['line_user_id']);
            $table->dropColumn(['line_user_id', 'avatar_url']);
        });
    }
};
