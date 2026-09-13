<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Alias lama untuk kompatibilitas dengan perintah seeding sebelumnya.
 * Seluruh data demo sekarang menggunakan skema K-pop merch terbaru.
 */
class DemoOrderRecapSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            OrderStatusSeeder::class,
            GoKpopMerchSeeder::class,
        ]);
    }
}
