<x-layouts.app title="Riwayat Status">
    <x-page-heading title="Riwayat Status" description="Audit trail perubahan status batch, pesanan member, dan item jajanan." />
    <form class="mb-4 grid gap-2 sm:grid-cols-3">
        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="date_from" type="date" value="{{ request('date_from') }}">
        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="date_to" type="date" value="{{ request('date_to') }}">
        <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Filter</button>
    </form>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Objek</th><th class="px-4 py-3">Lama</th><th class="px-4 py-3">Baru</th><th class="px-4 py-3">Catatan</th><th class="px-4 py-3">Admin</th><th class="px-4 py-3">Waktu</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($histories as $history)
                    <tr><td class="px-4 py-3">{{ class_basename($history->trackable_type) }} #{{ $history->trackable_id }}</td><td class="px-4 py-3"><x-status-badge :status="$history->oldStatus" /></td><td class="px-4 py-3"><x-status-badge :status="$history->newStatus" /></td><td class="px-4 py-3">{{ $history->note ?: '-' }}</td><td class="px-4 py-3">{{ $history->changedBy?->name ?: 'Sistem' }}</td><td class="px-4 py-3">{{ $history->created_at->format('d M Y H:i') }}</td></tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-zinc-500">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $histories->links() }}</div>
</x-layouts.app>
