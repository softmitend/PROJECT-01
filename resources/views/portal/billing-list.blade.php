<x-layouts.app :title="$title.' — Ocean Paws'">
    <style>
        .portal-billing-v2 .portal-v2-section-head{
            display:flex;
            min-height:86px;
            align-items:center;
            justify-content:space-between;
            gap:18px;
            margin-bottom:20px;
            border:2px solid var(--pv2-ink);
            border-radius:16px;
            padding:14px 16px;
            background:linear-gradient(120deg,#fffdf8 0 72%,#dce9cf 72%);
            box-shadow:4px 4px 0 var(--pv2-ink);
        }
        .portal-billing-v2 .portal-v2-section-head-main{display:flex;min-width:0;align-items:center;gap:13px}
        .portal-billing-v2 .portal-v2-section-head-icon{
            display:grid;
            width:46px;
            height:46px;
            flex:0 0 auto;
            place-items:center;
            border:2px solid var(--pv2-ink);
            border-radius:12px;
            background:var(--pv2-mint);
            box-shadow:3px 3px 0 var(--pv2-ink);
        }
        .portal-billing-v2 .portal-v2-section-copy{min-width:0}
        .portal-billing-v2 .portal-v2-section-copy>span{display:block;color:var(--pv2-pink-strong);font-size:9px;font-weight:900;letter-spacing:.13em}
        .portal-billing-v2 .portal-v2-section-copy h2{margin:4px 0 0;font-family:var(--pv2-heading);font-size:24px;line-height:1.05;letter-spacing:-.035em}
        .portal-billing-v2 .portal-v2-section-count{
            display:flex;
            min-width:72px;
            align-items:baseline;
            justify-content:center;
            gap:4px;
            border:2px solid var(--pv2-ink);
            border-radius:12px;
            padding:9px 11px;
            background:var(--pv2-paper);
            box-shadow:3px 3px 0 var(--pv2-ink);
        }
        .portal-billing-v2 .portal-v2-section-count strong{font-family:var(--pv2-heading);font-size:20px;line-height:1}
        .portal-billing-v2 .portal-v2-section-count span{color:var(--pv2-text);font-size:9px;font-weight:900;letter-spacing:.04em}
        @media(max-width:700px){
            .portal-billing-v2 .portal-v2-section-head{min-height:72px;border-radius:14px;padding:11px 12px;background:var(--pv2-paper);box-shadow:3px 3px 0 var(--pv2-ink)}
            .portal-billing-v2 .portal-v2-section-head-icon{width:40px;height:40px}
            .portal-billing-v2 .portal-v2-section-copy h2{font-size:19px}
            .portal-billing-v2 .portal-v2-section-count{min-width:58px;padding:8px 9px}
            .portal-billing-v2 .portal-v2-section-count strong{font-size:17px}
        }
    </style>
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
                            <span>FILTER</span>
                            <small>Pilih status tagihan</small>
                        </div>
                        <nav class="portal-v2-filters" aria-label="Daftar pembayaran">
                            <a class="{{ $tab === 'unpaid' ? 'active' : '' }}" href="{{ request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders') }}">
                                <x-public-icon name="wallet" :size="16" />
                                <span>Belum dibayar</span>
                                <b>{{ $unpaidCount }}</b>
                            </a>
                            <a class="{{ $tab === 'paid' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=paid' }}">
                                <x-public-icon name="check-circle" :size="16" />
                                <span>Berhasil</span>
                                <b>{{ $paidCount }}</b>
                            </a>
                            <a class="{{ $tab === 'refund' ? 'active' : '' }}" href="{{ (request()->routeIs('billing.ems') ? route('billing.ems') : route('billing.orders')).'?tab=refund' }}">
                                <x-public-icon name="history" :size="16" />
                                <span>Refund</span>
                                <b>{{ $refundCount }}</b>
                            </a>
                        </nav>
                    </aside>
                @endif

                <section class="portal-v2-content {{ !$member ? 'is-full' : '' }}">
                    @if($user && !$member)
                        <div class="portal-v2-empty">
                            <span class="portal-v2-empty-icon"><x-public-icon name="grid" :size="32" /></span>
                            <small>AKUN ADMIN</small>
                            <h2>Tagihan customer tidak tersedia di akun admin.</h2>
                            <p>Gunakan akun LINE customer untuk membuka data pembayaran pribadi.</p>
                            <a href="{{ route('admin.dashboard') }}">Buka dashboard</a>
                        </div>
                    @elseif(!$member)
                        <div class="portal-v2-empty">
                            <span class="portal-v2-empty-icon"><x-public-icon name="wallet" :size="32" /></span>
                            <small>TAGIHAN PRIBADI</small>
                            <h2>Login untuk melihat tagihanmu.</h2>
                            <p>Daftar pembayaran akan otomatis tampil sesuai pesanan yang terhubung ke akun LINE.</p>
                            <a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a>
                        </div>
                    @else
                        <div class="portal-v2-section-head">
                            <div class="portal-v2-section-head-main">
                                <span class="portal-v2-section-head-icon">
                                    <x-public-icon :name="$scope === 'ems' ? 'document' : 'card'" :size="18" />
                                </span>
                                <div class="portal-v2-section-copy">
                                    <span>{{ $scope === 'ems' ? 'EMS & PAJAK' : 'DAFTAR TAGIHAN' }}</span>
                                    <h2>{{ $tab === 'paid' ? 'Pembayaran selesai' : ($tab === 'refund' ? 'Riwayat refund' : 'Perlu dibayar') }}</h2>
                                </div>
                            </div>
                            <div class="portal-v2-section-count">
                                <strong>{{ $orders->count() }}</strong>
                                <span>data</span>
                            </div>
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
                                        <span class="portal-v2-thumb">
                                            @if($order->batch->catalog_image_path)<img src="{{ $order->batch->catalog_image_url }}" alt="">@else<x-public-icon name="box" :size="24" />@endif
                                        </span>
                                        <div class="portal-v2-card-title">
                                            <small>{{ $order->order_code }}</small>
                                            <h3>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h3>
                                            <p>{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                        </div>
                                        @if($scope === 'orders' && $order->paymentStatus)
                                            <x-status-badge :status="$order->paymentStatus" />
                                        @else
                                            <span class="portal-v2-state {{ $order->ems_tax_status === 'paid' ? 'is-paid' : '' }}">{{ $order->ems_tax_status_label }}</span>
                                        @endif
                                    </div>

                                    <div class="portal-v2-price-box">
                                        <span>{{ $amountLabel }}</span>
                                        <strong>Rp {{ number_format($shownAmount, 0, ',', '.') }}</strong>
                                    </div>

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
                                <div class="portal-v2-empty portal-v2-empty-inline">
                                    <span class="portal-v2-empty-icon"><x-public-icon name="card" :size="32" /></span>
                                    <small>KOSONG</small>
                                    <h2>Tidak ada transaksi di kategori ini.</h2>
                                    <p>Data pembayaran akan otomatis tampil ketika tersedia.</p>
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
