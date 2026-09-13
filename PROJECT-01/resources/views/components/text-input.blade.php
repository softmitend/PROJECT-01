@props(['label', 'name', 'type' => 'text', 'value' => null])

<label class="block">
    <span class="text-sm font-semibold text-zinc-800">{{ $label }}</span>
    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition placeholder:text-zinc-400 hover:border-zinc-300 focus:border-violet-500 focus:ring-4 focus:ring-violet-100']) }}>
</label>
