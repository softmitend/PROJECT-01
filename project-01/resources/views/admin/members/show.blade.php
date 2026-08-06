<x-layouts.app title="Detail {{ $member->display_name }}">
    <x-page-heading title="{{ $member->display_name }}" description="{{ $member->member_code }}">
        <x-slot:action><a class="rounded-md border border-zinc-300 px-4 py-2 text-sm" href="{{ route('admin.members.edit', $member, false) }}">Edit</a></x-slot:action>
    </x-page-heading>

    <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-3 text-sm">
            <div><div class="text-zinc-500">Username</div><div>{{ $member->username ?: '-' }}</div></div>
            <div><div class="text-zinc-500">Grup</div><div>{{ $member->group_name ?: '-' }}</div></div>
            <div><div class="text-zinc-500">Status</div><div>{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
        </div>
        @if ($member->notes)<p class="mt-4 text-sm text-zinc-700">{{ $member->notes }}</p>@endif
    </div>

    <h2 class="mt-8 mb-3 font-semibold">Batch yang Diikuti</h2>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <tbody class="divide-y divide-zinc-100">
                @forelse ($member->orders as $order)
                    <tr><td class="px-4 py-3">{{ $order->batch->batch_number }}</td><td class="px-4 py-3"><x-status-badge :status="$order->effective_status" /></td><td class="px-4 py-3">{{ $order->items->count() }} item</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.member-orders.show', $order, false) }}">Buka</a></td></tr>
                @empty
                    <tr><td class="px-4 py-8 text-center text-zinc-500">Belum ada batch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
