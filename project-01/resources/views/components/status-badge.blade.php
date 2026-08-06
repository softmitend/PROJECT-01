@props(['status' => null])

@if ($status)
    <span class="inline-flex items-center rounded px-2 py-1 text-xs font-medium ring-1 ring-inset ring-black/10" style="background-color: {{ $status->color }}20; color: {{ $status->color }}">
        {{ $status->name }}
    </span>
@else
    <span class="inline-flex items-center rounded bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600 ring-1 ring-inset ring-zinc-200">
        Belum ada status
    </span>
@endif
