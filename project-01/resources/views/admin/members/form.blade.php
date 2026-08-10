@php
    $formTitle = $member->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan';
@endphp

<x-layouts.app :title="$formTitle">
    <x-admin-form-shell
        :title="$formTitle"
        eyebrow="Data Pelanggan"
        description="Kelola identitas dan alamat pelanggan yang akan dipilih saat membuat pesanan."
        max-width="max-w-4xl"
    >
        <form method="POST" action="{{ $member->exists ? route('admin.members.update', $member, false) : route('admin.members.store', [], false) }}">
            @csrf
            @if ($member->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Data Pelanggan"
                    description="Pastikan email dan nomor telepon aktif karena data ini menjadi identitas pencarian riwayat customer."
                />

                <x-admin-form-section title="Identitas Pelanggan">
                    <div class="grid gap-4 {{ $member->exists ? 'sm:grid-cols-2' : 'lg:grid-cols-3' }}">
                        <x-text-input label="Nama pelanggan" name="display_name" :value="$member->display_name" placeholder="Nama lengkap atau nama penerima" required />
                        <x-text-input label="Email" name="email" type="email" :value="$member->email" placeholder="nama@email.com" required />
                        <x-text-input label="Nomor telepon" name="phone" type="tel" :value="$member->phone" placeholder="08xxxxxxxxxx" required />
                        @if ($member->exists)
                            <div class="admin-form-readonly"><span>Kode internal</span>{{ $member->member_code }}</div>
                        @endif
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Alamat dan Catatan">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span>Alamat lengkap</span>
                            <textarea name="address" rows="4" placeholder="Nama penerima, jalan, kecamatan, kota, provinsi, dan kode pos" required>{{ old('address', $member->address) }}</textarea>
                            <small class="admin-form-help">Gunakan alamat lengkap yang siap dipakai untuk kebutuhan pengiriman.</small>
                        </label>
                        <label class="block">
                            <span>Catatan admin</span>
                            <textarea name="notes" rows="3" placeholder="Opsional dan tidak ditampilkan kepada pelanggan">{{ old('notes', $member->notes) }}</textarea>
                            <small class="admin-form-help">Catatan ini hanya dapat dilihat oleh admin.</small>
                        </label>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Ketersediaan">
                    <label class="admin-form-choice">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active ?? true))>
                        <span><strong>Pelanggan aktif</strong><small>Pelanggan tersedia pada dropdown form penambahan pesanan.</small></span>
                    </label>
                </x-admin-form-section>
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Periksa kembali email, nomor telepon, dan alamat sebelum data disimpan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ route('admin.members.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Pelanggan</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>
</x-layouts.app>
