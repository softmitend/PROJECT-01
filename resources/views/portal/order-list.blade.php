<x-layouts.app :title="$title.' — Ocean Paws'">
    <main class="page billing-list-page">
        <div class="billing-list-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('profile.show') }}" aria-label="Kembali ke profil"><x-public-icon name="arrow-left" :size="18" /></a>
            </div>

            <header class="billing-list-header">
                <span>{{ $eyebrow }}</span>
                <h1>{{ $title }}</h1>
                <p>{{ $description }}</p>
            </header>

            <section class="billing-summary" aria-label="Ringkasan {{ strtolower($title) }}">
                <span class="billing-summary-icon"><x-public-icon :name="$icon" :size="23" /></span>
                <div>
                    <small>TOTAL DATA</small>
                    <strong>{{ $orders->count() }} pesanan</strong>
                </div>
            </section>

            <section class="billing-order-grid">
                @if($user && !$member)
                    <div class="billing-empty">
                        <x-public-icon name="grid" :size="24" />
                        <strong>Ini adalah akun admin.</strong>
                        <p>Gunakan akun LINE customer untuk membuka data pesanan pribadi.</p>
                        <a href="{{ route('admin.dashboard') }}">Buka dashboard</a>
                    </div>
                @elseif(!$member)
                    <div class="billing-empty">
                        <x-public-icon :name="$icon" :size="24" />
                        <strong>Login untuk melihat data pesananmu.</strong>
                        <p>Halaman ini hanya menampilkan pesanan yang terhubung dengan akun LINE milikmu.</p>
                        <a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a>
                    </div>
                @else
                    @forelse($orders as $order)
                        @php
                            $trackingStatus = $order->tracking_status;
                            $orderTotal = (float) ($order->total_amount ?: $order->items->sum(fn ($item) => (float) ($item->subtotal ?: $item->unit_price * $item->quantity)));
                            $paidAmount = (float) ($order->payment_amount ?: 0);
                            $remainingAmount = max($orderTotal - $paidAmount, 0);
                            $itemCount = (int) $order->items->sum('quantity');
                        @endphp

                        <article class="billing-order-card">
                            <header>
                                <span class="billing-order-thumb">
                                    @if($order->batch->catalog_image_path)
                                        <img src="{{ $order->batch->catalog_image_url }}" alt="">
                                    @else
                                        <x-public-icon name="box" :size="21" />
                                    @endif
                                </span>
                                <div class="billing-order-title">
                                    <small>{{ $order->order_code }}</small>
                                    <h2>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h2>
                                    <p>{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                                @if($trackingStatus)<x-status-badge :status="$trackingStatus" />@endif
                            </header>

                            @if($scope === 'unpaid')
                                <div class="billing-order-amount">
                                    <span>Sisa yang perlu dibayar</span>
                                    <strong>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong>
                                </div>
                            @else
                                <div class="billing-order-amount">
                                    <span>Total pesanan</span>
                                    <strong>Rp {{ number_format($orderTotal, 0, ',', '.') }}</strong>
                                </div>
                            @endif

                            <dl class="billing-order-meta">
                                <div><dt>Jumlah item</dt><dd>{{ $itemCount }} item</dd></div>
                                @if($scope === 'shipping')
                                    <div><dt>Status pengiriman</dt><dd>{{ $trackingStatus?->name ?: 'Dalam pengiriman' }}</dd></div>
                                @elseif($scope === 'refund')
                                    <div><dt>Status</dt><dd>Refund</dd></div>
                                @else
                                    <div><dt>Pembayaran</dt><dd>{{ $order->paymentStatus?->name ?: $order->payment_type_label }}</dd></div>
                                @endif
                            </dl>

                            <footer>
                                <a href="{{ $order->portal_tracking_url }}">
                                    {{ $scope === 'shipping' ? 'Lihat tracking' : 'Lihat rincian' }}
                                    <x-public-icon name="arrow-right" :size="13" />
                                </a>
                                @if($scope === 'unpaid')
                                    <a class="billing-proof-link" href="{{ route('billing.orders') }}">Buka tagihan</a>
                                @endif
                            </footer>
                        </article>
                    @empty
                        <div class="billing-empty">
                            <x-public-icon :name="$icon" :size="24" />
                            <strong>{{ $emptyTitle }}</strong>
                            <p>{{ $emptyDescription }}</p>
                            <a href="{{ route('orders.history') }}">Lihat semua pesanan</a>
                        </div>
                    @endforelse
                @endif
            </section>
        </div>
    </main>
</x-layouts.app>
