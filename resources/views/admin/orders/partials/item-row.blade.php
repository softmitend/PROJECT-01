<div data-item-row data-product-id="{{ $item['product_id'] ?? '' }}" class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-4">
    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
    <input data-item-name type="hidden" name="items[{{ $index }}][item_name]" value="{{ $item['item_name'] ?? '' }}">
    <input data-item-variant type="hidden" name="items[{{ $index }}][variant]" value="{{ $item['variant'] ?? '' }}">

    <div class="grid gap-3 md:grid-cols-[2fr_.6fr_1fr_1.2fr_auto] md:items-end">
        <label class="block">
            <span class="text-xs font-medium text-zinc-600">Produk</span>
            <select data-product-select name="items[{{ $index }}][product_id]" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm" required>
                <option value="">Pilih produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-variant="{{ $product->variant }}"
                        data-price="{{ $product->default_price }}"
                        @selected(($item['product_id'] ?? null) == $product->id)>
                        {{ $product->name }}{{ $product->variant ? ' · '.$product->variant : '' }}
                    </option>
                @endforeach
            </select>
        </label>
        <label class="block"><span class="text-xs font-medium text-zinc-600">Qty</span><input data-item-qty name="items[{{ $index }}][quantity]" type="number" min="1" value="{{ $item['quantity'] ?? 1 }}" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm" required></label>
        <label class="block"><span class="text-xs font-medium text-zinc-600">Harga satuan</span><input data-item-price name="items[{{ $index }}][unit_price]" type="number" min="0" step="0.01" value="{{ $item['unit_price'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm"></label>
        <label class="block"><span class="text-xs font-medium text-zinc-600">Status item</span><select name="items[{{ $index }}][override_status_id]" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Ikuti pesanan</option>@foreach($itemStatuses as $status)<option value="{{ $status->id }}" @selected(($item['override_status_id'] ?? null) == $status->id)>{{ $status->name }}</option>@endforeach</select></label>
        <button type="button" data-remove-item class="admin-form-danger-action">Hapus</button>
    </div>
    <label class="mt-3 block"><span class="text-xs font-medium text-zinc-600">Catatan item</span><input name="items[{{ $index }}][notes]" value="{{ $item['notes'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm" placeholder="Opsional, misalnya preferensi member atau kondisi khusus"></label>
</div>
