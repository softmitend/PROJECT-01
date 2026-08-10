<x-layouts.app title="Kelola Status">
    <x-page-heading title="Kelola Status" description="Susun tahapan progress batch, pesanan, item produk, dan pembayaran customer.">
        <x-slot:action><a class="admin-primary-action" href="{{ route('admin.order-statuses.create', [], false) }}">+ Tambah status</a></x-slot:action>
    </x-page-heading>

    <section class="mb-6 rounded-2xl border border-violet-100 bg-gradient-to-r from-violet-50 via-blue-50 to-cyan-50 p-5">
        <div class="text-sm font-bold text-violet-800">Alur operasional dari spreadsheet</div>
        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-semibold text-violet-900">
            @foreach(['Menunggu Pemesanan', 'Ordered', 'Arrived Warehouse', 'Flight / Sea to INA', 'Arrived Admin', 'Siap Distribusi', 'Selesai'] as $step)
                <span class="rounded-full border border-violet-200 bg-white px-3 py-1.5">{{ $step }}</span>
                @if(!$loop->last)<span class="text-violet-400">→</span>@endif
            @endforeach
        </div>
        <p class="mt-3 text-sm text-violet-800">Status batch menjadi status default seluruh pesanan di dalamnya. Gunakan override hanya jika satu pesanan atau satu item punya kondisi berbeda.</p>
    </section>

    <form class="admin-filter mb-6 grid gap-3 sm:grid-cols-[1fr_180px_180px_auto]">
        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="q" value="{{ request('q') }}" placeholder="Cari nama atau code status">
        <select name="scope" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
            <option value="">Semua cakupan</option>
            <option value="batch" @selected(request('scope') === 'batch')>Batch</option>
            <option value="member_order" @selected(request('scope') === 'member_order')>Pesanan</option>
            <option value="order_item" @selected(request('scope') === 'order_item')>Item</option>
            <option value="payment" @selected(request('scope') === 'payment')>Pembayaran</option>
            <option value="all" @selected(request('scope') === 'all')>Semua</option>
        </select>
        <select name="active" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
            <option value="">Aktif & nonaktif</option>
            <option value="1" @selected(request('active') === '1')>Aktif saja</option>
            <option value="0" @selected(request('active') === '0')>Nonaktif saja</option>
        </select>
        <button class="rounded-xl bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>

    @php
        $scopeLabels = ['all' => 'Berlaku untuk Semua Progress', 'batch' => 'Progress Batch', 'member_order' => 'Kondisi Pesanan', 'order_item' => 'Kondisi Item', 'payment' => 'Status Pembayaran'];
        $scopeDescriptions = [
            'all' => 'Dapat dipilih pada batch, pesanan, maupun item.',
            'batch' => 'Perubahan di sini otomatis diikuti pesanan tanpa override.',
            'member_order' => 'Khusus kondisi progress satu pesanan customer.',
            'order_item' => 'Khusus satu produk, misalnya barang kurang atau rusak.',
            'payment' => 'Pilihan status pembayaran pada form tambah dan edit pesanan.',
        ];
    @endphp

    <div class="space-y-6">
        @forelse($statuses->groupBy('scope') as $scope => $scopeStatuses)
            <section class="order-table-card">
                <div class="border-b border-zinc-200 bg-zinc-50 px-3 py-3">
                    <h2 class="font-bold">{{ $scopeLabels[$scope] ?? $scope }}</h2>
                    <p class="mt-1 text-sm text-zinc-500">{{ $scopeDescriptions[$scope] ?? '' }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="order-table">
                        <thead>
                            <tr><th>Urutan</th><th>Status</th><th>Keterangan</th><th>Sifat</th><th>Dipakai</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach($scopeStatuses as $status)
                                @php($usage = $status->batches_count + $status->member_orders_count + $status->order_items_count + $status->payment_member_orders_count + $status->old_histories_count + $status->new_histories_count)
                                <tr class="{{ $status->is_active ? '' : 'bg-zinc-50 opacity-60' }}">
                                    <td><span class="grid h-7 w-7 place-items-center rounded-md bg-zinc-100">{{ $status->sequence }}</span></td>
                                    <td><x-status-badge :status="$status" /><div class="mt-1 text-zinc-400">{{ $status->code }}</div></td>
                                    <td class="max-w-md text-zinc-600">{{ $status->description ?: 'Belum ada keterangan untuk customer.' }}</td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @if($status->is_initial)<span class="rounded bg-blue-50 px-2 py-1 text-xs text-blue-700">Awal</span>@endif
                                            @if($status->is_final)<span class="rounded bg-emerald-50 px-2 py-1 text-xs text-emerald-700">Final</span>@endif
                                            <span class="rounded bg-zinc-100 px-2 py-1 text-xs">{{ $status->status_type }}</span>
                                            @unless($status->is_active)<span class="rounded bg-red-50 px-2 py-1 text-xs text-red-700">Nonaktif</span>@endunless
                                        </div>
                                    </td>
                                    <td>{{ $usage }}×</td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <a class="order-table-action" href="{{ route('admin.order-statuses.edit', $status, false) }}">Edit</a>
                                            <form method="POST" action="{{ route('admin.order-statuses.destroy', $status, false) }}" onsubmit="return confirm('Hapus atau nonaktifkan status ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="order-table-action order-table-action-danger">{{ $usage ? 'Nonaktifkan' : 'Hapus' }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 bg-white py-12 text-center text-zinc-500">Tidak ada status yang cocok dengan filter.</div>
        @endforelse
    </div>
</x-layouts.app>
