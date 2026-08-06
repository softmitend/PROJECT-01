@props(['label', 'name', 'type' => 'text', 'value' => null])

<label class="block">
    <span class="text-sm font-medium text-zinc-700">{{ $label }}</span>
    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-zinc-900 focus:outline-none']) }}>
</label>
