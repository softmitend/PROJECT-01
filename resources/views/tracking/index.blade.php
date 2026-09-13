<x-layouts.app title="Ocean Paws — Group Order K-pop">
    <div class="landing-page ocean-home ocean-editorial">
        <div class="landing-wrap">
            <section class="editorial-opening">
                <div class="landing-hero">
                    <div class="landing-hero-copy">
                        <p class="editorial-eyebrow"><span></span> OCEANPAWS, SINCE 2025</p>
                        <h1 class="hero-title">Where every <span class="wish-highlight">WISH</span><br><span class="accent">comes a little closer.</span>
                        </h1>
                        <p class="hero-copy">Shop your NCT WISH favorites with us and make every WISH a little more
                            special <svg class="hero-copy-heart" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7.2-4.6-9.5-8.8C.5 8.6 2.4 4 6.7 4c2.2 0 4.1 1.3 5.3 3 1.2-1.7 3.1-3 5.3-3 4.3 0 6.2 4.6 4.2 8.2C19.2 16.4 12 21 12 21Z"/></svg></p>
                        <div class="landing-hero-actions">
                            <a href="{{ route('services.index') }}" class="landing-primary-link">Lihat layanan kami
                                <x-public-icon name="arrow-right" :size="17" /></a>
                            <a href="{{ route('tracking.index') }}" class="landing-catalog-link">Lacak pesanan
                                <x-public-icon name="arrow-right" :size="17" /></a>
                        </div>
                    </div>
                    <div class="editorial-art" role="img"
                        aria-label="Ilustrasi koleksi album dan photocard bernuansa biru">
                        <div class="art-orbit"></div>
                        <div class="art-record"><span></span></div>
                        <div class="art-album">
                            <div class="art-album-top"><span>THE COLLECTION</span><span>VOL. 01</span></div>
                            <svg class="art-flower" viewBox="0 0 240 240" fill="none" aria-hidden="true">
                                <g fill="#b5e5ff">
                                    <ellipse cx="120" cy="68" rx="25" ry="58" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(45 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(90 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(135 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(180 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(225 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(270 120 120)" />
                                    <ellipse cx="120" cy="68" rx="25" ry="58" transform="rotate(315 120 120)" />
                                </g>
                                <circle cx="120" cy="120" r="29" fill="#f5fbff" />
                                <circle cx="120" cy="120" r="8" fill="#2d8dc9" />
                            </svg>
                            <strong>on<br>repeat.</strong>
                            <div class="art-album-bottom"><span>YOUR NEXT FAVORITE</span><span>♡</span></div>
                        </div>
                        <div class="art-note">
                            <span>
                                <img src="{{ asset('img/icon.png') }}" alt="" class="art-note-image">
                            </span>
                            <div>Little things.<br><strong>Big happiness.</strong></div>
                        </div>
                        <div class="art-photocard">
                            <div class="art-photo-sky">
                                <img src="{{ asset('img/photocard.PNG') }}" alt="Ocean Paws" class="art-photo-image">
                            </div>

                            <small>stay in your orbit.</small>
                        </div>
                        <span class="art-sticker"><x-public-icon name="sparkle" :size="15" /> ALBUM · PHOTOCARD ·
                            MERCH</span>
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
                <div class="section-kicker" style="font-size: 13px;">About Us</div>
                <h2 class="section-title max-w-[680px]">Made for every WISH, since 2025 ♡</h2>
                <p class="section-copy" style="font-size: 14px;">Founded in June 2025, GO Oceanpaws is a group order
                    dedicated to NCT WISH. Since then, we've grown into a litte community pf 2000+ members and
                    successfully handled thousands of batches together. From albums and photocards to merch and all the
                    little WISH things you love, we're here to make every GO experiece easier, clearer, and more
                    enjoyable </p>
                <div class="about-info-rail">
                    <div class="social-row" aria-label="Media sosial Ocean Paws">
                        <a class="social-btn social-x" href="#" aria-label="X (sebelumnya Twitter)" title="X"><i
                                class="bi bi-twitter-x" aria-hidden="true"></i></a>
                        <a class="social-btn social-line" href="#" aria-label="LINE" title="LINE"><i class="bi bi-line"
                                aria-hidden="true"></i></a>
                        <a class="social-btn social-whatsapp" href="#" aria-label="WhatsApp" title="WhatsApp"><i
                                class="bi bi-whatsapp" aria-hidden="true"></i></a>
                    </div>
                    <div class="about-stat-grid" aria-label="Statistik Ocean Paws">
                        <div class="about-stat">
                            <span class="about-stat-icon"><x-public-icon name="box" :size="16" /></span>
                            <div class="about-stat-copy"><strong>6.000+</strong><span>Orders completed</span></div>
                        </div>
                        <div class="about-stat">
                            <span class="about-stat-icon"><x-public-icon name="message" :size="16" /></span>
                            <div class="about-stat-copy"><strong>4.500+</strong><span>Buyer testimonials</span></div>
                        </div>
                        <div class="about-stat">
                            <span class="about-stat-icon"><x-public-icon name="calendar" :size="16" /></span>
                            <div class="about-stat-copy"><strong>2025</strong><span>Established since</span></div>
                        </div>
                        <div class="about-stat">
                            <span class="about-stat-icon"><x-public-icon name="sparkle" :size="16" /></span>
                            <div class="about-stat-copy"><strong>5.0</strong><span>Buyer rating</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="testi-section" data-testimonial-carousel>
                <div class="testi-heading">
                    <div class="section-kicker">Testimonials</div>
                    <h2 class="section-title">Listen to what they<br>have to say about us</h2>
                    <p class="section-copy !mt-2">Check out reviews from happy buyers who just got their orders
                        delivered.</p>
                </div>
                <div class="testi-track" data-testimonial-track aria-label="Testimoni pembeli">
                    @foreach($testimonialSlides as $testimonial)
                        <article class="testi-card" data-testimonial-card>
                            <div class="testi-media">
                                <img src="{{ $testimonial['image'] }}" alt="Foto testimoni dari {{ $testimonial['name'] }}">
                                <span class="testi-quote-mark" aria-hidden="true">“</span>
                                <span class="testi-rating" aria-label="Rating {{ $testimonial['rating'] ?? 5 }} dari 5">
                                    <span aria-hidden="true">★</span>
                                    {{ number_format((float) ($testimonial['rating'] ?? 5), 1) }}
                                </span>
                            </div>
                            <div class="testi-copy">
                                <p>{{ $testimonial['content'] }}</p>
                                <div class="testi-user">
                                    <i>{{ mb_substr($testimonial['name'], 0, 1) }}</i>
                                    <div>
                                        <strong>{{ $testimonial['name'] }}</strong>
                                        <span>Pembeli Ocean Paws</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="testi-dots" aria-hidden="true">
                    @foreach($testimonialSlides as $testimonial)
                        <span class="{{ $loop->first ? 'active' : '' }}" data-testimonial-dot></span>
                    @endforeach
                </div>
                <div class="testi-cta">
                    <div class="flex min-w-0 items-center gap-3"><span class="icon-chip"><x-public-icon name="link"
                                :size="20" /></span>
                        <div><strong class="block text-[11px] lg:text-[14px]">Sudah pernah jajan di Ocean
                                Paws?</strong><span
                                class="block text-[9px] leading-4 text-[#587ca0] lg:text-[11px] lg:leading-5">Tulis
                                testimonimu dan ceritamu bisa ikut tampil di halaman ini.</span></div>
                    </div>
                    <a href="{{ route('testimonials.index') }}"
                        class="btn-primary shrink-0 !px-4 !py-2 !text-[10px] lg:!px-5 lg:!py-2.5 lg:!text-[11px]">Tulis
                        testimoni</a>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>