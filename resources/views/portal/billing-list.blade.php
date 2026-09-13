<x-layouts.app :title="$title.' — Ocean Paws'">
    <main class="page billing-list-page">
        <div class="billing-list-shell">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('orders.index') }}" aria-label="Kembali ke pusat pesanan"><x-public-icon name="arrow-left" :size="18" /></a>
            </div>

            <header class="billing-list-header">
                <span>{{ $scope === 'ems' ? 'EMS & PAJAK' : 'PEMBAYARAN PESANAN' }}</span>
                <h1>{{ $title }}</h1>
                <p>{{ $description }}</p>
            </header>

            <section class="billing-summary" aria-label="Ringkasan tagihan">
                <span class="billing-summary-icon"><x-public-icon :name="$scope === 'ems' ? 'document' : 'card'" :size="23" /></span>
                <div>
                    <small>{{ $tab === 'paid' ? 'TRANSAKSI BERHASIL' : ($tab === 'refund' ? 'TRANSAKSI REFUND' : 'PERLU DISELESAIKAN') }}</small>
                    <strong>{{ $tab === 'paid' ? $paidCount : ($tab === 'refund' ? $refundCount : $unpaidCount) }} {{ $scope === 'ems' ? 'tagihan' : 'transaksi' }}</strong>
                </div>
            </section>

            @if($member)
                <nav class="billing-tabs" aria-label="Daftar pembayaran">
                    <a class="{{ $tab === 'unpaid' ? 'active' : '' }}" href="{{ request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders') }}">
                        <span>Belum dibayar</span><b>{{ $unpaidCount }}</b>
                    </a>
                    <a class="{{ $tab === 'paid' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=paid' }}">
                        <span>Berhasil</span><b>{{ $paidCount }}</b>
                    </a>
                    <a class="{{ $tab === 'refund' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=refund' }}">
                        <span>Refund</span><b>{{ $refundCount }}</b>
                    </a>
                </nav>
            @endif

            <section class="billing-order-grid">
                @if($user && !$member)
                    <div class="billing-empty"><x-public-icon name="grid" :size="24" /><strong>Tagihan customer tidak tersedia di akun admin.</strong><p>Gunakan akun LINE customer untuk membuka data pembayaran pribadi.</p><a href="{{ route('admin.dashboard') }}">Buka dashboard</a></div>
                @elseif(!$member)
                    <div class="billing-empty"><x-public-icon name="wallet" :size="24" /><strong>Login untuk melihat tagihanmu.</strong><p>Daftar pembayaran akan tampil otomatis sesuai pesanan yang terhubung ke akun LINE.</p><a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a></div>
                @else
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
                                @if($scope === 'orders' && $order->paymentStatus)
                                    <x-status-badge :status="$order->paymentStatus" />
                                @else
                                    <span class="billing-state-pill {{ $order->ems_tax_status === 'paid' ? 'is-paid' : '' }}">{{ $order->ems_tax_status_label }}</span>
                                @endif
                            </header>

                            <div class="billing-order-amount">
                                <span>{{ $amountLabel }}</span>
                                <strong>Rp {{ number_format($shownAmount, 0, ',', '.') }}</strong>
                            </div>

                            <dl class="billing-order-meta">
                                @if($scope === 'orders')
                                    <div><dt>Jenis pembayaran</dt><dd>{{ $order->payment_type_label }}</dd></div>
                                    <div><dt>{{ $tab === 'paid' ? 'Dibayar pada' : 'Status' }}</dt><dd>{{ $tab === 'paid' ? ($order->payment_submitted_at?->translatedFormat('d M Y, H:i') ?: 'Pembayaran berhasil') : ($tab === 'refund' ? 'Direfund' : ($order->paymentStatus?->name ?: 'Menunggu pembayaran')) }}</dd></div>
                                @else
                                    <div><dt>Jatuh tempo</dt><dd>{{ $order->ems_tax_due_date?->translatedFormat('d M Y') ?: 'Belum ditentukan' }}</dd></div>
                                    <div><dt>Status</dt><dd>{{ $order->ems_tax_status_label }}</dd></div>
                                @endif
                            </dl>

                            <footer>
                                <a href="{{ $order->billing_tracking_url }}">Lihat rincian <x-public-icon name="arrow-right" :size="13" /></a>
                                @if($scope === 'orders' && $order->payment_proof_path)
                                    <a class="billing-proof-link" href="{{ route('profile.orders.payment-proof', $order) }}" target="_blank" rel="noopener">Bukti bayar</a>
                                @endif
                            </footer>
                        </article>
                    @empty
                        <div class="billing-empty"><x-public-icon name="card" :size="24" /><strong>Tidak ada transaksi di kategori ini.</strong><p>Data pembayaran akan otomatis tampil ketika tersedia.</p><a href="{{ route('orders.index') }}">Kembali ke Pesanan</a></div>
                    @endforelse
                @endif
            </section>
        </div>
    </main>
</x-layouts.app>
