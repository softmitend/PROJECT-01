<<<<<<< Updated upstream
<x-layouts.app title="Ocean Paws — Group Order K-pop">
    <div class="landing-page ocean-home ocean-editorial">
        <div class="landing-wrap">
            <section class="editorial-opening">
                <div class="landing-hero">
                    <div class="landing-hero-copy">
                        <p class="editorial-eyebrow"><span></span> OCEANPAWS, SINCE 2025</p>
                        <h1 class="hero-title">Where every<br><span class="wish-highlight">WISH</span> <span class="accent">comes</span><br><span class="accent">a little closer.</span>
                        </h1>
                        <p class="hero-copy">Shop your <span class="hero-copy-highlight">NCT WISH</span> favorites with us and make every WISH a little more
                            special <svg class="hero-copy-heart" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12 21s-7.2-4.6-9.5-8.8C.5 8.6 2.4 4 6.7 4c2.2 0 4.1 1.3 5.3 3 1.2-1.7 3.1-3 5.3-3 4.3 0 6.2 4.6 4.2 8.2C19.2 16.4 12 21 12 21Z" />
                            </svg></p>
                        <div class="landing-hero-actions">
                            <a href="{{ auth()->user()?->member ? route('orders.index') : route('line-auth.redirect') }}" class="landing-primary-link">{{ auth()->user()?->member ? 'Buka pesanan' : 'Login dengan LINE' }}
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
                                <svg class="art-note-heart" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 21s-8.5-5.1-8.5-11.2A4.8 4.8 0 0 1 12 6.7a4.8 4.8 0 0 1 8.5 3.1C20.5 15.9 12 21 12 21Z"/>
                                </svg>
                            </span>
                            <div>Little things.<br><strong>Big happiness.</strong></div>
                        </div>
                        <div class="art-photocard">
                            <div class="art-photo-sky">
                                <img src="{{ asset('img/photocard.PNG') }}" alt="Ocean Paws" class="art-photo-image">
                            </div>
=======
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
>>>>>>> Stashed changes

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
<<<<<<< Updated upstream
            </section>

            <footer class="ocean-reference-footer">
                <div class="ocean-footer-top-wave" aria-hidden="true">
                    <svg viewBox="0 0 1440 56" preserveAspectRatio="none">
                        <path d="M0 33C78 6 147 39 226 23s151-7 225 3 120-10 194-8c79 2 102 24 186 17 91-7 112-30 210-27 82 3 120 25 206 20 85-5 116-24 193-12v40H0Z" fill="#eef8ed"/>
                        <path d="M0 29C78 2 147 35 226 19s151-7 225 3 120-10 194-8c79 2 102 24 186 17 91-7 112-30 210-27 82 3 120 25 206 20 85-5 116-24 193-12" fill="none" stroke="#a6dcb4" stroke-width="3"/>
                    </svg>
                </div>

                <div class="ocean-footer-cloud ocean-footer-cloud-left" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
                <div class="ocean-footer-cloud ocean-footer-cloud-right" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>

                <div class="ocean-footer-main">
                    <div class="ocean-footer-brand">
                        <a href="{{ route('home') }}" aria-label="Ocean Paws home" class="ocean-footer-wordmark">
                            <span class="ocean-word-ocean">OCEA</span><span class="ocean-word-paws">PAWS</span><b>✿</b>
                        </a>
                        <p>Bringing your favorites closer</p>
                    </div>

                    <span class="ocean-footer-divider" aria-hidden="true"></span>

                    <nav class="ocean-footer-nav" aria-label="Footer navigation">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('services.index') }}">Layanan</a>
                        <a href="{{ route('tracking.index') }}">Tracking</a>
                        <a href="#faq">FAQ</a>
                    </nav>

                    <span class="ocean-footer-divider" aria-hidden="true"></span>

                    <div class="ocean-footer-social" aria-label="Media sosial Ocean Paws">
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>

                    <span class="ocean-footer-divider" aria-hidden="true"></span>

                    <div class="ocean-footer-slogan">
                        <span>Small Orders</span>
                        <strong>Bigger Happiness ♡</strong>
                    </div>

                    <svg class="ocean-footer-shell" viewBox="0 0 100 100" aria-hidden="true">
                        <g fill="#ffd6df" stroke="#ef6487" stroke-width="5" stroke-linejoin="round">
                            <path d="M50 72C28 70 13 57 14 39c1-12 8-20 17-17 2-11 10-18 19-10 8-8 16-1 19 10 9-3 16 5 17 17 2 18-14 31-36 33Z"/>
                            <path d="M50 16v51M31 24l12 44M69 24 57 68M18 39l20 32M82 39 62 71" fill="none"/>
                            <path d="M38 70c2 11 7 16 12 16s10-5 12-16Z"/>
                        </g>
                    </svg>
                </div>

                <div class="ocean-footer-sea" aria-hidden="true">
                    <svg viewBox="0 0 1440 150" preserveAspectRatio="none">
                        <path d="M0 47c105 39 174-9 276 7 111 18 134 54 259 29 107-21 154-39 259-11 111 30 160 33 267 1 106-31 199-34 379-3v80H0Z" fill="#7fb88a"/>
                        <path d="M0 74c105 39 174-9 276 7 111 18 134 54 259 29 107-21 154-39 259-11 111 30 160 33 267 1 106-31 199-34 379-3v53H0Z" fill="#8fc49a" opacity=".88"/>
                        <path d="M0 87c106 34 174-8 269 7 118 18 156 53 263 31 116-23 162-42 268-13 112 31 161 30 269 2 120-31 211-29 371 0" fill="none" stroke="#acd5b2" stroke-width="3" opacity=".7"/>
                        <path d="M87 114c58-27 112-26 170-7M553 126c89-33 174-35 263-6M1081 123c62-24 125-25 188-6" fill="none" stroke="#a8d2ae" stroke-width="2" opacity=".58"/>
                        <g fill="#b9dbbc" opacity=".75">
                            <ellipse cx="370" cy="85" rx="7" ry="4"/><ellipse cx="541" cy="116" rx="5" ry="3"/><ellipse cx="946" cy="109" rx="8" ry="4"/><ellipse cx="1003" cy="84" rx="5" ry="3"/><ellipse cx="1283" cy="124" rx="6" ry="3"/>
                        </g>
                    </svg>
                </div>

                <div class="ocean-footer-bottom">
                    <span>© 2025 OCEANPAWS. All rights reserved.</span>
                    <span>For NCT WISH. Always. ♡</span>
                </div>
            </footer>
        </div>
    </div>

    <style>
        .ocean-reference-footer {
            position: relative;
            width: 100%;
            min-height: 260px;
            margin-top: 84px;
            overflow: hidden;
            background: linear-gradient(180deg, #fbf6e9 0%, #eef8ed 46%, #e6f3e5 100%);
            color: #183d2d;
            isolation: isolate;
        }

        .ocean-footer-top-wave {
            position: absolute;
            z-index: 1;
            inset: -2px 0 auto;
            height: 58px;
            pointer-events: none;
        }

        .ocean-footer-top-wave svg,
        .ocean-footer-sea svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .ocean-footer-main {
            position: relative;
            z-index: 4;
            display: grid;
            grid-template-columns: minmax(170px, 1.15fr) 1px minmax(300px, 1.55fr) 1px minmax(180px, .9fr) 1px minmax(185px, 1fr) 82px;
            align-items: center;
            gap: clamp(18px, 2vw, 34px);
            width: min(90%, 1240px);
            margin: 0 auto;
            padding: 72px 0 102px;
        }

        .ocean-footer-brand {
            min-width: 0;
        }

        .ocean-footer-wordmark {
            position: relative;
            display: inline-flex;
            align-items: flex-end;
            text-decoration: none;
            filter: drop-shadow(0 2px 0 rgba(22, 59, 43, .12));
        }

        .ocean-footer-wordmark span {
            display: inline-block;
            font-family: "Bricolage Grotesque", "Nunito Sans", sans-serif;
            font-size: clamp(27px, 2.75vw, 42px);
            font-weight: 900;
            line-height: .9;
            letter-spacing: -.09em;
            -webkit-text-stroke: 1.7px #173b2b;
            paint-order: stroke fill;
            text-shadow: 0 2px 0 #fff8ef;
        }

        .ocean-word-ocean {
            color: #f6a5b7;
        }

        .ocean-word-paws {
            color: #8fd09e;
        }

        .ocean-footer-wordmark b {
            position: absolute;
            right: -17px;
            top: -11px;
            color: #ff7296;
            font-size: 20px;
            font-weight: 700;
            -webkit-text-stroke: 0;
        }

        .ocean-footer-brand p {
            margin: 7px 0 0;
            color: #36594b;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: -.015em;
            white-space: nowrap;
        }

        .ocean-footer-divider {
            width: 1px;
            height: 34px;
            background: rgba(31, 72, 52, .62);
        }

        .ocean-footer-nav,
        .ocean-footer-social {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ocean-footer-nav {
            gap: clamp(18px, 2.5vw, 34px);
        }

        .ocean-footer-nav a {
            color: #214838;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .2s ease, transform .2s ease;
        }

        .ocean-footer-nav a:hover,
        .ocean-footer-social a:hover {
            opacity: .68;
            transform: translateY(-2px);
        }

        .ocean-footer-social {
            gap: 21px;
        }

        .ocean-footer-social a {
            display: grid;
            place-items: center;
            color: #195334;
            font-size: 22px;
            line-height: 1;
            text-decoration: none;
            transition: opacity .2s ease, transform .2s ease;
        }

        .ocean-footer-slogan {
            display: flex;
            flex-direction: column;
            gap: 2px;
            color: #23483a;
            font-size: 12px;
            line-height: 1.3;
            white-space: nowrap;
        }

        .ocean-footer-slogan strong {
            font-weight: 600;
        }

        .ocean-footer-shell {
            width: 62px;
            height: 62px;
            transform: rotate(4deg);
            filter: drop-shadow(0 3px 0 rgba(239, 100, 135, .12));
        }

        .ocean-footer-cloud {
            position: absolute;
            z-index: 2;
            width: 128px;
            height: 80px;
            bottom: 75px;
            pointer-events: none;
        }

        .ocean-footer-cloud-left {
            left: -38px;
        }

        .ocean-footer-cloud-right {
            right: -32px;
        }

        .ocean-footer-cloud span {
            position: absolute;
            bottom: 0;
            border-radius: 999px 999px 18px 18px;
            background: rgba(255, 255, 255, .62);
        }

        .ocean-footer-cloud span:nth-child(1) {
            left: 0;
            width: 76px;
            height: 50px;
        }

        .ocean-footer-cloud span:nth-child(2) {
            left: 45px;
            width: 70px;
            height: 68px;
        }

        .ocean-footer-cloud span:nth-child(3) {
            left: 88px;
            width: 54px;
            height: 43px;
        }

        .ocean-footer-sea {
            position: absolute;
            z-index: 3;
            left: 0;
            right: 0;
            bottom: 0;
            height: 140px;
            pointer-events: none;
        }

        .ocean-footer-bottom {
            position: absolute;
            z-index: 5;
            left: 5%;
            right: 5%;
            bottom: 19px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #f5fff5;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .01em;
        }

        @media (max-width: 1050px) {
            .ocean-reference-footer {
                min-height: 350px;
            }

            .ocean-footer-main {
                grid-template-columns: 1fr 1fr;
                grid-template-areas:
                    "brand slogan"
                    "nav social";
                gap: 24px 48px;
                padding-top: 76px;
                padding-bottom: 135px;
            }

            .ocean-footer-brand { grid-area: brand; }
            .ocean-footer-nav { grid-area: nav; justify-content: flex-start; }
            .ocean-footer-social { grid-area: social; justify-content: flex-start; }
            .ocean-footer-slogan { grid-area: slogan; }
            .ocean-footer-divider { display: none; }
            .ocean-footer-shell { position: absolute; right: 0; top: 70px; }
            .ocean-footer-sea { height: 155px; }
        }

        @media (max-width: 640px) {
            .ocean-reference-footer {
                min-height: 485px;
                margin-top: 64px;
            }

            .ocean-footer-main {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: calc(100% - 40px);
                padding: 72px 0 160px;
                gap: 22px;
                text-align: center;
            }

            .ocean-footer-wordmark span {
                font-size: 36px;
            }

            .ocean-footer-brand p {
                font-size: 10px;
            }

            .ocean-footer-nav {
                flex-wrap: wrap;
                gap: 12px 23px;
                justify-content: center;
            }

            .ocean-footer-nav a {
                font-size: 11px;
            }

            .ocean-footer-social {
                justify-content: center;
            }

            .ocean-footer-slogan {
                font-size: 11px;
                text-align: center;
            }

            .ocean-footer-shell {
                position: static;
                width: 52px;
                height: 52px;
            }

            .ocean-footer-sea {
                height: 148px;
            }

            .ocean-footer-bottom {
                left: 20px;
                right: 20px;
                bottom: 17px;
                flex-direction: column;
                gap: 5px;
                font-size: 9px;
                text-align: center;
            }
        }
    </style>
</x-layouts.app>
=======
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
>>>>>>> Stashed changes
