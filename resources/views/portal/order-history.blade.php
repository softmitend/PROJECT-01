<x-layouts.app title="Riwayat Pesanan — Ocean Paws">
    <main class="page billing-list-page">
        <div class="billing-list-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('services.index') }}" aria-label="Kembali ke layanan"><x-public-icon name="arrow-left" :size="18" /></a>
            </div>

            <header class="billing-list-header">
                <span>DATA MILIKMU</span>
                <h1>Riwayat pesanan</h1>
                <p>Seluruh jajanan, pembayaran, dan perjalanan paketmu tersimpan di sini.</p>
            </header>

            <section class="billing-summary" aria-label="Ringkasan riwayat pesanan">
                <span class="billing-summary-icon"><x-public-icon name="history" :size="23" /></span>
                <div>
                    <small>{{ $filter === 'all' ? 'SEMUA PESANAN' : 'HASIL FILTER' }}</small>
                    <strong>{{ $orders->count() }} dari {{ $allOrderCount }} pesanan</strong>
                </div>
            </section>

            @if($member)
                <nav class="billing-tabs order-history-filters" aria-label="Filter riwayat pesanan">
                    @foreach([
                        'all' => 'Semua',
                        'unpaid' => 'Belum bayar',
                        'active' => 'Aktif',
                        'history' => 'Selesai',
                        'shipping' => 'Dikirim',
                        'refund' => 'Refund',
                    ] as $value => $label)
                        <a class="{{ $filter === $value ? 'active' : '' }}" href="{{ route('orders.history', $value === 'all' ? [] : ['filter' => $value]) }}">{{ $label }}</a>
                    @endforeach
                </nav>
            @endif

            <section class="billing-order-grid">
                @if($user && !$member)
                    <div class="billing-empty"><x-public-icon name="grid" :size="24" /><strong>Ini adalah akun admin.</strong><p>Gunakan akun LINE customer untuk melihat riwayat pesanan pribadi.</p><a href="{{ route('admin.dashboard') }}">Buka dashboard</a></div>
                @elseif(!$member)
                    <div class="billing-empty"><x-public-icon name="bag" :size="24" /><strong>Login untuk melihat riwayatmu.</strong><p>Pesanan dari semua batch akan tersimpan di halaman ini.</p><a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a></div>
                @else
                    @forelse($orders as $order)
                        @php
                            $trackingStatus = $order->tracking_status;
                            $orderTotal = (float) ($order->total_amount ?: $order->items->sum(fn ($item) => (float) ($item->subtotal ?: $item->unit_price * $item->quantity)));
                            $itemCount = (int) $order->items->sum('quantity');
                            $paymentLabel = $order->paymentStatus?->name ?: $order->payment_type_label;
                        @endphp

                        <article class="billing-order-card">
                            <header>
                                <span class="billing-order-thumb">
                                    @if($order->batch->catalog_image_path)<img src="{{ $order->batch->catalog_image_url }}" alt="">@else<x-public-icon name="box" :size="21" />@endif
                                </span>
                                <div class="billing-order-title">
                                    <small>{{ $order->order_code }}</small>
                                    <h2>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h2>
                                    <p>{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                                @if($trackingStatus)<x-status-badge :status="$trackingStatus" />@endif
                            </header>

                            <div class="billing-order-amount">
                                <span>Total pesanan</span>
                                <strong>Rp {{ number_format($orderTotal, 0, ',', '.') }}</strong>
                            </div>

                            <dl class="billing-order-meta">
                                <div><dt>Jumlah item</dt><dd>{{ $itemCount }} item</dd></div>
                                <div><dt>Pembayaran</dt><dd>{{ $paymentLabel }}</dd></div>
                            </dl>

                            <footer>
                                <a href="{{ $order->history_tracking_url }}">Lihat rincian <x-public-icon name="arrow-right" :size="13" /></a>
                            </footer>
                        </article>
                    @empty
                        <div class="billing-empty"><x-public-icon name="bag" :size="24" /><strong>Tidak ada pesanan di kategori ini.</strong><p>Coba pilih kategori lain atau mulai jajan dari katalog.</p><a href="{{ route('catalog.index') }}">Lihat katalog</a></div>
                    @endforelse
                @endif
            </section>
        </div>
    </main>
</x-layouts.app>
