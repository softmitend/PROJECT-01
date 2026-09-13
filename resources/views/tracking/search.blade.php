<x-layouts.app title="Lacak Pesanan — Ocean Paws">
    <main class="tracking-search-page ocean-home ocean-editorial">
        <div class="tracking-search-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('home') }}" aria-label="Kembali ke home"><x-public-icon name="arrow-left" :size="18" /></a>
            </div>

            @include('tracking.partials.search-panel')
        </div>
    </main>
</x-layouts.app>
