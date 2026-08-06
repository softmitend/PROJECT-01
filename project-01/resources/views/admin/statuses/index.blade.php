<x-layouts.app title="Status Pesanan">
    <x-page-heading title="Status Pesanan" description="Status bersifat dinamis; code stabil dan tidak ikut berubah saat nama diedit.">
        <x-slot:action><a class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('admin.order-statuses.create', [], false) }}">Tambah Status</a></x-slot:action>
    </x-page-heading>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Status</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Scope</th><th class="px-4 py-3">Urutan</th><th class="px-4 py-3">Flag</th><th class="px-4 py-3">Penggunaan</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($statuses as $status)
                    @php($usage = $status->batches_count + $status->member_orders_count + $status->order_items_count + $status->old_histories_count + $status->new_histories_count)
                    <tr>
                        <td class="px-4 py-3"><x-status-badge :status="$status" /></td><td class="px-4 py-3 font-mono text-xs">{{ $status->code }}</td><td class="px-4 py-3">{{ $status->status_type }}</td><td class="px-4 py-3">{{ $status->scope }}</td><td class="px-4 py-3">{{ $status->sequence }}</td><td class="px-4 py-3">{{ $status->is_initial ? 'Awal ' : '' }}{{ $status->is_final ? 'Final ' : '' }}{{ $status->is_active ? 'Aktif' : 'Nonaktif' }}</td><td class="px-4 py-3">{{ $usage }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.order-statuses.edit', $status, false) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $statuses->links() }}</div>
</x-layouts.app>
