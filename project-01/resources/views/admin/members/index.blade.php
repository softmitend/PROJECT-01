<x-layouts.app title="Member">
    <x-page-heading title="Member" description="Kelola data member tanpa memakai nama sebagai identifier utama.">
        <x-slot:action><a class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('admin.members.create', [], false) }}">Tambah Member</a></x-slot:action>
    </x-page-heading>

    <form class="mb-4 flex gap-2">
        <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, username, grup">
        <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Cari</button>
    </form>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Grup</th><th class="px-4 py-3">Pesanan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($members as $member)
                    <tr>
                        <td class="px-4 py-3"><div class="font-medium">{{ $member->display_name }}</div><div class="text-zinc-500">{{ $member->member_code }}</div></td>
                        <td class="px-4 py-3">{{ $member->username ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $member->group_name ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $member->orders_count }}</td>
                        <td class="px-4 py-3">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="px-4 py-3 text-right"><a class="rounded-md border border-zinc-300 px-3 py-2" href="{{ route('admin.members.show', $member, false) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-zinc-500">Belum ada member.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $members->links() }}</div>
</x-layouts.app>
