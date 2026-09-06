<x-layouts.app title="Ocean Paws — Group Order K-pop">
    <div class="landing-page ocean-home ocean-editorial">
        <div class="landing-wrap">
            <section class="editorial-opening">
                <div class="landing-hero">
                    <div class="landing-hero-copy">
                        <p class="editorial-eyebrow"><span></span> JAJAN BARENG, SEJAK 2023</p>
                        <h1 class="hero-title">Girl group<br>& artis Modhaus,<br><span class="accent">lebih dekat ke kamu.</span></h1>
                        <p class="hero-copy">Titip photocard, album, dan merch favoritmu. Jajan bareng dengan biaya yang jelas, lalu pantau perjalanan pesananmu di sini.</p>
                        <div class="landing-hero-actions">
                            <a href="{{ route('catalog.index') }}" class="landing-primary-link">Temukan jajananmu <x-public-icon name="arrow-right" :size="17" /></a>
                            <a href="{{ route('tracking.index') }}" class="landing-catalog-link">Lacak pesanan <x-public-icon name="arrow-right" :size="17" /></a>
                        </div>
                    </div>
                    <div class="editorial-art" role="img" aria-label="Ilustrasi koleksi album dan photocard bernuansa biru">
                        <div class="art-orbit"></div>
                        <div class="art-record"><span></span></div>
                        <div class="art-album">
                            <div class="art-album-top"><span>THE COLLECTION</span><span>VOL. 01</span></div>
                            <svg class="art-flower" viewBox="0 0 240 240" fill="none" aria-hidden="true"><g fill="#b5e5ff"><ellipse cx="120" cy="68" rx="25" ry="58"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(45 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(90 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(135 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(180 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(225 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(270 120 120)"/><ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(315 120 120)"/></g><circle cx="120" cy="120" r="29" fill="#f5fbff"/><circle cx="120" cy="120" r="8" fill="#2d8dc9"/></svg>
                            <strong>on<br>repeat.</strong>
                            <div class="art-album-bottom"><span>YOUR NEXT FAVORITE</span><span>♡</span></div>
                        </div>
                        <div class="art-note"><span><x-public-icon name="bag" :size="18" /></span><div>Little things.<br><strong>Big happiness.</strong></div></div>
                        <div class="art-photocard"><div class="art-photo-sky"><span>♡</span></div><small>stay in your orbit.</small></div>
                        <span class="art-sticker"><x-public-icon name="sparkle" :size="15" /> ALBUM · PHOTOCARD · MERCH</span>
                    </div>
                </div>
                <div class="editorial-categories" aria-label="Koleksi Ocean Paws">
                    <div class="editorial-marquee">
                        <div class="editorial-marquee-track">
                            <div class="editorial-marquee-group">
                                <span>Photocard</span><i>✦</i><span>Album</span><i>✦</i><span>Merch</span><i>✦</i><span>Lightstick</span><i>✦</i><span>Keychain</span><i>✦</i>
                            </div>
                            <div class="editorial-marquee-group" aria-hidden="true">
                                <span>Photocard</span><i>✦</i><span>Album</span><i>✦</i><span>Merch</span><i>✦</i><span>Lightstick</span><i>✦</i><span>Keychain</span><i>✦</i>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="about-section" id="tentang">
                <div class="section-kicker">Tentang kami</div>
                <h2 class="section-title max-w-[680px]">Group order untuk girl group underrated dan tripleS.</h2>
                <p class="section-copy">Kami jalan sejak 2023 dan sudah menemani pembeli lewat lebih dari 5.000 transaksi. Kami juga jadi GO pertama yang punya web dengan data real-time, jadi kamu bisa cek sendiri posisi photocard, merch, dan album kamu tanpa perlu chat admin dulu.</p>
                <div class="social-row">
                    <a class="social-btn social-x" href="#" aria-label="X (sebelumnya Twitter)" title="X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a class="social-btn social-line" href="#" aria-label="LINE" title="LINE"><i class="bi bi-line" aria-hidden="true"></i></a>
                    <a class="social-btn social-whatsapp" href="#" aria-label="WhatsApp" title="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                </div>

                <div class="mt-7 text-[10px] font-bold text-[#6084aa] lg:text-[12px]">Tim admin</div>
                <div class="admin-grid">
                    @foreach([
                        ['Juna', 'Admin All Negara', 'Aku siap bantu wujudin wishlist kalian dengan harga ramah di kantong! drop jajan 24/7.'],
                        ['Pucel', 'Admin China', 'Paling jago nyelem jajanan girlgroup di china yang termurah! DM pucel~'],
                        ['Amrina', 'Admin Korea', 'Hallo!! Aku admin Korea yang siap sedia layanin kamu 24/7~ Mau persod? Reqshare?'],
                        ['Choa', 'Admin Rekap', 'Siap untuk rekap order, cek data, dan ngerapiin laporan kamu!'],
                        ['Alesha', 'Admin Pack', 'Memastikan semua paket sampai dengan aman dan tepat waktu.'],
                    ] as [$name, $role, $description])
                        <article class="admin-card">
                            <div class="admin-head"><div class="avatar">{{ mb_substr($name, 0, 1) }}</div><div><strong class="text-[11px] lg:text-[14px]">{{ $name }}</strong><div class="text-[9px] text-[#5980a7] lg:text-[11px]">{{ $role }}</div></div></div>
                            <p>{{ $description }}</p>
                            <span class="contact-link"><x-public-icon name="message" :size="10" /> Hubungi</span>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="testi-section" data-testimonial-carousel>
                <div class="testi-heading">
                    <div class="section-kicker">Testimoni</div>
                    <h2 class="section-title">Kata mereka yang sudah<br>jajan.</h2>
                    <p class="section-copy !mt-2">Foto asli dari pembeli, dikirim langsung begitu paketnya sampai.</p>
                </div>
                <div class="testi-track" data-testimonial-track aria-label="Testimoni pembeli">
                    @foreach($testimonialSlides as $testimonial)
                        <article class="testi-card" data-testimonial-card>
                            <img src="{{ $testimonial['image'] }}" alt="Testimoni {{ $testimonial['name'] }}">
                            <div class="testi-copy"><p>{{ $testimonial['content'] }}</p><div class="testi-user"><i>{{ mb_substr($testimonial['name'], 0, 1) }}</i><span>{{ $testimonial['name'] }}</span></div></div>
                        </article>
                    @endforeach
                </div>
                <div class="testi-dots" aria-hidden="true">
                    @foreach($testimonialSlides as $testimonial)
                        <span class="{{ $loop->first ? 'active' : '' }}" data-testimonial-dot></span>
                    @endforeach
                </div>
                <div class="testi-cta">
                    <div class="flex min-w-0 items-center gap-3"><span class="icon-chip"><x-public-icon name="link" :size="20" /></span><div><strong class="block text-[11px] lg:text-[14px]">Sudah pernah jajan di Ocean Paws?</strong><span class="block text-[9px] leading-4 text-[#587ca0] lg:text-[11px] lg:leading-5">Tulis testimonimu dan ceritamu bisa ikut tampil di halaman ini.</span></div></div>
                    <a href="{{ route('testimonials.index') }}" class="btn-primary shrink-0 !px-4 !py-2 !text-[10px] lg:!px-5 lg:!py-2.5 lg:!text-[11px]">Tulis testimoni</a>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
