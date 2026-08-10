<x-layouts.app title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}">
    <x-admin-form-shell
        title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}"
        eyebrow="Pesanan Customer"
        description="Pilih customer dan batch, lalu tambahkan produk dari katalog pembelian."
        max-width="max-w-6xl"
    >
        <form method="POST" action="{{ $order->exists ? route('admin.member-orders.update', $order, false) : route('admin.member-orders.store', [], false) }}">
            @csrf
            @if($order->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Pencatatan Pesanan"
                    description="Pastikan customer, batch, dan produk sudah benar. Kode pesanan digunakan customer untuk memeriksa progress secara langsung."
                />

                <x-admin-form-section title="Identitas Pesanan">
                    <div class="grid gap-4 md:grid-cols-2">
            <x-text-input label="Kode pesanan / tracking" name="order_code" :value="$order->order_code ?: 'ORD-'.now()->format('YmdHis')" required />
            <label class="block">
                <span class="text-sm font-medium">Customer</span>
                <select name="member_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" required>
                    <option value="">Pilih customer</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $order->member_id) == $member->id)>{{ $member->display_name }} · {{ $member->email ?: $member->member_code }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Batch pembelian</span>
                <select name="batch_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" required>
                    <option value="">Pilih batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @selected(old('batch_id', $order->batch_id) == $batch->id)>{{ $batch->batch_number }}{{ $batch->batch_name ? ' · '.$batch->batch_name : '' }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Status khusus pesanan</span>
                <select name="override_status_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">
                    <option value="">Ikuti status batch</option>
                    @foreach($orderStatuses as $status)
                        <option value="{{ $status->id }}" @selected(old('override_status_id', $order->override_status_id) == $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
                <span class="mt-1 block text-xs text-zinc-500">Biarkan kosong jika progress sama dengan batch.</span>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Status pembayaran</span>
                <select name="payment_status_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">
                    <option value="">Pilih status pembayaran</option>
                    @foreach($paymentStatuses as $status)
                        <option value="{{ $status->id }}" @selected(old('payment_status_id', $order->payment_status_id) == $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
                @if($order->payment_status && !$order->payment_status_id)
                    <span class="admin-form-help">Data lama: {{ $order->payment_status }}. Pilih status baru untuk menghubungkannya ke Manajemen Status.</span>
                @else
                    <span class="admin-form-help">Pilihan berasal dari status dengan cakupan Pembayaran.</span>
                @endif
            </label>
            <label class="block md:col-span-2"><span class="text-sm font-medium">Catatan pesanan</span><textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('notes', $order->notes) }}</textarea></label>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Produk yang Dibeli">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-zinc-500">Nama, varian, dan harga awal diambil otomatis dari katalog produk.</p>
                        <button type="button" data-add-item class="admin-form-inline-action">+ Tambah Produk</button>
                    </div>

                    @php($rows = old('items', $order->items?->toArray() ?: [['quantity' => 1]]))
                    <div data-items class="space-y-3">
                        @foreach($rows as $i => $item)
                            @include('admin.orders.partials.item-row', ['index' => $i, 'item' => $item])
                        @endforeach
                    </div>

                    <template data-item-template>
                        @include('admin.orders.partials.item-row', ['index' => '__INDEX__', 'item' => ['quantity' => 1]])
                    </template>
                </x-admin-form-section>
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Produk dan status dapat diperbarui kembali selama pesanan masih berjalan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ route('admin.member-orders.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Pesanan</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('[data-items]');
            const template = document.querySelector('[data-item-template]');
            const addButton = document.querySelector('[data-add-item]');
            let nextIndex = {{ count($rows) }};

            const applyProduct = (select) => {
                const option = select.options[select.selectedIndex];
                const row = select.closest('[data-item-row]');
                row.querySelector('[data-item-name]').value = option.dataset.name || '';
                row.querySelector('[data-item-variant]').value = option.dataset.variant || '';
                if (!row.querySelector('[data-item-price]').value || row.dataset.productId !== select.value) {
                    row.querySelector('[data-item-price]').value = option.dataset.price || '';
                }
                row.dataset.productId = select.value;
            };

            container.addEventListener('change', (event) => {
                if (event.target.matches('[data-product-select]')) applyProduct(event.target);
            });

            container.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-item]');
                if (!button) return;
                if (container.querySelectorAll('[data-item-row]').length === 1) {
                    const row = button.closest('[data-item-row]');
                    row.querySelectorAll('input').forEach(input => input.value = input.matches('[data-item-qty]') ? 1 : '');
                    row.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                    return;
                }
                button.closest('[data-item-row]').remove();
            });

            addButton.addEventListener('click', () => {
                container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', nextIndex++));
            });
        });
    </script>
</x-layouts.app>
