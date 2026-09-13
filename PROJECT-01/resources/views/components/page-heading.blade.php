@props(['title', 'description' => null, 'action' => null])

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $description }}</p>
        @endif
    </div>
    @if ($action)
        <div>{{ $action }}</div>
    @endif
</div>
