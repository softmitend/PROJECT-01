<x-layouts.app title="Lacak Pesanan — Ocean Paws">
    <main class="tracking-search-page ocean-home ocean-editorial{{ isset($orderResult) || isset($memberResult) ? ' has-result' : '' }}">
        <div class="tracking-search-shell">
            @include('tracking.partials.search-panel')
        </div>
    </main>
</x-layouts.app>
