<x-layouts.app title="Jajanan">
    <x-page-heading title="Jajanan" description="Master produk untuk membantu input; item pesanan tetap menyimpan snapshot.">
        <x-slot:action><a class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('admin.products.create', [], false) }}">Tambah Jajanan</a></x-slot:action>
    </x-page-heading>
    <form class="mb-4 flex gap-2"><input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" name="q" value="{{ request('q') }}" placeholder="Cari nama atau varian"><button class="rounded-md border border-zinc-300 px-4 py-2 text-sm">Cari</button></form>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-100 text-left text-xs uppercase text-zinc-500"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Varian</th><th class="px-4 py-3">Harga default</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($products as $product)
                    <tr><td class="px-4 py-3 font-medium">{{ $product->name }}</td><td class="px-4 py-3">{{ $product->variant ?: '-' }}</td><td class="px-4 py-3">{{ $product->default_price ? 'Rp '.number_format($product->default_price, 0, ',', '.') : '-' }}</td><td class="px-4 py-3">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.products.edit', $product, false) }}">Edit</a></td></tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-500">Belum ada jajanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-layouts.app>
