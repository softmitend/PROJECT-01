<x-layouts.app title="Detail Pesanan {{ $order->order_code }}">
    <x-page-heading title="Batch {{ $order->batch->batch_number }}" description="{{ $member->display_name }} · {{ $order->order_code }}">
        <x-slot:action><a class="inline-flex rounded-full border border-zinc-200 bg-white px-4 py-2 text-sm font-semibold shadow-sm transition hover:border-zinc-400" href="{{ route('tracking.member', $member->member_code, false) }}">Kembali ke riwayat</a></x-slot:action>
    </x-page-heading>

    <div class="mb-6 rounded-3xl border border-violet-100 bg-gradient-to-br from-violet-50 to-white p-6 shadow-xl shadow-violet-100/30">
        <div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Status pesanan</div>
        <div class="mt-3"><x-status-badge :status="$order->effective_status" /></div>
        @if ($order->notes)
            <p class="mt-4 text-sm leading-6 text-zinc-600">{{ $order->notes }}</p>
        @endif
    </div>

    <div class="order-table-card">
        <div class="order-table-scroll">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Jajanan</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Status item</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td><div class="order-table-primary">{{ $item->item_name }}</div><div class="order-table-secondary">{{ $item->variant ?: 'Tanpa varian' }}</div></td>
                            <td class="font-semibold text-zinc-700">{{ $item->quantity }}</td>
                            <td class="font-semibold text-zinc-700">{{ $item->unit_price ? 'Rp '.number_format($item->unit_price, 0, ',', '.') : '-' }}</td>
                            <td><x-status-badge :status="$item->effective_status" /></td>
                            <td class="text-zinc-600">{{ $item->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <section class="soft-card mt-6 rounded-3xl p-6">
        <h2 class="font-bold">Timeline status</h2>
        <div class="mt-5 space-y-4">
            @forelse ($timeline as $history)
                <div class="border-l border-violet-200 pl-5 text-sm">
                    <div><x-status-badge :status="$history->oldStatus" /> <span class="mx-1 text-zinc-400">ke</span> <x-status-badge :status="$history->newStatus" /></div>
                    <div class="mt-2 text-zinc-500">{{ $history->note ?: 'Tanpa catatan' }} &middot; {{ $history->created_at->format('d M Y H:i') }}</div>
                </div>
            @empty
                <p class="text-sm text-zinc-500">Belum ada riwayat status untuk pesanan ini.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
