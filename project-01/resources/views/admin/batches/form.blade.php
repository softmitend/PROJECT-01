@php
    $formTitle = $batch->exists ? 'Edit Batch' : 'Tambah Batch';
@endphp

<x-layouts.app :title="$formTitle">
    <x-admin-form-shell
        :title="$formTitle"
        eyebrow="Batch Pembelian"
        description="Atur identitas, periode, dan status utama batch pembelian merchandise."
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
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-text-input label="Nomor batch" name="batch_number" :value="$batch->batch_number" placeholder="Contoh: GO-SVT-2508" required />
                        <x-text-input label="Nama batch" name="batch_name" :value="$batch->batch_name" placeholder="Contoh: SEVENTEEN Album Weverse" />
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Progress Batch">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span>Status batch</span>
                            <select name="current_status_id">
                                <option value="">Belum ada status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" @selected(old('current_status_id', $batch->current_status_id) == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span>Catatan perubahan status</span>
                            <input name="status_note" value="{{ old('status_note') }}" placeholder="Opsional, jelaskan perubahan terbaru">
                        </label>
                        <x-text-input label="Tanggal mulai" name="started_at" type="datetime-local" :value="$batch->started_at?->format('Y-m-d\TH:i')" />
                        <x-text-input label="Tanggal selesai" name="completed_at" type="datetime-local" :value="$batch->completed_at?->format('Y-m-d\TH:i')" />
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Keterangan Internal">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span>Deskripsi</span>
                            <textarea name="description" rows="4" placeholder="Jelaskan cakupan atau tujuan batch">{{ old('description', $batch->description) }}</textarea>
                        </label>
                        <label class="block">
                            <span>Catatan admin</span>
                            <textarea name="notes" rows="4" placeholder="Catatan khusus yang hanya dilihat admin">{{ old('notes', $batch->notes) }}</textarea>
                        </label>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Pengarsipan">
                    <label class="admin-form-choice">
                        <input type="checkbox" name="is_archived" value="1" @checked(old('is_archived', $batch->is_archived))>
                        <span><strong>Arsipkan batch</strong><small>Batch tetap tersimpan tetapi dipisahkan dari daftar operasional aktif.</small></span>
                    </label>
                </x-admin-form-section>
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Pastikan status dan periode batch sesuai sebelum menyimpan perubahan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ route('admin.batches.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Batch</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>
</x-layouts.app>
