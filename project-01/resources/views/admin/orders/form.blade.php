<x-layouts.app title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}">
    <x-page-heading title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}" />
    <form method="POST" action="{{ $order->exists ? route('admin.member-orders.update', $order, false) : route('admin.member-orders.store', [], false) }}" class="space-y-5">
        @csrf
        @if($order->exists) @method('PUT') @endif
        <section class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm md:grid-cols-2">
            <x-text-input label="Kode order" name="order_code" :value="$order->order_code ?: 'ORD-'.now()->format('YmdHis')" required />
            <label class="block"><span class="text-sm font-medium">Member</span><select name="member_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach($members as $member)<option value="{{ $member->id }}" @selected(old('member_id', $order->member_id) == $member->id)>{{ $member->display_name }} · {{ $member->member_code }}</option>@endforeach</select></label>
            <label class="block"><span class="text-sm font-medium">Batch</span><select name="batch_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">@foreach($batches as $batch)<option value="{{ $batch->id }}" @selected(old('batch_id', $order->batch_id) == $batch->id)>{{ $batch->batch_number }}</option>@endforeach</select></label>
            <label class="block"><span class="text-sm font-medium">Override status pesanan</span><select name="override_status_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"><option value="">Ikuti status batch</option>@foreach($orderStatuses as $status)<option value="{{ $status->id }}" @selected(old('override_status_id', $order->override_status_id) == $status->id)>{{ $status->name }}</option>@endforeach</select></label>
            <x-text-input label="Status pembayaran" name="payment_status" :value="$order->payment_status" />
            <label class="block md:col-span-2"><span class="text-sm font-medium">Catatan</span><textarea name="notes" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('notes', $order->notes) }}</textarea></label>
        </section>

        <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="mb-3 font-semibold">Item Jajanan</div>
            @php($rows = old('items', $order->items?->toArray() ?: [['quantity' => 1], ['quantity' => 1], ['quantity' => 1]]))
            <div class="space-y-3">
                @foreach($rows as $i => $item)
                    <div class="grid gap-3 rounded-md border border-zinc-200 p-3 md:grid-cols-6">
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item['id'] ?? '' }}">
                        <label class="block md:col-span-2"><span class="text-xs font-medium text-zinc-600">Nama item</span><input name="items[{{ $i }}][item_name]" value="{{ $item['item_name'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" {{ $i === 0 ? 'required' : '' }}></label>
                        <label class="block"><span class="text-xs font-medium text-zinc-600">Varian</span><input name="items[{{ $i }}][variant]" value="{{ $item['variant'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"></label>
                        <label class="block"><span class="text-xs font-medium text-zinc-600">Qty</span><input name="items[{{ $i }}][quantity]" type="number" min="1" value="{{ $item['quantity'] ?? 1 }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"></label>
                        <label class="block"><span class="text-xs font-medium text-zinc-600">Harga</span><input name="items[{{ $i }}][unit_price]" type="number" min="0" step="0.01" value="{{ $item['unit_price'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"></label>
                        <label class="block"><span class="text-xs font-medium text-zinc-600">Status item</span><select name="items[{{ $i }}][override_status_id]" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"><option value="">Ikuti pesanan</option>@foreach($itemStatuses as $status)<option value="{{ $status->id }}" @selected(($item['override_status_id'] ?? null) == $status->id)>{{ $status->name }}</option>@endforeach</select></label>
                        <label class="block md:col-span-6"><span class="text-xs font-medium text-zinc-600">Catatan item</span><input name="items[{{ $i }}][notes]" value="{{ $item['notes'] ?? '' }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"></label>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-zinc-500">Untuk menambah lebih banyak item, simpan dulu lalu edit pesanan dan isi baris kosong berikutnya.</p>
        </section>

        <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm text-white">Simpan Pesanan</button>
    </form>
</x-layouts.app>
