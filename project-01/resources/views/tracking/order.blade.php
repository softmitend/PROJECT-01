<x-layouts.app title="Detail Pesanan {{ $order->order_code }}">
    <x-page-heading title="Batch {{ $order->batch->batch_number }}" description="{{ $member->display_name }} · {{ $order->order_code }}" />

    <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-zinc-500">Status pesanan</div>
        <div class="mt-2"><x-status-badge :status="$order->effective_status" /></div>
        @if ($order->notes)
            <p class="mt-3 text-sm text-zinc-700">{{ $order->notes }}</p>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Jajanan</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Status item</th>
                    <th class="px-4 py-3">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->item_name }}</div>
                            <div class="text-zinc-500">{{ $item->variant }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ $item->unit_price ? 'Rp '.number_format($item->unit_price, 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$item->overrideStatus" /></td>
                        <td class="px-4 py-3 text-zinc-600">{{ $item->notes ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <section class="mt-6 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <h2 class="font-semibold">Timeline Status</h2>
        <div class="mt-4 space-y-3">
            @forelse ($order->statusHistories as $history)
                <div class="border-l-2 border-zinc-200 pl-4 text-sm">
                    <div><x-status-badge :status="$history->oldStatus" /> ke <x-status-badge :status="$history->newStatus" /></div>
                    <div class="mt-1 text-zinc-500">{{ $history->note ?: 'Tanpa catatan' }} · {{ $history->created_at->format('d M Y H:i') }}</div>
                </div>
            @empty
                <p class="text-sm text-zinc-500">Belum ada riwayat status khusus untuk pesanan ini.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
