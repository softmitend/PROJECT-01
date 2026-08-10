<x-layouts.app title="Detail {{ $member->display_name }}">
    <x-page-heading title="{{ $member->display_name }}" description="{{ $member->member_code }}">
        <x-slot:action><a class="rounded-md border border-zinc-300 px-4 py-2 text-sm" href="{{ route('admin.members.edit', $member, false) }}">Edit Pelanggan</a></x-slot:action>
    </x-page-heading>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="grid gap-5 text-sm sm:grid-cols-3">
            <div><div class="text-zinc-500">Email</div><div class="mt-1 font-medium">{{ $member->email ?: '-' }}</div></div>
            <div><div class="text-zinc-500">Nomor telepon</div><div class="mt-1 font-medium">{{ $member->phone ?: '-' }}</div></div>
            <div><div class="text-zinc-500">Status</div><div class="mt-1 font-medium">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
            <div class="sm:col-span-3"><div class="text-zinc-500">Alamat</div><div class="mt-1 whitespace-pre-line">{{ $member->address ?: '-' }}</div></div>
        </div>
        @if ($member->notes)<p class="mt-5 border-t border-zinc-100 pt-4 text-sm text-zinc-700"><strong>Catatan admin:</strong> {{ $member->notes }}</p>@endif
    </div>

    <h2 class="mb-3 mt-8 font-semibold">Daftar Pembelian</h2>
    <div class="order-table-card">
        <div class="order-table-scroll">
        <table class="order-table">
            <thead><tr><th>Pesanan</th><th>Status</th><th>Jumlah item</th><th><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
                @forelse ($member->orders as $order)
                    <tr><td><div class="order-table-primary text-violet-700">{{ $order->order_code }}</div><div class="order-table-secondary">Batch {{ $order->batch->batch_number }}</div></td><td><x-status-badge :status="$order->effective_status" /></td><td class="font-semibold text-zinc-700">{{ $order->items->sum('quantity') }} item</td><td class="text-right"><a class="order-table-action" href="{{ route('admin.member-orders.show', $order, false) }}">Buka</a></td></tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-zinc-400">Belum ada pembelian.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-layouts.app>
