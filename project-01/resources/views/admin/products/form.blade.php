<x-layouts.app title="{{ $product->exists ? 'Edit Jajanan' : 'Tambah Jajanan' }}">
    <x-page-heading title="{{ $product->exists ? 'Edit Jajanan' : 'Tambah Jajanan' }}" />
    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product, false) : route('admin.products.store', [], false) }}" class="max-w-2xl space-y-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($product->exists) @method('PUT') @endif
        <x-text-input label="Nama" name="name" :value="$product->name" required />
        <x-text-input label="Varian" name="variant" :value="$product->variant" />
        <x-text-input label="Harga default" name="default_price" type="number" step="0.01" min="0" :value="$product->default_price" />
        <label class="block"><span class="text-sm font-medium">Deskripsi</span><textarea name="description" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('description', $product->description) }}</textarea></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Aktif</label>
        <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Simpan</button>
    </form>
</x-layouts.app>
