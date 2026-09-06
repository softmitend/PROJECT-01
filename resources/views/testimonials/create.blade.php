<x-layouts.app title="Tulis Testimoni — Ocean Paws">
    <div class="testimonial-page">
        <div class="testimonial-form-shell">
            <header class="testimonial-topbar">
                <a href="{{ route('testimonials.index') }}" class="testimonial-back" aria-label="Kembali ke daftar jajanan">
                    <x-public-icon name="arrow-left" :size="18" />
                    <span>Kembali</span>
                </a>
                <span class="testimonial-top-label">OCEAN PAWS · TESTIMONI</span>
            </header>

            <section class="testimonial-form-heading">
                <span class="testimonial-kicker">SATU CERITA UNTUK SATU JAJANAN</span>
                <h1>Gimana pengalaman<br>jajanmu kali ini?</h1>
                <p>Ceritamu membantu customer lain memilih jajanan favorit mereka.</p>
            </section>

            <div class="testimonial-form-layout">
                <aside class="testimonial-selected-product">
                    <div class="testimonial-selected-image">
                        @if ($orderItem->order->batch->catalog_image_url)
                            <img src="{{ $orderItem->order->batch->catalog_image_url }}" alt="{{ $orderItem->item_name }}">
                        @else
                            <span><x-public-icon name="gift" :size="34" /></span>
                        @endif
                    </div>
                    <span class="testimonial-order-code">{{ $orderItem->order->order_code }}</span>
                    <h2>{{ $orderItem->item_name }}</h2>
                    <p>{{ $orderItem->variant ?: 'Tanpa variasi' }} · {{ $orderItem->quantity }} item</p>
                    <small>{{ $orderItem->order->batch->batch_name }}</small>
                </aside>

                <form action="{{ route('testimonials.store', $orderItem) }}" method="POST" enctype="multipart/form-data" class="testimonial-form-card">
                    @csrf

                    @if ($errors->any())
                        <div class="testimonial-alert is-error">{{ $errors->first() }}</div>
                    @endif

                    <fieldset class="testimonial-fieldset">
                        <legend>Seberapa puas kamu?</legend>
                        <p>Pilih satu rating untuk jajanan ini.</p>
                        <div class="testimonial-rating-options">
                            @foreach (range(1, 5) as $rating)
                                <input type="radio" name="rating" id="rating-{{ $rating }}" value="{{ $rating }}" {{ (int) old('rating', 5) === $rating ? 'checked' : '' }}>
                                <label for="rating-{{ $rating }}" aria-label="{{ $rating }} bintang"><span>★</span><small>{{ $rating }}</small></label>
                            @endforeach
                        </div>
                    </fieldset>

                    <label class="testimonial-field" for="content">
                        <span>Ceritakan pengalamanmu</span>
                        <small>Minimal 10 karakter</small>
                        <textarea id="content" name="content" rows="6" maxlength="1000" placeholder="Paketnya sampai dengan aman, barangnya lucu banget..." required>{{ old('content') }}</textarea>
                    </label>

                    <label class="testimonial-upload" for="photo">
                        <span class="testimonial-upload-icon"><x-public-icon name="upload" :size="21" /></span>
                        <span><strong>Tambahkan foto</strong><small>Opsional · JPG, PNG, atau WEBP · maksimal 4 MB</small></span>
                        <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp">
                    </label>

                    <div class="testimonial-form-actions">
                        <p>Dengan mengirim, testimoni dapat ditampilkan di halaman Ocean Paws.</p>
                        <button type="submit" class="testimonial-primary-button">Kirim testimoni <x-public-icon name="send" :size="16" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
