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
                    description="Pastikan username LINE dan nomor telepon benar karena username LINE menjadi identitas pencarian riwayat customer."
                />

                <x-admin-form-section title="Identitas Pelanggan">
                    <div class="grid gap-4 {{ $member->exists ? 'sm:grid-cols-2' : 'lg:grid-cols-3' }}">
                        <x-text-input label="Nama pelanggan" name="display_name" :value="$member->display_name" placeholder="Nama lengkap atau nama penerima" required />
                        <x-text-input label="Username LINE" name="username" :value="$member->username" placeholder="username_line" required />
                        <x-text-input label="Nomor telepon" name="phone" type="tel" :value="$member->phone" placeholder="08xxxxxxxxxx" required />
                        @if ($member->exists)
                            <label class="block">
                                <span class="text-sm font-semibold text-zinc-800">Kode pelanggan</span>
                                <input type="text" value="{{ $member->member_code }}" readonly aria-readonly="true" class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600 shadow-sm outline-none">
                            </label>
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

                @if($member->exists)
                    <x-admin-form-section title="Ketersediaan">
                        <label class="admin-form-choice">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active))>
                            <span><strong>Pelanggan aktif</strong><small>Pelanggan tersedia pada dropdown form penambahan pesanan.</small></span>
                        </label>
                    </x-admin-form-section>
                @endif
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Periksa kembali username LINE, nomor telepon, dan alamat sebelum data disimpan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ $member->exists ? route('admin.members.show', $member, false) : route('admin.members.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Pelanggan</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>
</x-layouts.app>
