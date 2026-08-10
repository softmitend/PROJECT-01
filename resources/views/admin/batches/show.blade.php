<x-layouts.app title="Batch {{ $batch->batch_number }}">
    <x-page-heading title="Batch {{ $batch->batch_number }}" description="{{ $batch->batch_name ?: 'Tanpa nama batch' }}">
        <x-slot:action><a class="rounded-md border border-zinc-300 px-4 py-2 text-sm" href="{{ route('admin.batches.edit', $batch, false) }}">Edit</a></x-slot:action>
    </x-page-heading>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
            <div class="text-sm text-zinc-500">Status batch saat ini</div>
            <div class="mt-2"><x-status-badge :status="$batch->currentStatus" /></div>
            @if($batch->notes)<p class="mt-3 text-sm">{{ $batch->notes }}</p>@endif
        </section>
        <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('admin.batches.status', $batch, false) }}" class="space-y-3">
                @csrf
                <label class="block"><span class="text-sm font-medium">Ubah status batch</span><select name="status_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach($statuses as $status)<option value="{{ $status->id }}">{{ $status->name }}</option>@endforeach</select></label>
                <input name="note" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" placeholder="Catatan perubahan">
                <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Update Status</button>
            </form>
        </section>
    </div>

    <section class="mt-6 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <h2 class="font-semibold">Tambahkan Member ke Batch</h2>
        <form method="POST" action="{{ route('admin.batches.members.store', $batch, false) }}" class="mt-3 flex gap-2">
            @csrf
            <select name="member_id" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach($members as $member)<option value="{{ $member->id }}">{{ $member->display_name }} · {{ $member->email ?: $member->member_code }}</option>@endforeach</select>
            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Tambah</button>
        </form>
    </section>

    <h2 class="mt-8 mb-3 font-semibold">Member dalam Batch</h2>
    <div class="order-table-card">
        <div class="order-table-scroll">
        <table class="order-table">
            <thead><tr><th>Pelanggan</th><th>Item</th><th>Status efektif</th><th>Update terakhir</th><th><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
                @forelse($batch->orders as $order)
                    <tr><td><div class="order-table-primary">{{ $order->member->display_name }}</div><div class="order-table-secondary">#{{ $order->member->member_code }}</div></td><td class="font-semibold text-zinc-700">{{ $order->items->sum('quantity') }}</td><td><x-status-badge :status="$order->effective_status" /></td><td><div class="text-zinc-700">{{ $order->updated_at->format('d M Y') }}</div><div class="order-table-secondary">{{ $order->updated_at->format('H:i') }}</div></td><td class="text-right"><a class="order-table-action" href="{{ route('admin.member-orders.show', $order, false) }}">Pesanan</a></td></tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-500">Belum ada member pada batch ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-layouts.app>
