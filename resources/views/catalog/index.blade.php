<x-layouts.app title="Katalog Group Order — Ocean Paws">
    <main class="page catalog-listing-page">
        <div class="catalog-listing-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('services.index') }}" aria-label="Kembali ke layanan"><x-public-icon name="arrow-left" :size="18" /></a>
                <strong>Pre-Order</strong>
            </div>

            <header class="catalog-listing-heading">
                <div>
                    <span>PILIHAN BATCH PRE-ORDER</span>
                    <h1>Pilih batch yang kamu mau.</h1>
                    <span class="sr-only">Pilih batch yang sedang dibuka.</span>
                </div>
                <p>Cek jadwal penutupan dan variasi yang tersedia, lalu pilih batch untuk melanjutkan pesananmu.</p>
            </header>

            <section class="preorder-grid" aria-label="Daftar batch Group Order">
                @forelse($catalogBatches as $catalogBatch)
                    <a href="{{ route('catalog.show', $catalogBatch) }}" class="po-card">
                        <div class="po-thumb">
                            @if($catalogBatch->catalog_image_path)
                                <img src="{{ $catalogBatch->catalog_image_url }}" alt="{{ $catalogBatch->batch_name ?: $catalogBatch->batch_number }}">
                            @else
                                <x-public-icon name="document" :size="20" /><span class="mt-2 block">Foto menyusul</span>
                            @endif
                            <span class="po-state {{ $catalogBatch->catalog_is_open ? 'is-open' : 'is-closed' }}">{{ $catalogBatch->catalog_is_open ? 'Open' : 'Closed' }}</span>
                        </div>
                        <div class="po-meta">
                            <h2>{{ $catalogBatch->batch_name ?: $catalogBatch->batch_number }}</h2>
                            <small>Close: {{ $catalogBatch->ordering_deadline?->translatedFormat('d F Y') ?: 'TBA' }}</small>
                            <span>{{ $catalogBatch->products->count() }} variasi</span>
                        </div>
                    </a>
                @empty
                    <div class="preorder-empty">
                        <span class="icon-chip"><x-public-icon name="bag" :size="20" /></span>
                        <strong>Belum ada batch yang dibuka.</strong>
                        <p>Katalog pre-order berikutnya akan muncul di sini.</p>
                    </div>
                @endforelse
            </section>

            @if($catalogBatches->hasPages())
                <div class="preorder-pagination">{{ $catalogBatches->links() }}</div>
            @endif
        </div>
    </main>
</x-layouts.app>
