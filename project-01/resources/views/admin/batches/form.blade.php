<x-layouts.app title="{{ $batch->exists ? 'Edit Batch' : 'Tambah Batch' }}">
    <x-page-heading title="{{ $batch->exists ? 'Edit Batch' : 'Tambah Batch' }}" />
    <form method="POST" action="{{ $batch->exists ? route('admin.batches.update', $batch, false) : route('admin.batches.store', [], false) }}" class="max-w-2xl space-y-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($batch->exists) @method('PUT') @endif
        <x-text-input label="Nomor batch" name="batch_number" :value="$batch->batch_number" required />
        <x-text-input label="Nama batch" name="batch_name" :value="$batch->batch_name" />
        <label class="block"><span class="text-sm font-medium">Status batch</span><select name="current_status_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"><option value="">Belum ada status</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected(old('current_status_id', $batch->current_status_id) == $status->id)>{{ $status->name }}</option>@endforeach</select></label>
        <label class="block"><span class="text-sm font-medium">Catatan perubahan status</span><input name="status_note" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"></label>
        <x-text-input label="Mulai" name="started_at" type="datetime-local" :value="$batch->started_at?->format('Y-m-d\\TH:i')" />
        <x-text-input label="Selesai" name="completed_at" type="datetime-local" :value="$batch->completed_at?->format('Y-m-d\\TH:i')" />
        <label class="block"><span class="text-sm font-medium">Deskripsi</span><textarea name="description" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('description', $batch->description) }}</textarea></label>
        <label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="notes" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('notes', $batch->notes) }}</textarea></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_archived" value="1" @checked(old('is_archived', $batch->is_archived))> Arsip</label>
        <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Simpan</button>
    </form>
</x-layouts.app>
