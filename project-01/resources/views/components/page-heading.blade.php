@props(['title', 'description' => null, 'action' => null])

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-zinc-600">{{ $description }}</p>
        @endif
    </div>
    @if ($action)
        <div>{{ $action }}</div>
    @endif
</div>
