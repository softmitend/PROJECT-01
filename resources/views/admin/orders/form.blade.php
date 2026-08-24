@php
    $selectedBatchId = (int) old('batch_id', $order->batch_id ?: request('batch_id'));
    $availableProducts = $productsByBatch->get($selectedBatchId, collect());
    $itemsAreLocked = $order->exists && ($order->batch?->orders_locked || $order->is_refunded);
    $batchProductPayload = $productsByBatch->map(fn ($products) => $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'variant' => $product->variant,
        'price' => $product->default_price,
    ])->values());
@endphp

<x-layouts.app title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}">
    <x-admin-form-shell
        title="{{ $order->exists ? 'Edit Pesanan' : 'Tambah Pesanan' }}"
        eyebrow="Pesanan Customer"
        description="Pilih customer dan batch; daftar produk akan mengikuti master produk pada batch tersebut."
        max-width="max-w-6xl"
    >
        <form method="POST" action="{{ $order->exists ? route('admin.member-orders.update', $order, false) : route('admin.member-orders.store', [], false) }}">
            @csrf
            @if($order->exists) @method('PUT') @endif

            <div class="admin-form-body">
                <x-admin-form-intro
                    title="Panduan Pencatatan Pesanan"
                    description="Pastikan customer, batch, dan produk sudah benar. Kode tracking dibuat otomatis oleh sistem dan progress awal mengikuti status batch."
                />

                <x-admin-form-section title="Identitas Pesanan">
                    <div class="space-y-4">
                        @if($order->exists)
                            <div class="admin-form-readonly">
                                <span>Kode pesanan / tracking</span>
                                <strong>{{ $order->order_code }}</strong>
                                <small class="admin-form-help">Identitas tracking ini dibuat sistem dan tidak dapat diubah.</small>
                            </div>
                        @else
                            <div class="admin-form-readonly admin-form-code-preview">
                                <span>Kode pesanan otomatis</span>
                                <strong>ORD-{{ now()->format('ym') }}-XXXXXX</strong>
                                <small class="admin-form-help">Nomor final ditentukan otomatis setelah pesanan disimpan.</small>
                            </div>
                        @endif

                        <div class="order-identity-grid grid gap-4 lg:grid-cols-3">
                            @if($selectedMember)
                                <label class="block">
                                    <span class="text-sm font-medium">Customer</span>
                                    <input class="admin-form-selectlike-readonly" type="text" value="{{ $selectedMember->display_name }}" readonly aria-readonly="true">
                                    <input type="hidden" name="member_id" value="{{ $selectedMember->id }}">
                                    <span class="admin-form-help">Customer tidak dapat diganti setelah ditentukan pada pesanan ini.</span>
                                </label>
                            @else
                                <label class="block">
                                    <span class="text-sm font-medium">Customer</span>
                                    <select name="member_id" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" required>
                                        <option value="">Pilih customer</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" @selected(old('member_id', request('member_id')) == $member->id)>{{ $member->display_name }} · {{ $member->email ?: $member->member_code }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
                            @if($itemsAreLocked)
                                <div class="admin-form-readonly">
                                    <span>Batch pembelian</span>
                                    <strong>{{ $order->batch->batch_number }}</strong>
                                    <small class="admin-form-help">{{ $order->is_refunded ? 'Batch dan produk terkunci karena pesanan refund telah selesai.' : 'Batch dan produk terkunci sejak progress mencapai tahap Sudah Dipesan ke Supplier.' }}</small>
                                    <input type="hidden" name="batch_id" value="{{ $order->batch_id }}">
                                </div>
                            @else
                                <label class="block">
                                    <span class="text-sm font-medium">Batch pembelian</span>
                                    <select name="batch_id" data-order-batch class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" required>
                                        <option value="">Pilih batch</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}" @selected($selectedBatchId === $batch->id)>{{ $batch->batch_number }}{{ $batch->batch_name ? ' · '.$batch->batch_name : '' }} · {{ $batch->products->count() }} produk</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
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
                        </div>

                        <label class="block"><span class="text-sm font-medium">Catatan pesanan</span><textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm">{{ old('notes', $order->notes) }}</textarea></label>
                    </div>
                </x-admin-form-section>

                <x-admin-form-section title="Produk yang Dibeli">
                    @if($itemsAreLocked)
                        <div class="admin-form-lock-notice">
                            <strong>Komposisi pesanan sudah dikunci</strong>
                            <p>{{ $order->is_refunded ? 'Pesanan refund telah selesai.' : 'Batch '.$order->batch->batch_number.' telah mencapai tahap '.($order->batch->currentStatus?->name ?: 'progress terkunci').'.' }} Produk, jumlah, harga, dan batch tidak dapat diubah lagi.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($order->items as $item)
                                <div class="admin-form-readonly">
                                    <span>{{ $item->item_name }}{{ $item->variant ? ' · '.$item->variant : '' }}</span>
                                    <strong>{{ $item->quantity }} × {{ $item->unit_price ? 'Rp '.number_format($item->unit_price, 0, ',', '.') : '-' }}</strong>
                                    <small class="admin-form-help">Subtotal: {{ $item->subtotal ? 'Rp '.number_format($item->subtotal, 0, ',', '.') : '-' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-sm text-zinc-500">Produk tersedia mengikuti batch yang dipilih. Batch satu produk akan dipilih dan dikunci otomatis.</p>
                            <button type="button" data-add-item class="admin-form-inline-action" @if($availableProducts->count() <= 1) hidden @endif>+ Tambah Produk</button>
                        </div>

                        @php($rows = old('items', $order->items?->toArray() ?: [['quantity' => 1]]))
                        <div data-items class="space-y-3">
                            @foreach($rows as $i => $item)
                                @include('admin.orders.partials.item-row', ['index' => $i, 'item' => $item, 'availableProducts' => $availableProducts])
                            @endforeach
                        </div>

                        <template data-item-template>
                            @include('admin.orders.partials.item-row', ['index' => '__INDEX__', 'item' => ['quantity' => 1], 'availableProducts' => $availableProducts])
                        </template>
                        <script type="application/json" data-batch-products>@json($batchProductPayload)</script>
                    @endif
                </x-admin-form-section>
            </div>

            <footer class="admin-form-footer">
                <p class="admin-form-footer-note">Progress pesanan mengikuti batch. Pengecualian status dapat diatur setelah pesanan tersimpan.</p>
                <div class="admin-form-actions">
                    <a class="admin-form-secondary" href="{{ $order->exists ? route('admin.member-orders.show', $order, false) : route('admin.member-orders.index', [], false) }}">Batal</a>
                    <button type="submit" class="admin-form-primary">Simpan Pesanan</button>
                </div>
            </footer>
        </form>
    </x-admin-form-shell>

    @unless($itemsAreLocked)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('[data-items]');
            const template = document.querySelector('[data-item-template]');
            const addButton = document.querySelector('[data-add-item]');
            const batchSelect = document.querySelector('[data-order-batch]');
            const productsByBatch = JSON.parse(document.querySelector('[data-batch-products]').textContent || '{}');
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

            const populateProductSelect = (select, products, preserveValue = '') => {
                select.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = products.length ? 'Pilih produk' : 'Pilih batch terlebih dahulu';
                select.appendChild(placeholder);

                products.forEach((product) => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.name}${product.variant ? ` · ${product.variant}` : ''}`;
                    option.dataset.name = product.name;
                    option.dataset.variant = product.variant || '';
                    option.dataset.price = product.price || '';
                    select.appendChild(option);
                });

                select.value = products.some((product) => String(product.id) === String(preserveValue))
                    ? String(preserveValue)
                    : (products.length === 1 ? String(products[0].id) : '');
                select.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery?.fn?.select2 && window.jQuery(select).hasClass('select2-hidden-accessible')) {
                    window.jQuery(select).trigger('change.select2');
                }
            };

            const configureProducts = (resetRows = false) => {
                const products = productsByBatch[batchSelect?.value] || [];
                if (resetRows) {
                    let existingRows = [...container.querySelectorAll('[data-item-row]')];
                    existingRows.slice(1).forEach((row) => row.remove());

                    if (!existingRows.length) {
                        container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', nextIndex++));
                        existingRows = [...container.querySelectorAll('[data-item-row]')];
                    }

                    const retainedRow = existingRows[0];
                    retainedRow.dataset.productId = '';
                    retainedRow.querySelectorAll('input').forEach((input) => {
                        input.value = input.matches('[data-item-qty]') ? 1 : '';
                    });
                }

                let rows = [...container.querySelectorAll('[data-item-row]')];
                if (products.length === 1 && rows.length > 1) {
                    rows.slice(1).forEach((row) => row.remove());
                    rows = rows.slice(0, 1);
                }

                rows.forEach((row) => {
                    const select = row.querySelector('[data-product-select]');
                    populateProductSelect(select, products, resetRows ? '' : select.value);
                    row.querySelector('[data-remove-item]').hidden = products.length <= 1;
                });

                addButton.hidden = products.length <= 1;
                document.dispatchEvent(new CustomEvent('admin:enhance-selects', {
                    detail: { root: container },
                }));
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
                const products = productsByBatch[batchSelect?.value] || [];
                const row = container.lastElementChild;
                populateProductSelect(row.querySelector('[data-product-select]'), products);
            });

            let batchConfigurationTimer;
            const scheduleBatchConfiguration = () => {
                window.clearTimeout(batchConfigurationTimer);
                batchConfigurationTimer = window.setTimeout(() => configureProducts(true), 0);
            };

            batchSelect?.addEventListener('change', scheduleBatchConfiguration);
            if (batchSelect && window.jQuery) {
                window.jQuery(batchSelect).on('select2:select select2:clear', scheduleBatchConfiguration);
            }
            configureProducts();
        });
    </script>
    @endunless
</x-layouts.app>
