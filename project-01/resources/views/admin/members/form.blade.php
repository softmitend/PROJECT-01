<x-layouts.app title="{{ $member->exists ? 'Edit Member' : 'Tambah Member' }}">
    <x-page-heading title="{{ $member->exists ? 'Edit Member' : 'Tambah Member' }}" />
    <form method="POST" action="{{ $member->exists ? route('admin.members.update', $member, false) : route('admin.members.store', [], false) }}" class="max-w-2xl space-y-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($member->exists) @method('PUT') @endif
        <x-text-input label="Kode member" name="member_code" :value="$member->member_code" required />
        <x-text-input label="Nama tampil" name="display_name" :value="$member->display_name" required />
        <x-text-input label="Username" name="username" :value="$member->username" />
        <x-text-input label="Access code" name="access_code" :value="$member->access_code" />
        <x-text-input label="Grup" name="group_name" :value="$member->group_name" />
        <label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="notes" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('notes', $member->notes) }}</textarea></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active ?? true))> Aktif</label>
        <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Simpan</button>
    </form>
</x-layouts.app>
