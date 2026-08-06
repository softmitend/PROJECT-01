<x-layouts.app title="Pesanan Member">
    <x-page-heading title="Pesanan Member" description="Kelola pesanan tiap member pada tiap batch.">
        <x-slot:action><a class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('admin.member-orders.create', [], false) }}">Tambah Pesanan</a></x-slot:action>
    </x-page-heading>
    <form class="mb-4 flex gap-2"><input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" name="q" value="{{ request('q') }}" placeholder="Cari order, member, atau batch"><button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Cari</button></form>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Member</th><th class="px-4 py-3">Batch</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Item</th><th class="px-4 py-3">Total</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($orders as $order)
                    <tr><td class="px-4 py-3 font-mono text-xs">{{ $order->order_code }}</td><td class="px-4 py-3">{{ $order->member->display_name }}</td><td class="px-4 py-3">{{ $order->batch->batch_number }}</td><td class="px-4 py-3"><x-status-badge :status="$order->effective_status" /></td><td class="px-4 py-3">{{ $order->items_count }}</td><td class="px-4 py-3">{{ $order->total_amount ? 'Rp '.number_format($order->total_amount, 0, ',', '.') : '-' }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.member-orders.show', $order, false) }}">Detail</a></td></tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-zinc-500">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-layouts.app>
