@php
    $oldQuantities = collect(old('items', []))->pluck('quantity', 'product_id');
    $hasInitialSelection = $oldQuantities->filter(fn ($quantity) => (int) $quantity > 0)->isNotEmpty();
@endphp

<x-layouts.app title="{{ $batch->batch_name ?: $batch->batch_number }} — Ocean Paws">
    <main class="page !pb-28">
        <div class="catalog-shell page-mid">
            <div class="detail-topbar">
                <a class="top-back" href="{{ route('catalog.index') }}" aria-label="Kembali ke katalog"><x-public-icon name="arrow-left" :size="18" /></a>
                <strong>Pre-Order</strong>
            </div>

            @if(session('catalog_order_code'))
                <section class="catalog-success" role="status">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <div>
                        <strong>Pembayaran sudah dikirim ke admin.</strong>
                        <p>Kode pesananmu: <b>{{ session('catalog_order_code') }}</b>. Simpan kode ini untuk tracking.</p>
                    </div>
                    <a href="{{ route('tracking.index') }}">Cek pesanan</a>
                </section>
            @endif

            @if($errors->any())
                <section class="catalog-error" role="alert">
                    <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                    <div><strong>Pesanan belum dapat dikirim.</strong><p>{{ $errors->first() }}</p></div>
                </section>
            @endif

            <article class="catalog-product-hero product-hero-card">
                <div class="catalog-product-image product-hero-image">
                    @if($batch->catalog_image_path)
                        <img src="{{ $batch->catalog_image_url }}" alt="{{ $batch->batch_name ?: $batch->batch_number }}">
                    @else
                        <div class="catalog-product-placeholder">
                            <i class="bi bi-stars" aria-hidden="true"></i>
                            <span>OCEAN PAWS</span>
                            <strong>K-POP GROUP ORDER</strong>
                        </div>
                    @endif
                </div>
                <div class="catalog-product-copy product-caption">
                    <small>{{ $batch->batch_number }}</small>
                    <h1>{{ $batch->batch_name ?: 'Open Group Order Ocean Paws' }}</h1>
                    <p>
                        Tutup pesanan:
                        <strong>{{ $batch->ordering_deadline?->translatedFormat('d F Y, H:i') ?: 'TBA' }}</strong>
                    </p>
                    @if($batch->description)<div>{{ $batch->description }}</div>@endif
                </div>
            </article>

            <form method="POST" enctype="multipart/form-data" action="{{ route('catalog.checkout', $batch) }}" class="catalog-checkout" data-catalog-checkout data-qris-ready="{{ $batch->qris_image_path ? 'true' : 'false' }}">
                @csrf

                <section class="catalog-panel catalog-order-panel section-card variation-card" data-catalog-order-panel>
                    <header class="catalog-panel-title variation-title">
                        <span class="icon-chip"><x-public-icon name="box" :size="20" /></span>
                        <div><h2>Pilihan Barang</h2><p>Daftar variasi yang tersedia untuk batch ini.</p></div>
                        <button type="button" class="catalog-edit-button" data-open-variant-modal>{{ $hasInitialSelection ? 'Ubah variasi' : 'Pilih variasi' }}</button>
                    </header>

                    <div class="catalog-variant-options catalog-variant-options-inline" aria-label="Daftar variasi tersedia">
                        @forelse($batch->products as $product)
                            <article class="catalog-available-variant">
                                <div>
                                    <strong>{{ $product->variant ?: $product->name }}</strong>
                                    <small>DP Rp {{ number_format($product->pivot->dp_price, 0, ',', '.') }} · Lunas Rp {{ number_format($product->pivot->full_price, 0, ',', '.') }}</small>
                                </div>
                                <span><i class="bi bi-check2" aria-hidden="true"></i> Tersedia</span>
                            </article>
                        @empty
                            <p class="catalog-variant-none">Belum ada variasi yang tersedia untuk batch ini.</p>
                        @endforelse
                    </div>
                    <div data-catalog-order-list hidden></div>
                </section>

                <section class="catalog-panel catalog-payment-panel section-card" data-catalog-payment-section @if(!$hasInitialSelection) hidden @endif>
                    <header class="catalog-panel-title">
                        <span class="icon-chip"><x-public-icon name="card" :size="20" /></span>
                        <div><h2>Submit Proof Payment Disini!</h2><p>Pilih pembayaran, scan QRIS, lalu upload buktinya.</p></div>
                    </header>

                    <div class="catalog-payment-options">
                        <label class="catalog-payment-choice">
                            <input type="radio" name="payment_type" value="dp" @checked(old('payment_type', 'dp') === 'dp')>
                            <span class="catalog-payment-radio"></span>
                            <span><strong>Bayar DP</strong><small>Sebagian dulu, sisanya saat barang tiba</small></span>
                            <b data-catalog-dp-total>Rp 0</b>
                        </label>
                        <label class="catalog-payment-choice">
                            <input type="radio" name="payment_type" value="full" @checked(old('payment_type') === 'full')>
                            <span class="catalog-payment-radio"></span>
                            <span><strong>Bayar Lunas</strong><small>Sekali bayar, tidak ada tagihan susulan</small></span>
                            <b data-catalog-full-total>Rp 0</b>
                        </label>
                    </div>

                    <div class="catalog-payment-breakdown">
                        <small>RINCIAN PEMBAYARAN</small>
                        <div><span>Nilai barang</span><b data-catalog-selected-subtotal>Rp 0</b></div>
                        <div class="catalog-payment-total"><span>Total Pembayaran</span><strong data-catalog-payment-total>Rp 0</strong></div>
                    </div>

                    <div class="catalog-qris-section">
                        <small>METODE PEMBAYARAN</small>
                        @if($batch->qris_image_path)
                            <img src="{{ $batch->qris_image_url }}" alt="QRIS pembayaran {{ $batch->batch_name }}">
                            <p>Pindai QRIS di atas dengan aplikasi bank atau e-wallet.</p>
                        @else
                            <div class="catalog-qris-missing"><i class="bi bi-qr-code" aria-hidden="true"></i><strong>QRIS belum tersedia</strong><p>Admin perlu mengunggah QRIS sebelum pembayaran dapat dikirim.</p></div>
                        @endif
                    </div>

                    <div class="catalog-customer-fields">
                        <small>DATA PEMESAN</small>
                        @if($customer)
                            <div class="catalog-line-customer">
                                <span class="catalog-line-customer-avatar">
                                    @if($customer->avatar_url)<img src="{{ $customer->avatar_url }}" alt="">@else<i class="bi bi-person" aria-hidden="true"></i>@endif
                                </span>
                                <span><strong>{{ $customer->display_name }}</strong><small><i class="bi bi-line" aria-hidden="true"></i> Terhubung dengan LINE</small></span>
                                <a href="{{ route('profile.show') }}">Profil</a>
                            </div>
                            <input type="hidden" name="customer_name" value="{{ old('customer_name', $customer->display_name) }}">
                        @else
                            <div class="catalog-field-grid">
                                <label><span>Nama kamu *</span><input name="customer_name" value="{{ old('customer_name') }}" placeholder="Nama yang dikenali admin" required></label>
                                <label><span>Username LINE *</span><input name="customer_username" value="{{ old('customer_username') }}" placeholder="Tanpa tanda @" required></label>
                            </div>
                        @endif
                    </div>

                    <div class="catalog-proof-section">
                        <label class="catalog-proof-upload">
                            <span>Upload Bukti Transfer *</span>
                            <span class="catalog-proof-control"><i class="bi bi-upload" aria-hidden="true"></i><span><strong data-proof-file-name>Pilih Bukti Pembayaran</strong><small>Format gambar, maksimal 5 MB</small></span></span>
                            <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp" required data-payment-proof-input>
                        </label>
                        <label class="catalog-notes-field"><span>Notes (versi/web)</span><textarea name="notes" rows="3" maxlength="1000" placeholder="Contoh: Version A / Web Apple Music...">{{ old('notes') }}</textarea><small>Tambahkan catatan spesifik jika diperlukan.</small></label>
                    </div>

                    <footer class="catalog-pay-footer">
                        <button type="submit" disabled data-catalog-pay-button><x-public-icon name="send" :size="15" /> <span>Upload bukti pembayaran dahulu</span></button>
                    </footer>
                </section>

                <div class="catalog-variant-modal" data-catalog-variant-modal hidden aria-hidden="true">
                    <button type="button" class="catalog-variant-backdrop" data-close-variant-modal aria-label="Tutup pilihan variasi"></button>
                    <section class="catalog-variant-sheet" role="dialog" aria-modal="true" aria-labelledby="variant-modal-title" tabindex="-1">
                        <div class="catalog-variant-handle"></div>
                        <header>
                            <div class="catalog-variant-thumb">
                                @if($batch->catalog_image_path)<img src="{{ $batch->catalog_image_url }}" alt="">@else<i class="bi bi-box-seam" aria-hidden="true"></i>@endif
                            </div>
                            <div><h2 id="variant-modal-title">Pilih jumlah di bawah</h2><p>{{ $batch->batch_name ?: $batch->batch_number }}</p></div>
                            <button type="button" data-close-variant-modal aria-label="Tutup"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                        </header>
                        <div class="catalog-variant-sheet-body">
                            <small>VARIASI</small>
                            <div class="catalog-variant-options">
                                @forelse($batch->products as $product)
                                    <article class="catalog-variant-option"
                                        data-catalog-variant
                                        data-product-id="{{ $product->id }}"
                                        data-name="{{ $product->variant ?: $product->name }}"
                                        data-dp-price="{{ (float) $product->pivot->dp_price }}"
                                        data-full-price="{{ (float) $product->pivot->full_price }}"
                                        data-quantity="{{ (int) ($oldQuantities[$product->id] ?? 0) }}">
                                        <div><strong>{{ $product->variant ?: $product->name }}</strong><small>DP Rp {{ number_format($product->pivot->dp_price, 0, ',', '.') }} · Lunas Rp {{ number_format($product->pivot->full_price, 0, ',', '.') }}</small></div>
                                        <div class="catalog-quantity-control">
                                            <button type="button" data-quantity-minus aria-label="Kurangi {{ $product->variant ?: $product->name }}">−</button>
                                            <span data-quantity-value>{{ (int) ($oldQuantities[$product->id] ?? 0) }}</span>
                                            <button type="button" data-quantity-plus aria-label="Tambah {{ $product->variant ?: $product->name }}">+</button>
                                        </div>
                                    </article>
                                @empty
                                    <p class="catalog-variant-none">Belum ada variasi yang tersedia untuk batch ini.</p>
                                @endforelse
                            </div>
                        </div>
                        <footer><button type="button" disabled data-confirm-variants>Pilih minimal 1 barang</button></footer>
                    </section>
                </div>

            </form>
        </div>
    </main>
</x-layouts.app>
