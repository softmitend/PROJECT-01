<x-layouts.app title="Rekap {{ $member->display_name }}">
    <x-page-heading title="{{ $member->display_name }}" description="Kode member: {{ $member->member_code }}" />

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500">
                    <tr>
                        <th class="px-4 py-3">Batch</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Jumlah jajanan</th>
                        <th class="px-4 py-3">Update terakhir</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($member->orders as $order)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $order->batch->batch_number }}</div>
                                <div class="text-zinc-500">{{ $order->batch->batch_name }}</div>
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$order->effective_status" /></td>
                            <td class="px-4 py-3">{{ $order->items->sum('quantity') }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $order->updated_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a class="rounded-md border border-zinc-300 px-3 py-2" href="/tracking/{{ $member->member_code }}/orders/{{ $order->id }}">Buka</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-500">Belum ada batch yang diikuti.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
