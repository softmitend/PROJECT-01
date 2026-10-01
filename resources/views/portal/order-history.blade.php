<x-layouts.app title="Riwayat Pesanan — Ocean Paws">
    <div class="portal-v2 portal-orders-v2">
        <div class="portal-v2-shell">
            <a class="portal-v2-back" href="{{ route('orders.index') }}" aria-label="Kembali ke pusat pesanan">
                <x-public-icon name="arrow-left" :size="18" />
                <span>Kembali</span>
            </a>

            <section class="portal-v2-hero portal-v2-hero-orders">
                <div class="portal-v2-hero-copy">
                    <span class="portal-v2-kicker">DATA MILIKMU</span>
                    <h1>Riwayat<br>pesanan</h1>
                    <p>Semua jajanan, pembayaran, dan perjalanan paketmu dirapikan dalam satu tempat.</p>
                </div>
                <div class="portal-v2-hero-stat">
                    <span class="portal-v2-stat-icon"><x-public-icon name="history" :size="26" /></span>
                    <small>{{ $filter === 'all' ? 'SEMUA PESANAN' : 'HASIL FILTER' }}</small>
                    <strong>{{ $orders->count() }}</strong>
                    <p>dari {{ $allOrderCount }} pesanan</p>
                </div>
            </section>

            <div class="portal-v2-layout">
                @if($member)
                    <aside class="portal-v2-filter-panel">
                        <div class="portal-v2-filter-head">
                            <span>FILTER</span>
                            <small>Pilih status pesanan</small>
                        </div>
                        <nav class="portal-v2-filters" aria-label="Filter riwayat pesanan">
                            @foreach([
                                'all' => ['Semua','bag'],
                                'unpaid' => ['Belum bayar','wallet'],
                                'active' => ['Aktif','sparkle'],
                                'history' => ['Selesai','check'],
                                'shipping' => ['Dikirim','truck'],
                                'refund' => ['Refund','history'],
                            ] as $value => [$label,$icon])
                                <a class="{{ $filter === $value ? 'active' : '' }}" href="{{ route('orders.history', $value === 'all' ? [] : ['filter' => $value]) }}">
                                    <x-public-icon :name="$icon" :size="16" />
                                    <span>{{ $label }}</span>
                                    <x-public-icon name="chevron-right" :size="13" />
                                </a>
                            @endforeach
                        </nav>
                    </aside>
                @endif

                <section class="portal-v2-content {{ !$member ? 'is-full' : '' }}">
                    @if($user && !$member)
                        <div class="portal-v2-empty">
                            <span class="portal-v2-empty-icon"><x-public-icon name="grid" :size="32" /></span>
                            <small>AKUN ADMIN</small>
                            <h2>Riwayat customer tidak tampil di sini.</h2>
                            <p>Gunakan akun LINE customer untuk melihat riwayat pesanan pribadi.</p>
                            <a href="{{ route('admin.dashboard') }}">Buka dashboard</a>
                        </div>
                    @elseif(!$member)
                        <div class="portal-v2-empty">
                            <span class="portal-v2-empty-icon"><x-public-icon name="bag" :size="32" /></span>
                            <small>RIWAYAT PRIBADI</small>
                            <h2>Login untuk membuka pesananmu.</h2>
                            <p>Pesanan dari semua batch akan otomatis terkumpul setelah akun LINE terhubung.</p>
                            <a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a>
                        </div>
                    @else
                        <div class="portal-v2-section-head">
                            <div><span>DAFTAR PESANAN</span><h2>{{ $filter === 'all' ? 'Semua pesananmu' : 'Pesanan terfilter' }}</h2></div>
                            <strong>{{ $orders->count() }} data</strong>
                        </div>

                        <div class="portal-v2-order-grid">
                            @forelse($orders as $order)
                                @php
                                    $trackingStatus = $order->tracking_status;
                                    $orderTotal = (float) ($order->total_amount ?: $order->items->sum(fn ($item) => (float) ($item->subtotal ?: $item->unit_price * $item->quantity)));
                                    $itemCount = (int) $order->items->sum('quantity');
                                    $paymentLabel = $order->paymentStatus?->name ?: $order->payment_type_label;
                                @endphp
                                <article class="portal-v2-order-card">
                                    <div class="portal-v2-card-top">
                                        <span class="portal-v2-thumb">
                                            @if($order->batch->catalog_image_path)<img src="{{ $order->batch->catalog_image_url }}" alt="">@else<x-public-icon name="box" :size="24" />@endif
                                        </span>
                                        <div class="portal-v2-card-title">
                                            <small>{{ $order->order_code }}</small>
                                            <h3>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h3>
                                            <p>{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                        </div>
                                        @if($trackingStatus)<x-status-badge :status="$trackingStatus" />@endif
                                    </div>

                                    <div class="portal-v2-price-box">
                                        <span>Total pesanan</span>
                                        <strong>Rp {{ number_format($orderTotal, 0, ',', '.') }}</strong>
                                    </div>

                                    <div class="portal-v2-meta-grid">
                                        <div><span>Jumlah item</span><strong>{{ $itemCount }} item</strong></div>
                                        <div><span>Pembayaran</span><strong>{{ $paymentLabel }}</strong></div>
                                    </div>

                                    <a class="portal-v2-card-action" href="{{ $order->history_tracking_url }}">Lihat rincian <x-public-icon name="arrow-right" :size="14" /></a>
                                </article>
                            @empty
                                <div class="portal-v2-empty portal-v2-empty-inline">
                                    <span class="portal-v2-empty-icon"><x-public-icon name="bag" :size="32" /></span>
                                    <small>KOSONG</small>
                                    <h2>Tidak ada pesanan di kategori ini.</h2>
                                    <p>Coba kategori lain atau kembali ke pusat pesanan.</p>
                                    <a href="{{ route('orders.index') }}">Kembali ke Pesanan</a>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
