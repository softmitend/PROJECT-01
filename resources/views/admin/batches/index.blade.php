<x-layouts.app title="Batch">
    <x-page-heading title="Batch" description="Kelola periode pembelian dan pantau progres setiap batch.">
        <x-slot:action><a class="admin-primary-action" href="{{ route('admin.batches.create', [], false) }}">+ Tambah batch</a></x-slot:action>
    </x-page-heading>
    <div class="order-table-card">
        <form class="order-table-toolbar">
            <input class="min-w-0 flex-1" name="q" value="{{ request('q') }}" placeholder="Cari nomor atau nama batch...">
            <select name="status_id" class="sm:max-w-56"><option value="">Semua status</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>@endforeach</select>
            <button class="order-table-toolbar-button" type="submit"><svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h14l-5.5 6v5l-3 1v-6L3 4Z"/></svg>Filter</button>
        </form>
        <div class="order-table-scroll">
            <table class="order-table">
                <thead><tr><th>Batch</th><th>Status</th><th>Pesanan</th><th>Item</th><th>Arsip</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                <tbody>
                    @forelse ($batches as $batch)
                        <tr>
                            <td><div class="order-table-primary">{{ $batch->batch_number }}</div><div class="order-table-secondary">{{ $batch->batch_name ?: 'Tanpa nama batch' }}</div></td>
                            <td><x-status-badge :status="$batch->currentStatus" /></td>
                            <td class="font-semibold text-zinc-700">{{ $batch->orders_count }}</td>
                            <td class="text-zinc-600">{{ $batch->items_count }}</td>
                            <td><span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $batch->is_archived ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">{{ $batch->is_archived ? 'Diarsipkan' : 'Aktif' }}</span></td>
                            <td class="text-right"><a class="order-table-action" href="{{ route('admin.batches.show', $batch, false) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-zinc-400">Belum ada batch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="order-table-footer"><span>Menampilkan {{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }} dari {{ $batches->total() }} batch</span><div>{{ $batches->links() }}</div></div>
    </div>
</x-layouts.app>
