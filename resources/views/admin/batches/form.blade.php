@php
    $formTitle = $batch->exists ? 'Edit Batch' : 'Tambah Batch';
    $productsAreLocked = $batch->exists && $batch->orders()->exists();
    $progressIsLocked = $batch->exists && $batch->progress_locked;
    $selectedProductIds = collect(old('product_ids', $batch->products?->pluck('id')->all() ?: []))
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values();
    $selectedProducts = $products->whereIn('id', $selectedProductIds);
@endphp

<x-layouts.app :title="$formTitle">
    <x-admin-form-shell
        :title="$formTitle"
        eyebrow="Batch Pembelian"
        description="Atur nama, periode, dan status utama batch pembelian merchandise."
        max-width="max-w-4xl"
    >
        <form method="POST" action="{{ $batch->exists ? route('admin.batches.update', $batch, false) : route('admin.batches.store', [], false) }}">
            @csrf
            @if ($batch->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Batch Pembelian"
                    description="Status batch menjadi progress utama seluruh pesanan di dalamnya selama pesanan tidak memiliki status khusus."
                />

                <x-admin-form-section title="Identitas Batch">
                    <div class="batch-form-identity">
                        <div class="batch-form-identity-code">
                            <span>{{ $batch->exists ? 'Nomor batch' : 'Nomor batch otomatis' }}</span>
                            <strong>{{ $batch->exists ? $batch->batch_number : 'BTH-'.now()->format('ym').'-XXXX' }}</strong>
                        </div>
                        <x-text-input label="Nama batch" name="batch_name" :value="$batch->batch_name" placeholder="Contoh: SEVENTEEN Album Weverse" />
                        <small class="admin-form-help">Nomor batch dibuat sistem dan tidak dapat diubah; gunakan nama batch sebagai identitas utama yang mudah dikenali.</small>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Produk dan Progress Batch">
                    <div class="grid items-start gap-4 lg:grid-cols-2">
                        @if($productsAreLocked)
                            <div class="admin-form-readonly">
                                <span>Produk dalam batch</span>
                                <strong>{{ $batch->products->map(fn ($product) => $product->name.($product->variant ? ' · '.$product->variant : ''))->join(', ') ?: 'Belum ada produk' }}</strong>
                                <small class="admin-form-help">Produk terkunci karena batch sudah memiliki pesanan.</small>
                            </div>
                        @else
                            <label class="block">
                                <span>Produk dalam batch</span>
                                <select data-batch-product-picker>
                                    <option value="">Pilih produk untuk ditambahkan</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-variant="{{ $product->variant }}" @disabled($selectedProductIds->contains($product->id))>
                                            {{ $product->name }}{{ $product->variant ? ' · '.$product->variant : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="admin-form-help">Pilih satu per satu; produk terpilih akan tampil di bawah form.</span>
                            </label>
                        @endif
                        @if($progressIsLocked)
                            <div class="admin-form-readonly">
                                <span>Status batch</span>
                                <strong>{{ $batch->currentStatus?->name ?: 'Status final' }}</strong>
                                <small class="admin-form-help">Progress telah selesai dan tidak dapat diubah lagi.</small>
                                <input type="hidden" name="current_status_id" value="{{ $batch->current_status_id }}">
                            </div>
                        @else
                            <label class="block">
                                <span>Status batch</span>
                                <select name="current_status_id">
                                    <option value="">Belum ada status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" @selected(old('current_status_id', $batch->current_status_id) == $status->id)>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        @endif
                    </div>
                    @unless($productsAreLocked)
                        <div class="batch-selected-products mt-4" data-batch-selected-products>
                            <div class="batch-selected-products-heading">
                                <span>Produk terpilih</span>
                                <small data-selected-product-count>{{ $selectedProducts->count() }} produk</small>
                            </div>
                            <div class="batch-selected-products-list" data-selected-product-list>
                                @foreach($selectedProducts as $product)
                                    <div class="batch-selected-product" data-selected-product data-product-id="{{ $product->id }}">
                                        <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <small>{{ $product->variant ?: 'Tanpa varian' }}</small>
                                        </div>
                                        <button type="button" data-remove-selected-product aria-label="Hapus {{ $product->name }}">Hapus</button>
                                    </div>
                                @endforeach
                            </div>
                            <p class="batch-selected-products-empty" data-selected-product-empty @if($selectedProducts->isNotEmpty()) hidden @endif>Belum ada produk yang dipilih.</p>
                        </div>
                    @endunless
                    @if($progressIsLocked)
                        <div class="admin-form-lock-notice mt-4">
                            <strong>Progress batch telah selesai</strong>
                            <p>Status {{ $batch->currentStatus?->name }} merupakan status final. Tidak ada perubahan progress lanjutan yang dapat dilakukan.</p>
                        </div>
                    @elseif($batch->exists)
                        <label class="mt-4 block">
                            <span>Catatan perubahan status</span>
                            <input name="status_note" value="{{ old('status_note') }}" placeholder="Opsional, jelaskan alasan perubahan progress terbaru">
                            <span class="admin-form-help">Catatan ini disimpan pada riwayat hanya ketika status batch berubah.</span>
                        </label>
                    @endif
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-text-input label="Tanggal mulai" name="started_at" type="datetime-local" :value="$batch->started_at?->format('Y-m-d\TH:i')" />
                        <x-text-input label="Tanggal selesai" name="completed_at" type="datetime-local" :value="$batch->completed_at?->format('Y-m-d\TH:i')" />
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Catatan Batch">
                    <label class="block">
                        <span>Catatan</span>
                        <textarea name="notes" rows="4" placeholder="Tambahkan informasi penting mengenai batch ini">{{ old('notes', $batch->notes) }}</textarea>
                        <span class="admin-form-help">Gunakan satu catatan ringkas agar informasi batch tidak tersebar pada banyak bidang.</span>
                    </label>
                </x-admin-form-section>

                @if($batch->exists)
                    <x-admin-form-section title="Pengarsipan">
                        <label class="admin-form-choice">
                            <input type="checkbox" name="is_archived" value="1" @checked(old('is_archived', $batch->is_archived))>
                            <span><strong>Arsipkan batch</strong><small>Batch tetap tersimpan tetapi dipisahkan dari daftar operasional aktif.</small></span>
                        </label>
                    </x-admin-form-section>
                @endif
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Pastikan status dan periode batch sesuai sebelum menyimpan perubahan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ $batch->exists ? route('admin.batches.show', $batch, false) : route('admin.batches.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Batch</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>
</x-layouts.app>
