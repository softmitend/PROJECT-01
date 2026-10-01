<x-layouts.app :title="$title.' — Ocean Paws'">
    <div class="portal-v2 portal-billing-v2">
        <div class="portal-v2-shell">
            <a class="portal-v2-back" href="{{ route('orders.index') }}" aria-label="Kembali ke pusat pesanan">
                <x-public-icon name="arrow-left" :size="18" />
                <span>Kembali</span>
            </a>

            <section class="portal-v2-hero portal-v2-hero-billing">
                <div class="portal-v2-hero-copy">
                    <span class="portal-v2-kicker">{{ $scope === 'ems' ? 'EMS & PAJAK' : 'PEMBAYARAN PESANAN' }}</span>
                    <h1>{{ $title }}</h1>
                    <p>{{ $description }}</p>
                </div>
                <div class="portal-v2-hero-stat">
                    <span class="portal-v2-stat-icon"><x-public-icon :name="$scope === 'ems' ? 'document' : 'card'" :size="26" /></span>
                    <small>{{ $tab === 'paid' ? 'TRANSAKSI BERHASIL' : ($tab === 'refund' ? 'TRANSAKSI REFUND' : 'PERLU DISELESAIKAN') }}</small>
                    <strong>{{ $tab === 'paid' ? $paidCount : ($tab === 'refund' ? $refundCount : $unpaidCount) }}</strong>
                    <p>{{ $scope === 'ems' ? 'tagihan' : 'transaksi' }}</p>
                </div>
            </section>

            <div class="portal-v2-layout">
                @if($member)
                    <aside class="portal-v2-filter-panel">
                        <div class="portal-v2-filter-head">
                            <span>STATUS</span>
                            <small>Pilih kategori tagihan</small>
                        </div>
                        <nav class="portal-v2-filters" aria-label="Daftar pembayaran">
                            <a class="{{ $tab === 'unpaid' ? 'active' : '' }}" href="{{ request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders') }}">
                                <x-public-icon name="wallet" :size="16" /><span>Belum dibayar</span><b>{{ $unpaidCount }}</b>
                            </a>
                            <a class="{{ $tab === 'paid' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=paid' }}">
                                <x-public-icon name="check" :size="16" /><span>Berhasil</span><b>{{ $paidCount }}</b>
                            </a>
                            <a class="{{ $tab === 'refund' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=refund' }}">
                                <x-public-icon name="history" :size="16" /><span>Refund</span><b>{{ $refundCount }}</b>
                            </a>
                        </nav>
                    </aside>
                @endif

                <section class="portal-v2-content {{ !$member ? 'is-full' : '' }}">
                    @if($user && !$member)
                        <div class="portal-v2-empty"><span class="portal-v2-empty-icon"><x-public-icon name="grid" :size="32" /></span><small>AKUN ADMIN</small><h2>Tagihan customer tidak tersedia di akun admin.</h2><p>Gunakan akun LINE customer untuk membuka data pembayaran pribadi.</p><a href="{{ route('admin.dashboard') }}">Buka dashboard</a></div>
                    @elseif(!$member)
                        <div class="portal-v2-empty"><span class="portal-v2-empty-icon"><x-public-icon name="wallet" :size="32" /></span><small>TAGIHAN PRIBADI</small><h2>Login untuk melihat tagihanmu.</h2><p>Daftar pembayaran akan otomatis tampil sesuai pesanan yang terhubung ke akun LINE.</p><a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a></div>
                    @else
                        <div class="portal-v2-section-head">
                            <div><span>{{ $scope === 'ems' ? 'EMS & PAJAK' : 'TRANSAKSI' }}</span><h2>{{ $tab === 'paid' ? 'Pembayaran selesai' : ($tab === 'refund' ? 'Riwayat refund' : 'Perlu dibayar') }}</h2></div>
                            <strong>{{ $orders->count() }} data</strong>
                        </div>

                        <div class="portal-v2-order-grid">
                            @forelse($orders as $order)
                                @php
                                    $orderTotal = (float) ($order->total_amount ?: $order->items->sum(fn ($item) => (float) ($item->subtotal ?: $item->unit_price * $item->quantity)));
                                    $paidAmount = (float) ($order->payment_amount ?: 0);
                                    $shownAmount = $scope === 'ems'
                                        ? (float) $order->ems_tax_amount
                                        : ($tab === 'paid' ? $paidAmount : ($tab === 'refund' ? $paidAmount : max($orderTotal - $paidAmount, 0)));
                                    $amountLabel = $scope === 'ems'
                                        ? 'Tagihan EMS & pajak'
                                        : ($tab === 'paid' ? 'Nominal pembayaran' : ($tab === 'refund' ? 'Nominal refund' : 'Sisa yang perlu dibayar'));
                                @endphp
                                <article class="portal-v2-order-card">
                                    <div class="portal-v2-card-top">
                                        <span class="portal-v2-thumb">@if($order->batch->catalog_image_path)<img src="{{ $order->batch->catalog_image_url }}" alt="">@else<x-public-icon name="box" :size="24" />@endif</span>
                                        <div class="portal-v2-card-title"><small>{{ $order->order_code }}</small><h3>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h3><p>{{ $order->created_at->translatedFormat('d M Y') }}</p></div>
                                        @if($scope === 'orders' && $order->paymentStatus)
                                            <x-status-badge :status="$order->paymentStatus" />
                                        @else
                                            <span class="portal-v2-state {{ $order->ems_tax_status === 'paid' ? 'is-paid' : '' }}">{{ $order->ems_tax_status_label }}</span>
                                        @endif
                                    </div>

                                    <div class="portal-v2-price-box"><span>{{ $amountLabel }}</span><strong>Rp {{ number_format($shownAmount, 0, ',', '.') }}</strong></div>

                                    <div class="portal-v2-meta-grid">
                                        @if($scope === 'orders')
                                            <div><span>Jenis pembayaran</span><strong>{{ $order->payment_type_label }}</strong></div>
                                            <div><span>{{ $tab === 'paid' ? 'Dibayar pada' : 'Status' }}</span><strong>{{ $tab === 'paid' ? ($order->payment_submitted_at?->translatedFormat('d M Y, H:i') ?: 'Pembayaran berhasil') : ($tab === 'refund' ? 'Direfund' : ($order->paymentStatus?->name ?: 'Menunggu pembayaran')) }}</strong></div>
                                        @else
                                            <div><span>Jatuh tempo</span><strong>{{ $order->ems_tax_due_date?->translatedFormat('d M Y') ?: 'Belum ditentukan' }}</strong></div>
                                            <div><span>Status</span><strong>{{ $order->ems_tax_status_label }}</strong></div>
                                        @endif
                                    </div>

                                    <div class="portal-v2-card-actions">
                                        <a class="portal-v2-card-action" href="{{ $order->billing_tracking_url }}">Lihat rincian <x-public-icon name="arrow-right" :size="14" /></a>
                                        @if($scope === 'orders' && $order->payment_proof_path)
                                            <a class="portal-v2-card-action is-secondary" href="{{ route('profile.orders.payment-proof', $order) }}" target="_blank" rel="noopener">Bukti bayar</a>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <div class="portal-v2-empty portal-v2-empty-inline"><span class="portal-v2-empty-icon"><x-public-icon name="card" :size="32" /></span><small>KOSONG</small><h2>Tidak ada transaksi di kategori ini.</h2><p>Data pembayaran akan otomatis tampil ketika tersedia.</p><a href="{{ route('orders.index') }}">Kembali ke Pesanan</a></div>
                            @endforelse
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
