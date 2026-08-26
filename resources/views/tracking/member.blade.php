<x-layouts.app title="Rekap {{ $member->display_name }}">
    <x-page-heading title="Riwayat Pembelian {{ $member->display_name }}" description="{{ $member->orders->count() }} pembelian ditemukan">
        <x-slot:action><a class="inline-flex rounded-full border border-zinc-200 bg-white px-4 py-2 text-sm font-semibold shadow-sm transition hover:border-zinc-400" href="{{ route('tracking.index') }}#smart-search">Cari username LINE lain</a></x-slot:action>
    </x-page-heading>

    <div class="order-table-card">
        <div class="order-table-scroll">
            <table class="order-table tracking-history-table">
                <thead>
                    <tr>
                        <th>Pesanan & Batch</th>
                        <th>Status</th>
                        <th>Jumlah item</th>
                        <th>Update terakhir</th>
                        <th><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->orders as $order)
                        <tr class="transition hover:bg-zinc-50/80">
                            <td data-label="Pesanan">
                                <div class="order-table-primary font-mono text-violet-700">{{ $order->order_code }}</div>
                                <div class="order-table-secondary">{{ $order->batch->batch_number }} · {{ $order->batch->batch_name }}</div>
                            </td>
                            <td data-label="Status"><x-status-badge :status="$order->tracking_status" /></td>
                            <td data-label="Jumlah item" class="font-semibold text-zinc-700">{{ $order->items->sum('quantity') }}</td>
                            <td data-label="Update terakhir" class="text-zinc-500">{{ $order->updated_at->format('d M Y H:i') }}</td>
                            <td data-label="Aksi" class="text-right">
                                <a class="order-table-action" href="{{ URL::temporarySignedRoute('tracking.order', now()->addMinutes(15), ['memberCode' => $member->member_code, 'memberOrder' => $order]) }}">Lihat detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr class="tracking-table-empty"><td colspan="5" class="px-4 py-8 text-center text-zinc-500">Belum ada pembelian yang tercatat untuk username LINE ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
