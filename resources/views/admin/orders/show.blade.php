<x-layouts.app title="Pesanan {{ $order->order_code }}">
    <x-page-heading title="{{ $order->order_code }}" description="{{ $order->member->display_name }} · Batch {{ $order->batch->batch_number }}">
        <x-slot:action><a class="rounded-md border border-zinc-300 px-4 py-2 text-sm" href="{{ route('admin.member-orders.edit', $order, false) }}">Edit</a></x-slot:action>
    </x-page-heading>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Status efektif</div><div class="mt-2"><x-status-badge :status="$order->effective_status" /></div></div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Total</div><div class="mt-2 text-xl font-semibold">{{ $order->total_amount ? 'Rp '.number_format($order->total_amount, 0, ',', '.') : '-' }}</div></div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-sm text-zinc-500">Pembayaran</div><div class="mt-2">@if($order->paymentStatus)<x-status-badge :status="$order->paymentStatus" />@else{{ $order->payment_status ?: '-' }}@endif</div></div>
    </div>

    <div class="order-table-card mt-6">
        <div class="order-table-scroll">
        <table class="order-table">
            <thead><tr><th>Item</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th>Status khusus</th></tr></thead>
            <tbody>@foreach($order->items as $item)<tr><td><div class="order-table-primary">{{ $item->item_name }}</div><div class="order-table-secondary">{{ $item->variant ?: 'Tanpa varian' }}</div></td><td class="font-semibold text-zinc-700">{{ $item->quantity }}</td><td class="text-zinc-600">{{ $item->unit_price ? 'Rp '.number_format($item->unit_price, 0, ',', '.') : '-' }}</td><td class="font-semibold text-zinc-700">{{ $item->subtotal ? 'Rp '.number_format($item->subtotal, 0, ',', '.') : '-' }}</td><td><x-status-badge :status="$item->overrideStatus" /></td></tr>@endforeach</tbody>
        </table>
        </div>
    </div>

    <section class="mt-6 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <h2 class="font-semibold">Riwayat Status</h2>
        <div class="mt-4 space-y-3">@forelse($order->statusHistories as $history)<div class="border-l-2 border-zinc-200 pl-4 text-sm"><div><x-status-badge :status="$history->oldStatus" /> ke <x-status-badge :status="$history->newStatus" /></div><div class="mt-1 text-zinc-500">{{ $history->note ?: 'Tanpa catatan' }} · {{ $history->changedBy?->name ?: 'Sistem' }} · {{ $history->created_at->format('d M Y H:i') }}</div></div>@empty<p class="text-sm text-zinc-500">Belum ada riwayat.</p>@endforelse</div>
    </section>
</x-layouts.app>
