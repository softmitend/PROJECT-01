<x-layouts.app title="Testimoni Jajanan — Ocean Paws">
    <div class="testimonial-page">
        <div class="testimonial-shell">
            <header class="testimonial-topbar">
                <a href="{{ route('home') }}" class="testimonial-back" aria-label="Kembali ke beranda">
                    <x-public-icon name="arrow-left" :size="18" />
                    <span>Kembali</span>
                </a>
                <span class="testimonial-top-label">OCEAN PAWS · TESTIMONI</span>
            </header>

            @if (session('status'))
                <div class="testimonial-alert is-success"><x-public-icon name="check-circle" :size="18" /> {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="testimonial-alert is-error">{{ $errors->first() }}</div>
            @endif

            <section class="testimonial-list-hero">
                <span class="testimonial-kicker">CERITA SETELAH JAJAN</span>
                <h1>Pilih jajanan yang<br>sudah sampai di kamu.</h1>
                <p>Testimoni hanya bisa dibuat dari riwayat pesananmu. Pilih satu jajanan, lalu ceritakan pengalamanmu.</p>
            </section>

            @if (! $member)
                <section class="testimonial-empty-card">
                    <span class="testimonial-empty-icon"><x-public-icon name="user" :size="25" /></span>
                    <div>
                        <span class="testimonial-kicker">KHUSUS CUSTOMER</span>
                        <h2>Masuk dulu untuk melihat jajananmu.</h2>
                        <p>Riwayat pesanan akan terhubung otomatis dari akun LINE yang terdaftar.</p>
                    </div>
                    @if ($lineConfigured)
                        <a href="{{ route('line-auth.redirect') }}" class="testimonial-primary-button">Masuk dengan LINE <x-public-icon name="arrow-right" :size="16" /></a>
                    @else
                        <a href="{{ route('profile.show') }}" class="testimonial-primary-button">Lihat profil <x-public-icon name="arrow-right" :size="16" /></a>
                    @endif
                </section>
            @else
                <section class="testimonial-list-section">
                    <div class="testimonial-section-heading">
                        <div>
                            <span class="testimonial-kicker">BISA DIBERI TESTIMONI</span>
                            <h2>Riwayat jajananmu</h2>
                        </div>
                        <span class="testimonial-count">{{ $eligibleItems->count() }} jajanan</span>
                    </div>

                    @if ($eligibleItems->isEmpty())
                        <div class="testimonial-empty-card is-compact">
                            <span class="testimonial-empty-icon"><x-public-icon name="bag" :size="25" /></span>
                            <div>
                                <h2>Belum ada jajanan yang bisa diulas.</h2>
                                <p>Jajanan baru akan muncul di sini setelah tercatat pada riwayat pesananmu.</p>
                            </div>
                            <a href="{{ route('catalog.index') }}" class="testimonial-secondary-button">Lihat katalog</a>
                        </div>
                    @else
                        <div class="testimonial-product-grid">
                            @foreach ($eligibleItems as $item)
                                <article class="testimonial-product-card">
                                    <div class="testimonial-product-image">
                                        @if ($item->order->batch->catalog_image_url)
                                            <img src="{{ $item->order->batch->catalog_image_url }}" alt="{{ $item->item_name }}">
                                        @else
                                            <span><x-public-icon name="gift" :size="30" /></span>
                                        @endif
                                    </div>
                                    <div class="testimonial-product-copy">
                                        <span class="testimonial-order-code">{{ $item->order->order_code }}</span>
                                        <h3>{{ $item->item_name }}</h3>
                                        <p>{{ $item->variant ?: 'Tanpa variasi' }} · {{ $item->quantity }} item</p>
                                        <div class="testimonial-product-meta">
                                            <span>{{ $item->order->created_at->translatedFormat('d M Y') }}</span>
                                            <a href="{{ route('testimonials.create', $item) }}">Beri testimoni <x-public-icon name="arrow-right" :size="14" /></a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                @if ($reviewedItems->isNotEmpty())
                    <section class="testimonial-reviewed-section">
                        <div class="testimonial-section-heading">
                            <div>
                                <span class="testimonial-kicker">SUDAH KAMU ULAS</span>
                                <h2>Cerita yang terkirim</h2>
                            </div>
                        </div>
                        <div class="testimonial-reviewed-list">
                            @foreach ($reviewedItems as $item)
                                <article>
                                    <div>
                                        <strong>{{ $item->item_name }}</strong>
                                        <span>{{ $item->variant ?: $item->order->order_code }}</span>
                                    </div>
                                    <span class="testimonial-stars" aria-label="Rating {{ $item->testimonial->rating }} dari 5">{{ str_repeat('★', $item->testimonial->rating) }}{{ str_repeat('☆', 5 - $item->testimonial->rating) }}</span>
                                    <p>“{{ $item->testimonial->content }}”</p>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
