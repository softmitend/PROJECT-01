<x-layouts.app title="Batch">
    <x-page-heading title="Batch" description="Kelola nomor batch, status batch, dan member yang ikut batch.">
        <x-slot:action><a class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('admin.batches.create', [], false) }}">Tambah Batch</a></x-slot:action>
    </x-page-heading>
    <form class="mb-4 grid gap-2 sm:grid-cols-[1fr_220px_auto]">
        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="q" value="{{ request('q') }}" placeholder="Cari nomor atau nama batch">
        <select name="status_id" class="rounded-md border border-zinc-300 px-3 py-2 text-sm"><option value="">Semua status</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>@endforeach</select>
        <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Filter</button>
    </form>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Batch</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Pesanan</th><th class="px-4 py-3">Item</th><th class="px-4 py-3">Arsip</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($batches as $batch)
                    <tr><td class="px-4 py-3"><div class="font-medium">{{ $batch->batch_number }}</div><div class="text-zinc-500">{{ $batch->batch_name }}</div></td><td class="px-4 py-3"><x-status-badge :status="$batch->currentStatus" /></td><td class="px-4 py-3">{{ $batch->orders_count }}</td><td class="px-4 py-3">{{ $batch->items_count }}</td><td class="px-4 py-3">{{ $batch->is_archived ? 'Ya' : 'Tidak' }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.batches.show', $batch, false) }}">Detail</a></td></tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-zinc-500">Belum ada batch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $batches->links() }}</div>
</x-layouts.app>
