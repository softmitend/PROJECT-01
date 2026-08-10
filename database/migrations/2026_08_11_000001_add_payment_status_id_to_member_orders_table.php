<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_orders', function (Blueprint $table) {
            $table->foreignId('payment_status_id')
                ->nullable()
                ->after('override_status_id')
                ->constrained('order_statuses')
                ->nullOnDelete();
        });

        $paymentCodes = ['menunggu-dp', 'menunggu-pelunasan', 'refund'];

        DB::table('order_statuses')
            ->whereIn('code', $paymentCodes)
            ->update(['scope' => 'payment', 'updated_at' => now()]);

        DB::table('order_statuses')
            ->where('code', 'menunggu-dp')
            ->update(['is_initial' => true, 'updated_at' => now()]);

        if (DB::table('order_statuses')->exists() && ! DB::table('order_statuses')->where('code', 'lunas')->exists()) {
            DB::table('order_statuses')->insert([
                'name' => 'Lunas',
                'code' => 'lunas',
                'description' => 'Pembayaran customer telah diterima seluruhnya.',
                'color' => '#059669',
                'sequence' => 90,
                'status_type' => 'success',
                'scope' => 'payment',
                'is_initial' => false,
                'is_final' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $paymentStatusIds = DB::table('order_statuses')
            ->where('scope', 'payment')
            ->pluck('id');

        DB::table('member_orders')
            ->whereIn('override_status_id', $paymentStatusIds)
            ->update(['override_status_id' => null]);

        DB::table('member_orders')
            ->whereNotNull('payment_status')
            ->orderBy('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    $legacyStatus = trim((string) $order->payment_status);
                    $statusId = DB::table('order_statuses')
                        ->where('scope', 'payment')
                        ->where(function ($query) use ($legacyStatus): void {
                            $query->where('name', $legacyStatus)
                                ->orWhere('code', Str::slug($legacyStatus));
                        })
                        ->value('id');

                    if ($statusId) {
                        DB::table('member_orders')
                            ->where('id', $order->id)
                            ->update(['payment_status_id' => $statusId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('member_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_status_id');
        });
    }
};
