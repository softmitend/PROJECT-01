<x-layouts.app title="Lacak Pesanan — Ocean Paws">
    <main class="tracking-search-page ocean-home ocean-editorial">
        <div class="tracking-search-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('services.index') }}" aria-label="Kembali ke layanan"><x-public-icon name="arrow-left" :size="18" /></a>
                <strong>Lacak Pesanan</strong>
            </div>

            @include('tracking.partials.search-panel')
        </div>
    </main>
</x-layouts.app>
