<x-layouts.app title="Detail Pesanan {{ $order->order_code }}">
    <x-page-heading title="Detail Pesanan" description="Seluruh informasi batch, item, dan timeline pesanan dalam satu tampilan.">
        <x-slot:action><a class="inline-flex rounded-full border border-zinc-200 bg-white px-4 py-2 text-sm font-semibold shadow-sm transition hover:border-zinc-400" href="{{ URL::temporarySignedRoute('tracking.member', now()->addMinutes(15), ['memberCode' => $member->member_code]) }}">Kembali ke riwayat</a></x-slot:action>
    </x-page-heading>

    <article class="detail-record-card">
        <header class="detail-record-hero">
            <div class="min-w-0">
                <p class="detail-record-kicker">Pesanan {{ $member->display_name }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h2 class="detail-record-title">{{ $order->order_code }}</h2>
                    <x-status-badge :status="$order->tracking_status" />
                </div>
                <p class="detail-record-description">Batch {{ $order->batch->batch_number }} · {{ $order->batch->batch_name ?: 'K-pop merchandise' }}</p>
            </div>
            <div class="detail-record-id">{{ $order->items->sum('quantity') }} item</div>
        </header>

        @if ($order->notes)
            <section class="detail-record-section"><div class="detail-record-note mt-0"><span>Catatan pesanan</span><p>{{ $order->notes }}</p></div></section>
        @endif

        <section class="detail-record-section detail-record-table-section">
            <div class="detail-record-section-heading detail-record-table-heading"><div><h3>Item Pesanan</h3><p>Rincian produk, harga, dan progres tiap item.</p></div></div>
            <div class="order-table-scroll">
                <table class="order-table tracking-product-table">
                    <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Status item</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            <tr>
                                <td data-label="Produk"><div class="order-table-primary">{{ $item->item_name }}</div><div class="order-table-secondary">{{ $item->variant ?: 'Tanpa varian' }}</div></td>
                                <td data-label="Qty" class="font-semibold text-zinc-700">{{ $item->quantity }}</td>
                                <td data-label="Harga" class="font-semibold text-zinc-700">{{ $item->unit_price ? 'Rp '.number_format($item->unit_price, 0, ',', '.') : '-' }}</td>
                                <td data-label="Status item"><x-status-badge :status="$order->is_refunded ? $order->tracking_status : $item->effective_status" /></td>
                                <td data-label="Catatan" class="text-zinc-600">{{ $item->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr class="tracking-table-empty"><td colspan="5" class="py-10 text-center text-zinc-500">Belum ada item yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="detail-record-section">
            <div class="detail-record-section-heading"><div><h3>Timeline Status</h3><p>Pembaruan terbaru ditampilkan terlebih dahulu.</p></div></div>
            <div class="detail-record-timeline">
                @forelse ($timeline as $history)
                    <div class="detail-record-timeline-item">
                        <span class="detail-record-timeline-dot"></span>
                        <div class="flex flex-wrap items-center gap-2"><x-status-badge :status="$history->oldStatus" /><span class="text-zinc-400 text-xs">menjadi</span><x-status-badge :status="$history->newStatus" /></div>
                        <p>{{ $history->note ?: 'Tanpa catatan' }} · {{ $history->created_at->format('d M Y, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500">Belum ada riwayat status untuk pesanan ini.</p>
                @endforelse
            </div>
        </section>
    </article>
</x-layouts.app>
