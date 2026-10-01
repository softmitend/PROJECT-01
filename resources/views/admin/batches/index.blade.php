<x-layouts.app title="Batch">
    <x-page-heading title="Batch" description="Kelola periode pembelian dan pantau progres setiap batch.">
        <x-slot:action>
            <div class="flex flex-wrap gap-2">
                <a class="admin-primary-action" href="{{ route('admin.batches.create', [], false) }}">+ Tambah batch</a>
                <a class="admin-form-secondary" href="{{ route('admin.batches.settings', [], false) }}">Pengaturan</a>
            </div>
        </x-slot:action>
    </x-page-heading>
    <div class="status-manager-card status-folder-map">
        <form class="order-table-toolbar status-manager-toolbar" data-auto-filter>
            <input type="hidden" name="view" value="{{ $archiveView }}">
            <input class="min-w-0 flex-1" name="q" value="{{ request('q') }}" placeholder="Cari nomor atau nama batch...">
            <select name="status_id" class="sm:max-w-56"><option value="">Semua status</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>@endforeach</select>
            <div class="date-range-filter">
                <label><span>Dari</span><input name="date_from" type="date" value="{{ request('date_from') }}" aria-label="Tanggal batch mulai"></label>
                <i aria-hidden="true">—</i>
                <label><span>Sampai</span><input name="date_to" type="date" value="{{ request('date_to') }}" aria-label="Tanggal batch akhir"></label>
            </div>
            <button class="order-table-toolbar-button" type="submit"><svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="9" r="5.5"/><path d="m13 13 4 4"/></svg>Cari</button>
        </form>

        <div class="status-folder-tabs" role="tablist" aria-label="Kategori batch">
            @foreach([
                'active' => ['label' => 'Batch Aktif', 'count' => $batchCounts['active']],
                'archived' => ['label' => 'Arsip', 'count' => $batchCounts['archived']],
            ] as $view => $tab)
                <a
                    href="{{ route('admin.batches.index', ['view' => $view], false) }}"
                    role="tab"
                    class="status-folder-tab {{ $archiveView === $view ? 'is-active' : '' }}"
                    aria-selected="{{ $archiveView === $view ? 'true' : 'false' }}"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6.75A1.75 1.75 0 0 1 4.75 5h4.1l1.8 2h8.6A1.75 1.75 0 0 1 21 8.75v8.5A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z"/></svg>
                    <span>{{ $tab['label'] }}</span>
                    <small>{{ $tab['count'] }}</small>
                </a>
            @endforeach
            <a
                href="{{ route('admin.batches.settings', [], false) }}"
                role="tab"
                class="status-folder-tab"
                aria-selected="false"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan</span>
            </a>
        </div>

        <div class="status-folder-content">
            <section class="status-folder-panel">
                <div class="status-folder-heading">
                    <div>
                        <h2>{{ $archiveView === 'archived' ? 'Batch yang Diarsipkan' : 'Batch Operasional Aktif' }}</h2>
                        <p>{{ $archiveView === 'archived' ? 'Batch yang tidak lagi tampil pada daftar operasional utama.' : 'Batch yang masih tersedia untuk kebutuhan operasional dan pembuatan pesanan.' }}</p>
                    </div>
                    <span>{{ $batches->total() }} batch</span>
                </div>

                <div class="status-folder-application">
                    <span class="status-folder-application-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="7.5"/><path d="M10 8.5v5M10 6.25h.01"/></svg>
                    </span>
                    <div>
                        <strong>{{ $archiveView === 'archived' ? 'Folder arsip batch' : 'Folder batch aktif' }}</strong>
                        <p>{{ $archiveView === 'archived' ? 'Data di sini dipisahkan dari daftar utama dan hanya tersedia sebagai referensi readonly.' : 'Batch yang diarsipkan tidak akan muncul pada folder ini.' }}</p>
                    </div>
                </div>

                <div class="status-table-frame">
                    <div class="order-table-scroll">
                        <table class="order-table responsive-card-table">
                            <thead><tr><th>Batch</th><th>Status</th><th>Pesanan</th><th>Item</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                            <tbody>
                                @forelse ($batches as $batch)
                                    <tr>
                                        <td data-label="Batch"><div class="order-table-primary">{{ $batch->batch_number }}</div><div class="order-table-secondary">{{ $batch->batch_name ?: 'Tanpa nama batch' }}</div></td>
                                        <td data-label="Status"><x-status-badge :status="$batch->currentStatus" /></td>
                                        <td data-label="Pesanan" class="font-semibold text-zinc-700">{{ $batch->orders_count }}</td>
                                        <td data-label="Item" class="text-zinc-600">{{ $batch->items_count }}</td>
                                        <td data-card-action class="text-right"><a class="order-table-action" href="{{ route('admin.batches.show', $batch, false) }}">Detail</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-12 text-center text-zinc-400">{{ $archiveView === 'archived' ? 'Belum ada batch yang diarsipkan.' : 'Tidak ada batch aktif yang cocok dengan pencarian atau filter.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="order-table-footer"><span>Menampilkan {{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }} dari {{ $batches->total() }} batch</span><div>{{ $batches->links() }}</div></div>
            </section>
        </div>
    </div>
</x-layouts.app>