@php
    $formTitle = $product->exists ? 'Edit Produk' : 'Tambah Produk';
@endphp

<x-layouts.app :title="$formTitle">
    <x-admin-form-shell
        :title="$formTitle"
        eyebrow="Katalog Produk"
        description="Kelola produk yang tersedia untuk dipilih pada form pembelian customer."
        max-width="max-w-3xl"
    >
        <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product, false) : route('admin.products.store', [], false) }}">
            @csrf
            @if ($product->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Katalog Produk"
                    description="Gunakan nama dan varian yang jelas agar admin dapat memilih produk dengan cepat saat mencatat pesanan."
                />

                <x-admin-form-section title="Identitas Produk">
                    <div class="grid gap-4 lg:grid-cols-3">
                        <x-text-input label="Nama produk" name="name" :value="$product->name" placeholder="Contoh: SEVENTEEN Album" required />
                        <x-text-input label="Varian" name="variant" :value="$product->variant" placeholder="Contoh: Carat Ver." />
                        <x-text-input label="Harga default" name="default_price" type="number" step="0.01" min="0" :value="$product->default_price" placeholder="0" />
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Keterangan Produk">
                    <label class="block">
                        <span>Deskripsi</span>
                        <textarea name="description" rows="4" placeholder="Jelaskan isi atau detail produk">{{ old('description', $product->description) }}</textarea>
                        <small class="admin-form-help">Informasi singkat untuk membantu admin membedakan produk.</small>
                    </label>
                </x-admin-form-section>

                @if($product->exists)
                    <x-admin-form-section title="Ketersediaan Produk">
                        <label class="admin-form-choice">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                            <span><strong>Produk aktif</strong><small>Produk aktif dapat dipilih ketika admin menyusun produk di dalam batch.</small></span>
                        </label>
                    </x-admin-form-section>
                @endif

            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Harga default dapat disesuaikan kembali pada setiap item pesanan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ $product->exists ? route('admin.products.show', $product, false) : route('admin.products.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Produk</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>
</x-layouts.app>
