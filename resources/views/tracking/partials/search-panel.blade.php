<section class="tracking-lookup-hero" aria-labelledby="order-search-title">
    <div class="tracking-lookup-hero-inner">
        <div class="tracking-lookup-copy">
            <span class="tracking-lookup-kicker">TRACK YOUR ORDER · TANPA LOGIN</span>
            <h1 id="order-search-title">Pesananmu,<br><span>selalu terlacak.</span></h1>
            <p>Masukkan kode pesanan untuk melihat satu perjalanan, atau username LINE untuk membuka seluruh riwayatmu.</p>

            <form method="POST" action="{{ route('tracking.search') }}" class="tracking-lookup-form" role="search" aria-label="Cari tracking dan riwayat pesanan">
                @csrf
                <label for="tracking-query" class="sr-only">Kode pesanan atau username LINE</label>
                <span class="tracking-lookup-search-icon"><x-public-icon name="search" :size="20" /></span>
                <input id="tracking-query" type="search" name="query" value="{{ old('query', $searchQuery ?? '') }}" autocomplete="off" autocapitalize="none" spellcheck="false" maxlength="255" placeholder="Contoh: ORD-001 atau @username" aria-describedby="tracking-query-hint{{ $errors->has('query') ? ' tracking-query-error' : '' }}" @if($errors->has('query')) aria-invalid="true" autofocus @endif required>
                <button type="submit"><span>Lacak pesanan</span><x-public-icon name="arrow-right" :size="16" /></button>
            </form>
            <p id="tracking-query-hint" class="tracking-lookup-hint"><x-public-icon name="sparkle" :size="13" /> Data mengikuti pembaruan terakhir dari admin.</p>

            @error('query')
                <div id="tracking-query-error" class="tracking-lookup-error" role="alert"><x-public-icon name="search" :size="18" /><span>{{ $message }}</span></div>
            @enderror
        </div>

        <aside class="tracking-guide-card" aria-label="Tahapan tracking pesanan">
            <span class="tracking-guide-star">✦</span>
            <div class="tracking-guide-label">LIVE ORDER JOURNEY</div>
            <h2>Satu pencarian,<br>semua jadi jelas.</h2>
            <div class="tracking-guide-flow">
                <div><i><x-public-icon name="bag" :size="16" /></i><span><strong>Pesanan tercatat</strong><small>Item dan pembayaran</small></span></div>
                <div><i><x-public-icon name="box" :size="16" /></i><span><strong>Proses diperbarui</strong><small>Warehouse hingga EMS</small></span></div>
                <div><i><x-public-icon name="truck" :size="16" /></i><span><strong>Paket dikirim</strong><small>Tracking sampai tujuan</small></span></div>
            </div>
            <p>Tidak perlu menghubungi admin hanya untuk menanyakan update terakhir.</p>
        </aside>
    </div>
</section>

<section class="tracking-result-section" aria-live="polite">
    <div class="tracking-result-shell">
        @isset($orderResult)
            <article class="tracking-result-card" data-smart-search-result="tracking">
                <header class="tracking-result-banner">
                    <div>
                        <span class="tracking-result-kicker"><x-public-icon name="box" :size="14" /> {{ $orderResult->batch->batch_number }}</span>
                        <h2>{{ $orderResult->order_code }}</h2>
                        <p>{{ $orderResult->batch->batch_name ?: 'Pesanan Ocean Paws' }} &middot; Diperbarui {{ $orderResult->updated_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="tracking-current-status">
                        <small>STATUS TERKINI</small>
                        <x-status-badge :status="$orderResult->tracking_status" />
                        <span>{{ $orderResult->tracking_status?->description ?: 'Status pesanan mengikuti pembaruan terbaru dari admin.' }}</span>
                    </div>
                </header>

                <div class="tracking-result-layout">
                    <div class="tracking-result-main">
                        <div class="tracking-result-heading">
                            <div><span>RINCIAN PESANAN</span><h3>Barang yang sedang dipantau</h3></div>
                            <strong>{{ $orderResult->items->sum('quantity') }} item</strong>
                        </div>

                        <div class="tracking-result-items">
                            @forelse($orderResult->items as $item)
                                <div class="tracking-result-item">
                                    <span class="tracking-result-item-icon"><x-public-icon name="box" :size="18" /></span>
                                    <span><strong>{{ $item->item_name }}</strong><small>{{ $item->variant ?: 'Tanpa varian' }} &middot; Qty {{ $item->quantity }}</small></span>
                                    <x-status-badge :status="$orderResult->is_refunded ? $orderResult->tracking_status : ($item->overrideStatus ?: $orderResult->effective_status)" />
                                </div>
                            @empty
                                <p class="tracking-result-empty-copy">Belum ada item yang tercatat.</p>
                            @endforelse
                        </div>

                        @if($orderResult->batch->notes)
                            <div class="tracking-admin-note"><x-public-icon name="message" :size="18" /><span><strong>Catatan admin</strong><small>{{ $orderResult->batch->notes }}</small></span></div>
                        @endif
                    </div>

                    <aside class="tracking-result-side">
                        <div class="tracking-order-summary">
                            <span>RINGKASAN</span>
                            <div><small>Pelanggan</small><strong>{{ $orderResult->member->display_name }}</strong></div>
                            <div><small>Pembayaran</small><strong>{{ $orderResult->paymentStatus?->name ?: 'Belum ditentukan' }}</strong></div>
                            <div><small>Total barang</small><strong>Rp {{ number_format($orderResult->total_amount, 0, ',', '.') }}</strong></div>
                        </div>
                        <x-order-ems-bill :order="$orderResult" />
                        <a class="tracking-result-action" href="{{ URL::temporarySignedRoute('tracking.progress', now()->addMinutes(15), ['orderCode' => $orderResult->order_code]) }}">Lihat perjalanan lengkap <x-public-icon name="arrow-right" :size="15" /></a>
                    </aside>
                </div>
            </article>
        @endisset

        @isset($memberResult)
            <article class="tracking-result-card tracking-history-card" data-smart-search-result="history">
                <header class="tracking-result-banner">
                    <div>
                        <span class="tracking-result-kicker"><x-public-icon name="history" :size="14" /> RIWAYAT PEMBELIAN</span>
                        <h2>{{ $memberResult->display_name }}</h2>
                        <p>{{ '@'.$memberResult->username }} &middot; {{ $memberResult->orders->count() }} pesanan ditemukan</p>
                    </div>
                    <div class="tracking-current-status tracking-member-status">
                        <small>AKUN DITEMUKAN</small>
                        <strong>{{ $memberResult->orders->sum(fn ($order) => $order->items->sum('quantity')) }} item tercatat</strong>
                        <span>Buka setiap pesanan untuk melihat item, status, pembayaran, dan Pajak/EMS.</span>
                    </div>
                </header>

                <div class="tracking-history-list">
                    @forelse($memberResult->orders as $order)
                        <details>
                            <summary>
                                <span class="tracking-history-icon"><x-public-icon name="box" :size="18" /></span>
                                <span><strong>{{ $order->order_code }}</strong><small>{{ $order->batch->batch_name ?: $order->batch->batch_number }} &middot; {{ $order->items->sum('quantity') }} item</small></span>
                                <x-status-badge :status="$order->tracking_status" />
                                <x-public-icon name="chevron-right" :size="16" />
                            </summary>
                            <div class="tracking-history-detail">
                                @foreach($order->items as $item)
                                    <div><span><strong>{{ $item->item_name }}</strong><small>{{ $item->variant ?: 'Tanpa varian' }} &middot; Qty {{ $item->quantity }}</small></span><x-status-badge :status="$order->is_refunded ? $order->tracking_status : ($item->overrideStatus ?: $order->effective_status)" /></div>
                                @endforeach
                                <x-order-ems-bill :order="$order" compact />
                                <a href="{{ URL::temporarySignedRoute('tracking.order', now()->addMinutes(15), ['memberCode' => $memberResult->member_code, 'memberOrder' => $order]) }}">Lihat detail & perjalanan <x-public-icon name="arrow-right" :size="14" /></a>
                            </div>
                        </details>
                    @empty
                        <p class="tracking-result-empty-copy">Belum ada riwayat pesanan untuk username LINE ini.</p>
                    @endforelse
                </div>
            </article>
        @endisset

        @if(!isset($orderResult) && !isset($memberResult))
            <article class="tracking-lookup-empty">
                <span><x-public-icon name="search" :size="20" /></span>
                <div>
                    <h2>Belum ada pesanan yang dicari.</h2>
                    <p>Masukkan kode atau username LINE. Tidak perlu login.</p>
                </div>
            </article>
        @endif
    </div>
</section>
