<x-layouts.app title="Riwayat Status">
    <x-page-heading title="Riwayat Status" description="Audit trail perubahan status batch, pesanan, dan item produk." />
    <div class="order-table-card">
        <form class="order-table-toolbar">
            <input class="sm:max-w-52" name="date_from" type="date" value="{{ request('date_from') }}" aria-label="Tanggal mulai">
            <span class="hidden text-xs font-medium text-zinc-400 sm:inline">sampai</span>
            <input class="sm:max-w-52" name="date_to" type="date" value="{{ request('date_to') }}" aria-label="Tanggal akhir">
            <button class="order-table-toolbar-button" type="submit"><svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h14l-5.5 6v5l-3 1v-6L3 4Z"/></svg>Filter tanggal</button>
        </form>
        <div class="order-table-scroll">
            <table class="order-table">
                <thead><tr><th>Objek</th><th>Status lama</th><th>Status baru</th><th>Catatan</th><th>Admin</th><th>Waktu</th></tr></thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr>
                            <td><div class="order-table-primary">{{ class_basename($history->trackable_type) }}</div><div class="order-table-secondary">#{{ $history->trackable_id }}</div></td>
                            <td><x-status-badge :status="$history->oldStatus" /></td>
                            <td><x-status-badge :status="$history->newStatus" /></td>
                            <td class="max-w-xs text-zinc-600">{{ $history->note ?: '-' }}</td>
                            <td class="text-zinc-600">{{ $history->changedBy?->name ?: 'Sistem' }}</td>
                            <td class="whitespace-nowrap"><div class="text-zinc-700">{{ $history->created_at->format('d M Y') }}</div><div class="order-table-secondary">{{ $history->created_at->format('H:i') }}</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-zinc-400">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="order-table-footer"><span>Menampilkan {{ $histories->firstItem() ?? 0 }}–{{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} aktivitas</span><div>{{ $histories->links() }}</div></div>
    </div>
</x-layouts.app>
