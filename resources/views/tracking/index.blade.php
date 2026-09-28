<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8d9e2">
    <meta name="description" content="Ocean Paws — bringing your NCT WISH favorites closer.">
    <title>Ocean Paws — Bringing Your Favorites Closer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="op-body">
@php
    $lineDestination = auth()->user()?->member ? route('orders.index') : route('line-auth.redirect');
    $tickerItems = ['ALBUM', 'LIGHTSTICK', 'KEYCHAIN', 'PHOTOCARD', 'POSTER', 'POB', 'PLUSHIE', 'TRADING CARD', 'FAN KIT', "SEASON'S GREETINGS"];
@endphp

<div class="op-page">
    <section class="op-hero" id="home">
        <img class="op-sky-cloud op-sky-cloud-a" src="{{ asset('assets/glossy_pastel_pink_cloud.png') }}" alt="" aria-hidden="true">
        <img class="op-sky-cloud op-sky-cloud-b" src="{{ asset('assets/pastel_pink_cloud_with_soft_highlights.png') }}" alt="" aria-hidden="true">

        <header class="op-browser op-header">
            <div class="op-browserbar">
                <span class="op-window-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            </div>
            <div class="op-navrow">
                <a class="op-brand" href="#home" aria-label="Ocean Paws home"><img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws — Bringing your favorites closer"></a>
                <nav class="op-nav" aria-label="Navigasi utama">
                    <a class="is-active" href="#home"><i class="bi bi-house-heart-fill"></i><span>Home</span></a>
                    <a href="{{ route('tracking.index') }}"><i class="bi bi-truck-front-fill"></i><span>Tracking</span></a>
                    @auth
                        <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i><span>Profile</span></a>
                    @endauth
                </nav>
            </div>
        </header>

        <div class="op-hero-scene">
            <img class="op-hero-wave" src="{{ asset('assets/ocean_paws_hero_ripples_wave.png') }}" alt="" aria-hidden="true">
            <img class="op-hero-cliff" src="{{ asset('assets/pastel_coastal_cliffs_and_greenery.png') }}" alt="" aria-hidden="true">
            <div class="op-copy-window-back" aria-hidden="true"></div>
            <div class="op-copy-window op-browser">
                <div class="op-browserbar"><strong>OCEANPAWS.EXE</strong><span class="op-window-dots" aria-hidden="true"><i></i><i></i><i></i></span></div>
                <div class="op-copy-inner">
                    <small>OCEANPAWS, since 2025</small>
                    <h1>Where every <em>WISH</em><br>comes a little closer
                        <svg class="op-heading-star op-heading-star-pink" viewBox="0 0 200 200" aria-hidden="true" focusable="false"><path d="M195 100c-87.305 4.275-90.725 7.695-95 95-4.275-87.305-7.695-90.725-95-95 87.305-4.275 90.725-7.695 95-95 4.275 87.305 7.695 90.725 95 95" /></svg>
                        <svg class="op-heading-star op-heading-star-green" viewBox="0 0 200 200" aria-hidden="true" focusable="false"><path d="M195 100c-87.305 4.275-90.725 7.695-95 95-4.275-87.305-7.695-90.725-95-95 87.305-4.275 90.725-7.695 95-95 4.275 87.305 7.695 90.725 95 95" /></svg>
                    </h1>
                    <p>Shop your NCT WISH favorites with us and<br>make every WISH a little more special ♡</p>
                    <div class="op-actions">
                        <a class="op-line" href="{{ $lineDestination }}"><b>LINE</b><span>{{ auth()->user()?->member ? 'Buka Pesanan' : 'Login via LINE' }}</span><i class="bi bi-arrow-right"></i></a>
                        <a class="op-search" href="{{ route('tracking.index') }}"><i class="bi bi-search"></i><span>Lacak Pesanan</span></a>
                    </div>
                    <div class="op-copy-hearts" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg>
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg>
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg>
                    </div>
                </div>
            </div>

            <img class="op-hero-mascot" src="{{ asset('assets/oceanpaws-hero-mascot-reference.png') }}" alt="Maskot Ocean Paws berselancar di ombak">
            <img class="op-hero-sand" src="{{ asset('assets/pastel_beach_sand_right.png') }}" alt="" aria-hidden="true">
            <img class="op-palm" src="{{ asset('assets/oceanpaws-palm-rooted-v3.png') }}" alt="" aria-hidden="true">
            <div class="op-sign" aria-label="NCT WISH, Oceanpaws, happier together">
                <img src="{{ asset('assets/oceanpaws-wooden-sign-v2.png') }}" alt="" aria-hidden="true">
                <span>NCT WISH</span>
                <span>OCEANPAWS</span>
                <span>HAPPIER<br>TOGETHER ♡</span>
            </div>
            <img class="op-sign-grass" src="{{ asset('assets/oceanpaws-hero-background-v3.png') }}" alt="" aria-hidden="true">
            <img class="op-starfish" src="{{ asset('assets/glossy_pink_starfish_sticker.png') }}" alt="" aria-hidden="true">
        </div>
    </section>

    <div class="op-ticker" role="marquee" aria-label="K-pop merchandise: album, lightstick, keychain, photocard, poster, POB, plushie, trading card, fan kit, season's greetings">
        <div class="op-ticker-track" aria-hidden="true">
            @foreach ([1, 2] as $copy)
                <div class="op-ticker-group">
                    @foreach ($tickerItems as $item)
                        <span class="op-ticker-item">{{ $item }}</span><span class="op-ticker-separator">✦</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <main class="op-main">
        <section class="op-about" id="about" aria-labelledby="about-title">
            <article class="op-browser op-about-card">
                <div class="op-browserbar"><strong>ABOUT US</strong><span class="op-window-dots"><i></i><i></i><i></i></span></div>
                <div class="op-polaroids" aria-hidden="true">
                    <div class="op-polaroid op-polaroid-back"></div><div class="op-polaroid op-polaroid-mid"></div>
                    <div class="op-polaroid op-polaroid-front">
                        <header class="op-photo-titlebar"><strong>OCEAN PAWS.JPG</strong><span class="op-window-dots"><i></i><i></i><i></i></span></header>
                        <figure class="op-photo-screen"><img src="{{ asset('assets/kawaii_cat_ocean_paws_parcel.png') }}" alt=""></figure>
                        <footer class="op-photo-status"><span>Good Music · Brighter Days<br>with OCEANPAWS ♡</span><b>01 / 03</b></footer>
                    </div>
                </div>
                <div class="op-about-content">
                    <h2 id="about-title">Made for every <em>WISH</em>, since 2025. ♡</h2>
                    <p>Founded in June 2025, GO Oceanpaws is a group order dedicated to NCT WISH. Since then, we've grown into a little community of 2,000+ members and successfully handled thousands of batches together. From albums and photocards to merch and all the little WISH things you love, we're here to make every GO experience easier, clearer, and more enjoyable. ♡</p>
                    <div class="op-about-merch">
                        <img src="{{ asset('assets/oceanpaws-about-merch-collage-v1.png') }}" alt="" aria-hidden="true">
                    </div>
                </div>
            </article>
        </section>

        <section class="op-stats" aria-label="Statistik Ocean Paws">
            <article class="is-pink"><div class="op-stat-dots" aria-hidden="true"><i></i><i></i><i></i></div><div class="op-stat-inner"><img class="op-stat-icon" src="{{ asset('assets/stat-community.svg') }}" alt="" aria-hidden="true"><strong data-count="2000" data-count-suffix="+" aria-label="2,000+">2,000+</strong><span>Community Members</span></div></article>
            <article class="is-green"><div class="op-stat-dots" aria-hidden="true"><i></i><i></i><i></i></div><div class="op-stat-inner"><img class="op-stat-icon" src="{{ asset('assets/stat-batches.svg') }}" alt="" aria-hidden="true"><strong>Thousands+</strong><span>Batches Completed</span></div></article>
            <article class="is-pink"><div class="op-stat-dots" aria-hidden="true"><i></i><i></i><i></i></div><div class="op-stat-inner"><img class="op-stat-icon" src="{{ asset('assets/stat-wish.svg') }}" alt="" aria-hidden="true"><strong data-count="100" data-count-suffix="%" aria-label="100%">100%</strong><span>NCT WISH Focused</span></div></article>
            <article class="is-green"><div class="op-stat-dots" aria-hidden="true"><i></i><i></i><i></i></div><div class="op-stat-inner"><img class="op-stat-icon" src="{{ asset('assets/stat-secure.svg') }}" alt="" aria-hidden="true"><strong>Secure &amp; Clear</strong><span>Ordering Process</span></div></article>
        </section>

        {{-- Keep published testimonial data in the landing response for existing integrations,
             while the supplied reference intentionally has no visible testimonial panel. --}}
        <section hidden aria-hidden="true">
            @foreach ($testimonialSlides as $testimonial)
                <article><p>{{ $testimonial['content'] }}</p><span>{{ $testimonial['name'] }}</span></article>
            @endforeach
        </section>

        <section class="op-cta" aria-labelledby="cta-title">
            <img class="op-cta-wave" src="{{ asset('assets/pastel_mint_ocean_wave_banner.png') }}" alt="" aria-hidden="true">
            <img class="op-cta-mascot" src="{{ asset('assets/oceanpaws-cta-resting-mascot.png') }}" alt="Maskot Ocean Paws di pelampung">
            <div class="op-cta-copy"><h2 id="cta-title">Let's get closer<br><span>to your <em>WISH!</em></span></h2><a class="op-line" href="{{ $lineDestination }}"><b>LINE</b><span>{{ auth()->user()?->member ? 'Buka Pesanan' : 'Login via LINE' }}</span><i class="bi bi-arrow-right"></i></a></div>
            <img class="op-cta-palm" src="{{ asset('assets/kawaii_pastel_palm_tree_sticker.png') }}" alt="" aria-hidden="true">
            <div class="op-cta-sign"><img src="{{ asset('assets/oceanpaws-cta-wood-sign-v1.png') }}" alt="" aria-hidden="true"><span>NCT WISH<br><b>A BRIGHTER<br>TOMORROW<br>TOGETHER ♡</b></span></div>
            <img class="op-cta-sand" src="{{ asset('assets/oceanpaws-cta-sand-only-v2.png') }}" alt="" aria-hidden="true">
            <img class="op-cta-starfish" src="{{ asset('assets/oceanpaws-cta-starfish-3d-v1.png') }}" alt="" aria-hidden="true">
        </section>
    </main>

    <footer class="op-footer">
        <div class="op-footer-inner">
            <a class="op-footer-brand" href="#home" aria-label="Ocean Paws — kembali ke atas">
                <img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws">
            </a>
            <p class="op-footer-credit">© {{ now()->year }} OCEANPAWS. All rights reserved.</p>
        </div>
    </footer>
</div>
</body>
</html>
