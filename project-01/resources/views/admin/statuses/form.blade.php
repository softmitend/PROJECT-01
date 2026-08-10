@php
    $formTitle = $orderStatus->exists ? 'Edit Status' : 'Tambah Status';
@endphp

<x-layouts.app :title="$formTitle">
    <x-admin-form-shell
        :title="$formTitle"
        eyebrow="Status Pesanan"
        description="Susun status yang akan tampil pada progress batch, pesanan, item, dan pembayaran customer."
        max-width="max-w-4xl"
    >
        <form method="POST" action="{{ $orderStatus->exists ? route('admin.order-statuses.update', $orderStatus, false) : route('admin.order-statuses.store', [], false) }}">
            @csrf
            @if ($orderStatus->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Status"
                    description="Gunakan nama yang mudah dimengerti customer dan code yang stabil karena code dipakai sebagai referensi sistem."
                />

                <x-admin-form-section title="Identitas Status">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-text-input label="Nama status untuk customer" name="name" :value="$orderStatus->name" placeholder="Contoh: Arrived Admin" required />
                        <div>
                            <x-text-input label="Code sistem" name="code" :value="$orderStatus->code" placeholder="arrived-admin" required />
                            <small class="admin-form-help">Gunakan huruf kecil serta tanda hubung.</small>
                        </div>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Konfigurasi Tampilan">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <x-text-input label="Warna badge" name="color" type="color" :value="$orderStatus->color ?: '#7c3aed'" required />
                        <x-text-input label="Nomor urutan" name="sequence" type="number" min="0" :value="$orderStatus->sequence ?? 10" required />
                        <label class="block">
                            <span>Jenis hasil</span>
                            <select name="status_type" required>
                                @foreach (['process' => 'Sedang diproses', 'success' => 'Berhasil / selesai', 'failed' => 'Bermasalah', 'cancelled' => 'Dibatalkan'] as $type => $label)
                                    <option value="{{ $type }}" @selected(old('status_type', $orderStatus->status_type ?: 'process') === $type)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Cakupan Status">
                    <label class="block">
                        <span>Berlaku untuk</span>
                        <select name="scope">
                            @foreach (['batch' => 'Batch — progress utama seluruh customer', 'member_order' => 'Pesanan — kondisi khusus satu customer', 'order_item' => 'Item — kondisi khusus satu produk', 'payment' => 'Pembayaran — status pembayaran customer', 'all' => 'Semua — dapat digunakan di semua tingkat progress'] as $scope => $label)
                                <option value="{{ $scope }}" @selected(old('scope', $orderStatus->scope ?: 'batch') === $scope)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="admin-form-help">Cakupan menentukan lokasi status dapat dipilih oleh admin.</small>
                    </label>
                </x-admin-form-section>

                <x-admin-form-section title="Keterangan Customer">
                    <label class="block">
                        <span>Keterangan yang mudah dipahami customer</span>
                        <textarea name="description" rows="3" placeholder="Jelaskan apa yang sedang terjadi dan langkah berikutnya">{{ old('description', $orderStatus->description) }}</textarea>
                    </label>
                </x-admin-form-section>

                <x-admin-form-section title="Perilaku Status">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <label class="admin-form-choice">
                            <input type="checkbox" name="is_initial" value="1" @checked(old('is_initial', $orderStatus->is_initial))>
                            <span><strong>Status awal</strong><small>Tahap pertama pada cakupan ini.</small></span>
                        </label>
                        <label class="admin-form-choice">
                            <input type="checkbox" name="is_final" value="1" @checked(old('is_final', $orderStatus->is_final))>
                            <span><strong>Status final</strong><small>Tidak ada progress lanjutan.</small></span>
                        </label>
                        <label class="admin-form-choice">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $orderStatus->is_active ?? true))>
                            <span><strong>Status aktif</strong><small>Tersedia pada dropdown admin.</small></span>
                        </label>
                    </div>
                </x-admin-form-section>
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Perubahan status dapat memengaruhi pilihan progress pada batch dan pesanan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ route('admin.order-statuses.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Status</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const name = document.querySelector('[name="name"]');
            const code = document.querySelector('[name="code"]');
            let codeWasEdited = code.value.length > 0;
            code.addEventListener('input', () => codeWasEdited = true);
            name.addEventListener('input', () => {
                if (codeWasEdited) return;
                code.value = name.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            });
        });
    </script>
</x-layouts.app>
