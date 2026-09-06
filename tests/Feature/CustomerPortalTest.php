<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    public function test_billing_overview_is_available_to_guests(): void
    {
        $this->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Tagihan &amp; Pembayaran', false)
            ->assertSee('Login untuk melihat')
            ->assertSee(route('billing.orders'), false)
            ->assertSee(route('billing.ems'), false)
            ->assertDontSee('>Riwayat Pembayaran</h2>', false);
    }

    public function test_services_overview_links_to_available_customer_features(): void
    {
        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('Layanan Pesanan')
            ->assertSee('Katalog')
            ->assertSee(route('catalog.index'), false)
            ->assertSee(route('tracking.index'), false)
            ->assertDontSee('>Order</span>', false)
            ->assertSee(route('orders.history'), false);
    }

    public function test_tracking_search_is_separate_from_the_landing_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="tracking-query"', false)
            ->assertSee(route('tracking.index'), false);

        $this->get(route('tracking.index'))
            ->assertOk()
            ->assertSee('Lacak pesananmu.')
            ->assertSee('TRACK YOUR ORDER')
            ->assertSee('Belum ada pesanan yang dicari.')
            ->assertDontSee('tracking-trust-section', false)
            ->assertSee('id="tracking-query"', false)
            ->assertDontSee('Group order untuk girl group');
    }
}
