<?php

namespace Tests\Feature;

use Database\Seeders\CustomerCatalogSeeder;
use Database\Seeders\CustomerMemberSeeder;
use Database\Seeders\CustomerOrderSeeder;
use Database\Seeders\OrderStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_demo_seeders_are_repeatable_and_cover_public_views(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $seeders = [
            OrderStatusSeeder::class,
            CustomerCatalogSeeder::class,
            CustomerMemberSeeder::class,
            CustomerOrderSeeder::class,
        ];

        foreach (range(1, 2) as $_run) {
            foreach ($seeders as $seeder) {
                $this->seed($seeder);
            }
        }

        $this->assertDatabaseCount('batches', 6);
        $this->assertDatabaseCount('member_orders', 6);
        $this->assertDatabaseHas('members', [
            'member_code' => 'OP-CUSTOMER-DEMO',
            'username' => 'oceanpaws.demo',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('member_orders', [
            'order_code' => 'ORD-DEMO-003',
            'ems_tax_status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('member_orders', [
            'order_code' => 'ORD-DEMO-006',
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('LE SSERAFIM — Made My Night');

        $this->post(route('tracking.search'), ['query' => 'ORD-DEMO-003'])
            ->assertOk()
            ->assertSee('ORD-DEMO-003')
            ->assertSee('Caca Ocean');

        $this->post(route('tracking.search'), ['query' => 'oceanpaws.demo'])
            ->assertOk()
            ->assertSee('Caca Ocean');
    }
}
