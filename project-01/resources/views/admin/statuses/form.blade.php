<x-layouts.app title="{{ $orderStatus->exists ? 'Edit Status' : 'Tambah Status' }}">
    <x-page-heading title="{{ $orderStatus->exists ? 'Edit Status' : 'Tambah Status' }}" />
    <form method="POST" action="{{ $orderStatus->exists ? route('admin.order-statuses.update', $orderStatus, false) : route('admin.order-statuses.store', [], false) }}" class="max-w-2xl space-y-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($orderStatus->exists) @method('PUT') @endif
        <x-text-input label="Nama" name="name" :value="$orderStatus->name" required />
        <x-text-input label="Code" name="code" :value="$orderStatus->code" required />
        <x-text-input label="Warna badge" name="color" type="color" :value="$orderStatus->color ?: '#64748b'" required />
        <x-text-input label="Urutan" name="sequence" type="number" min="0" :value="$orderStatus->sequence ?? 0" required />
        <label class="block"><span class="text-sm font-medium">Jenis</span><select name="status_type" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach (\App\Models\OrderStatus::TYPES as $type)<option value="{{ $type }}" @selected(old('status_type', $orderStatus->status_type) === $type)>{{ $type }}</option>@endforeach</select></label>
        <label class="block"><span class="text-sm font-medium">Scope</span><select name="scope" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach (\App\Models\OrderStatus::SCOPES as $scope)<option value="{{ $scope }}" @selected(old('scope', $orderStatus->scope) === $scope)>{{ $scope }}</option>@endforeach</select></label>
        <label class="block"><span class="text-sm font-medium">Deskripsi</span><textarea name="description" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('description', $orderStatus->description) }}</textarea></label>
        <div class="flex flex-wrap gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_initial" value="1" @checked(old('is_initial', $orderStatus->is_initial))> Status awal</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_final" value="1" @checked(old('is_final', $orderStatus->is_final))> Status final</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $orderStatus->is_active ?? true))> Aktif</label>
        </div>
        <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Simpan</button>
    </form>
</x-layouts.app>
