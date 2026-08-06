<x-layouts.app title="Pesanan {{ $order->order_code }}">
    <x-page-heading title="{{ $order->order_code }}" description="{{ $order->member->display_name }} · Batch {{ $order->batch->batch_number }}">
        <x-slot:action><a class="rounded-md border border-zinc-300 px-4 py-2 text-sm" href="{{ route('admin.member-orders.edit', $order, false) }}">Edit</a></x-slot:action>
    </x-page-heading>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Status efektif</div><div class="mt-2"><x-status-badge :status="$order->effective_status" /></div></div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Total</div><div class="mt-2 text-xl font-semibold">{{ $order->total_amount ? 'Rp '.number_format($order->total_amount, 0, ',', '.') : '-' }}</div></div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Pembayaran</div><div class="mt-2">{{ $order->payment_status ?: '-' }}</div></div>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3">Harga</th><th class="px-4 py-3">Subtotal</th><th class="px-4 py-3">Status khusus</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">@foreach($order->items as $item)<tr><td class="px-4 py-3"><div class="font-medium">{{ $item->item_name }}</div><div class="text-zinc-500">{{ $item->variant }}</div></td><td class="px-4 py-3">{{ $item->quantity }}</td><td class="px-4 py-3">{{ $item->unit_price ? number_format($item->unit_price, 0, ',', '.') : '-' }}</td><td class="px-4 py-3">{{ $item->subtotal ? number_format($item->subtotal, 0, ',', '.') : '-' }}</td><td class="px-4 py-3"><x-status-badge :status="$item->overrideStatus" /></td></tr>@endforeach</tbody>
        </table>
    </div>

    <section class="mt-6 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <h2 class="font-semibold">Riwayat Status</h2>
        <div class="mt-4 space-y-3">@forelse($order->statusHistories as $history)<div class="border-l-2 border-zinc-200 pl-4 text-sm"><div><x-status-badge :status="$history->oldStatus" /> ke <x-status-badge :status="$history->newStatus" /></div><div class="mt-1 text-zinc-500">{{ $history->note ?: 'Tanpa catatan' }} · {{ $history->changedBy?->name ?: 'Sistem' }} · {{ $history->created_at->format('d M Y H:i') }}</div></div>@empty<p class="text-sm text-zinc-500">Belum ada riwayat.</p>@endforelse</div>
    </section>
</x-layouts.app>
