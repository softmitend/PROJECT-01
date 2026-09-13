<x-layouts.app title="Ocean Paws — Group Order K-pop">
    <div class="tracker-luna">
        <section class="tracker-luna-hero" id="home">
            <div class="tracker-luna-shell">
                <div class="tracker-luna-title-wrap">
                    <i class="bi bi-stars tracker-luna-star tracker-luna-star-one" aria-hidden="true"></i>
                    <i class="bi bi-stars tracker-luna-star tracker-luna-star-two" aria-hidden="true"></i>
                    <span class="tracker-luna-cloud tracker-luna-cloud-one"></span>
                    <span class="tracker-luna-cloud tracker-luna-cloud-two"></span>
                    <p class="tracker-luna-title-kicker">OCEAN PAWS · GROUP ORDER DESK</p>
                    <h1><span>Titipan K-pop,</span><span class="is-shifted">lebih dekat</span><span class="is-accent">denganmu.</span></h1>
                    <p class="tracker-luna-title-note">Ocean Paws membuka titipan album, photocard, dan merchandise K-pop lewat sistem group order yang rapi, transparan, dan mudah diikuti dari awal sampai tiba.</p>

                    <aside class="tracker-luna-go-showcase" aria-label="Pilihan titipan Group Order Ocean Paws">
                        <div class="tracker-luna-go-showcase-head">
                            <span>OPEN GROUP ORDER</span>
                            <i class="bi bi-stars" aria-hidden="true"></i>
                        </div>
                        <div class="tracker-luna-go-showcase-art">
                            <span class="tracker-luna-go-disc"><i class="bi bi-disc" aria-hidden="true"></i></span>
                            <span class="tracker-luna-go-card"><i class="bi bi-card-image" aria-hidden="true"></i></span>
                            <span class="tracker-luna-go-bag"><i class="bi bi-bag-heart" aria-hidden="true"></i></span>
                        </div>
                        <div class="tracker-luna-go-showcase-copy">
                            <small>ALBUM · PHOTOCARD · MERCH</small>
                            <strong>Titip rilisan favoritmu.</strong>
                            <p>Pembelian kolektif dengan rincian biaya dan perjalanan order yang lebih teratur.</p>
                        </div>
                        <div class="tracker-luna-go-showcase-foot">
                            <span><i class="bi bi-check-lg" aria-hidden="true"></i> Biaya jelas</span>
                            <span><i class="bi bi-arrow-up-right" aria-hidden="true"></i> Update rapi</span>
                        </div>
                    </aside>
                </div>

                <div class="tracker-luna-scallop" aria-hidden="true"></div>

                <div class="tracker-luna-tracking-window" id="tracking">
                    <div class="tracker-luna-window-topbar">
                        <span><i class="bi bi-broadcast-pin" aria-hidden="true"></i> LIVE ORDER DESK</span>
                        <span>OCEAN PAWS</span>
                    </div>
                    <div class="tracker-luna-window-grid">
                        <div class="tracker-luna-window-copy">
                            <span>TRACK YOUR ORDER</span>
                            <h2>Semua titipan dalam satu tempat.</h2>
                            <p>Gunakan kode pesanan atau username LINE untuk melihat status, item, dan tagihan Pajak/EMS.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tracking.search') }}" class="tracker-luna-search-form">
                        @csrf
                        <label for="query" class="sr-only">Kode pesanan atau username LINE</label>
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input id="query" name="query" value="{{ old('query', $searchQuery ?? '') }}" placeholder="Kode pesanan atau username LINE" required autocomplete="off">
                        <button type="submit"><span>Cari order</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
                    </form>

                    @isset($orderResult)
                        <details open class="ocean-result-panel tracker-luna-result" data-smart-search-result="tracking">
                            <summary class="ocean-result-summary">
                                <span class="ocean-result-icon tracker-luna-result-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                                <span class="ocean-result-copy min-w-0 flex-1"><span class="ocean-result-kicker">Hasil tracking</span><span class="ocean-result-title">{{ $orderResult->order_code }}</span></span>
                                <span class="ocean-result-badge"><x-status-badge :status="$orderResult->tracking_status" /></span>
                                <i class="bi bi-chevron-down ocean-chevron" aria-hidden="true"></i>
                            </summary>
                            <div class="tracker-luna-result-body">
                                <div class="tracker-luna-result-facts">
                                    <div><span>Pelanggan</span><strong>{{ $orderResult->member->display_name }}</strong></div>
                                    <div><span>Batch</span><strong>{{ $orderResult->batch->batch_name ?: $orderResult->batch->batch_number }}</strong></div>
                                    <div><span>Jumlah</span><strong>{{ $orderResult->items->sum('quantity') }} item</strong></div>
                                    <div><span>Pembayaran</span><strong>{{ $orderResult->paymentStatus?->name ?: 'Belum ditentukan' }}</strong></div>
                                </div>
                                <x-order-ems-bill :order="$orderResult" />
                                <div class="tracker-luna-result-heading"><div><span>Item pesanan</span><small>Status setiap item dapat berbeda.</small></div><a href="{{ URL::temporarySignedRoute('tracking.progress', now()->addMinutes(15), ['orderCode' => $orderResult->order_code]) }}">Detail lengkap <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
                                <div class="ocean-history-items-scroll"><table class="ocean-history-items-table"><thead><tr><th>Produk</th><th>Qty</th><th>Status item</th></tr></thead><tbody>
                                    @forelse($orderResult->items as $item)
                                        <tr><td><strong>{{ $item->item_name }}</strong><span>{{ $item->variant ?: 'Tanpa varian' }}</span></td><td>{{ $item->quantity }}</td><td><x-status-badge :status="$orderResult->is_refunded ? $orderResult->tracking_status : ($item->overrideStatus ?: $orderResult->effective_status)" /></td></tr>
                                    @empty<tr><td colspan="3">Belum ada produk.</td></tr>@endforelse
                                </tbody></table></div>
                            </div>
                        </details>
                    @endisset

                    @isset($memberResult)
                        <details open class="ocean-result-panel tracker-luna-result" data-smart-search-result="history">
                            <summary class="ocean-result-summary">
                                <span class="ocean-result-icon tracker-luna-result-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                                <span class="ocean-result-copy min-w-0 flex-1"><span class="ocean-result-kicker">Riwayat pembelian</span><span class="ocean-result-title">{{ $memberResult->display_name }}</span></span>
                                <span class="ocean-result-badge"><span class="tracker-luna-result-count">{{ $memberResult->orders->count() }} pesanan</span></span>
                                <i class="bi bi-chevron-down ocean-chevron" aria-hidden="true"></i>
                            </summary>
                            <div class="tracker-luna-history-list">
                                @forelse($memberResult->orders as $order)
                                    <details class="ocean-order-toggle">
                                        <summary><span class="min-w-0 flex-1"><strong class="tracker-luna-order-code">{{ $order->order_code }}</strong><span class="tracker-luna-order-subtitle">{{ $order->batch->batch_name ?: $order->batch->batch_number }} · {{ $order->items->sum('quantity') }} item</span></span><x-status-badge :status="$order->tracking_status" /><i class="bi bi-chevron-down ocean-chevron" aria-hidden="true"></i></summary>
                                        <div class="tracker-luna-history-order">
                                            <div class="tracker-luna-order-meta"><span>Diperbarui {{ $order->updated_at->format('d M Y, H:i') }}</span><span>Pembayaran: <strong>{{ $order->paymentStatus?->name ?: 'Belum ditentukan' }}</strong></span></div>
                                            <x-order-ems-bill :order="$order" compact />
                                            <div class="ocean-history-items-scroll"><table class="ocean-history-items-table"><thead><tr><th>Produk</th><th>Qty</th><th>Status item</th></tr></thead><tbody>
                                                @foreach($order->items as $item)<tr><td><strong>{{ $item->item_name }}</strong><span>{{ $item->variant ?: 'Tanpa varian' }}</span></td><td>{{ $item->quantity }}</td><td><x-status-badge :status="$order->is_refunded ? $order->tracking_status : ($item->overrideStatus ?: $order->effective_status)" /></td></tr>@endforeach
                                            </tbody></table></div>
                                            <a href="{{ URL::temporarySignedRoute('tracking.order', now()->addMinutes(15), ['memberCode' => $memberResult->member_code, 'memberOrder' => $order]) }}" class="tracker-luna-detail-link">Lihat tracking lengkap <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                                        </div>
                                    </details>
                                @empty<p class="tracker-luna-empty">Belum ada riwayat pesanan untuk username LINE ini.</p>@endforelse
                            </div>
                        </details>
                    @endisset
                </div>

                <div class="tracker-hybrid-stats" aria-label="Keunggulan Group Order Ocean Paws">
                    <div><strong>K-POP</strong><span>Album, photocard & merch</span></div>
                    <div><strong>GO</strong><span>Pembelian kolektif</span></div>
                    <div><strong>Rinci</strong><span>Biaya transparan</span></div>
                    <div><strong>Update</strong><span>Tracking pendukung</span></div>
                </div>
            </div>
        </section>

        <section class="tracker-luna-cream" id="services">
            <div class="tracker-luna-shell">
                <div class="tracker-luna-final-cta">
                    <span>GROUP ORDER K-POP OCEAN PAWS</span>
                    <h2>Titip wishlist K-popmu dengan proses yang lebih rapi.</h2>
                    <p>Gabungkan pembelian album, photocard, dan merchandise favoritmu dalam group order dengan rincian biaya yang jelas. Tracking tersedia sebagai pendamping setelah pesanan berjalan.</p>
                    <a href="#tracking">Cek pesananmu <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </section>

        <footer class="tracker-luna-footer">
            <img src="{{ asset('img/Picsart_26-08-23_02-05-04-834.png') }}" alt="Ocean Paws">
            <p>Ocean Paws · K-pop Group Order</p>
            <a href="#home">Kembali ke atas <i class="bi bi-arrow-up" aria-hidden="true"></i></a>
        </footer>

    </div>
</x-layouts.app>
