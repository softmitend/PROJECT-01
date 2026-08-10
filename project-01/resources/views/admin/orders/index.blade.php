<x-layouts.app title="Pesanan">
    <x-page-heading title="Pesanan" description="Kelola pembelian pelanggan dan status produk di setiap batch.">
        <x-slot:action><a class="admin-primary-action" href="{{ route('admin.member-orders.create', [], false) }}">+ Tambah pesanan</a></x-slot:action>
    </x-page-heading>
    <div class="order-table-card">
        <form class="order-table-toolbar">
            <input class="min-w-0 flex-1" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan, pelanggan, atau batch...">
            <button class="order-table-toolbar-button" type="submit"><svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="9" r="5.5"/><path d="m13 13 4 4"/></svg>Cari</button>
        </form>
        <div class="order-table-scroll">
            <table class="order-table">
                <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Batch</th><th>Status</th><th>Item</th><th>Total</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><div class="order-table-primary text-violet-700">{{ $order->order_code }}</div><div class="order-table-secondary">Diperbarui {{ $order->updated_at->format('d M Y, H:i') }}</div></td>
                            <td><div class="order-table-primary">{{ $order->member->display_name }}</div><div class="order-table-secondary">{{ $order->member->email ?: '-' }}</div></td>
                            <td class="text-zinc-600">{{ $order->batch->batch_number }}</td>
                            <td><x-status-badge :status="$order->effective_status" /></td>
                            <td class="font-semibold text-zinc-700">{{ $order->items_count }}</td>
                            <td class="font-semibold text-zinc-700">{{ $order->total_amount ? 'Rp '.number_format($order->total_amount, 0, ',', '.') : '-' }}</td>
                            <td class="text-right"><a class="order-table-action" href="{{ route('admin.member-orders.show', $order, false) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-zinc-400">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="order-table-footer"><span>Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan</span><div>{{ $orders->links() }}</div></div>
    </div>
</x-layouts.app>
