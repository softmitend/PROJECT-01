@props(['status' => null])

@if ($status)
    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ring-black/10" style="background-color: {{ $status->color }}14; color: {{ $status->color }}">
        <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $status->color }}"></span>
        {{ $status->name }}
    </span>
@else
    <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600 ring-1 ring-inset ring-zinc-200">
        <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span>
        Belum ada status
    </span>
@endif
