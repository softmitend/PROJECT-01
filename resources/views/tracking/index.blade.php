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

    <style>
        .op-footer-reference{
            position:relative;
            isolation:isolate;
            min-height:330px;
            overflow:hidden;
            background:#f7f3e9;
            color:#315d48;
            font-family:"Nunito Sans",sans-serif;
        }
        .op-footer-reference::before{
            content:"";
            position:absolute;
            z-index:1;
            top:0;
            left:-2%;
            width:104%;
            height:38px;
            border-top:3px solid #74a884;
            border-radius:50% 50% 0 0/100% 100% 0 0;
            transform:translateY(8px) rotate(-.35deg);
            pointer-events:none;
        }
        .op-footer-reference .op-footer-reference-shell{
            position:relative;
            z-index:4;
            display:grid;
            width:min(100% - 80px,1240px);
            margin:0 auto;
            grid-template-columns:minmax(235px,1.05fr) auto minmax(230px,.95fr);
            align-items:start;
            gap:34px;
            padding:54px 0 145px;
        }
        .op-footer-reference .op-footer-brand-block{
            min-width:0;
        }
        .op-footer-reference .op-footer-brand-block img{
            display:block;
            width:220px;
            max-width:100%;
            height:auto;
            margin-left:-5px;
        }
        .op-footer-reference .op-footer-brand-block p{
            margin:3px 0 0;
            color:#3f7058;
            font-size:13px;
            font-weight:600;
            letter-spacing:.01em;
        }
        .op-footer-reference .op-footer-center{
            display:flex;
            align-items:flex-start;
            gap:30px;
            padding-top:29px;
        }
        .op-footer-reference .op-footer-links,
        .op-footer-reference .op-footer-socials{
            display:flex;
            align-items:center;
            gap:22px;
        }
        .op-footer-reference .op-footer-links{
            padding:0 29px;
            border-right:1px solid rgba(49,93,72,.48);
            border-left:1px solid rgba(49,93,72,.48);
        }
        .op-footer-reference .op-footer-links a,
        .op-footer-reference .op-footer-socials a{
            color:#315d48;
            text-decoration:none;
            transition:transform .18s ease,opacity .18s ease;
        }
        .op-footer-reference .op-footer-links a{
            font-size:13px;
            font-weight:700;
            white-space:nowrap;
        }
        .op-footer-reference .op-footer-socials a{
            display:grid;
            width:24px;
            height:24px;
            place-items:center;
            font-size:18px;
        }
        .op-footer-reference .op-footer-links a:hover,
        .op-footer-reference .op-footer-socials a:hover{
            opacity:.72;
            transform:translateY(-2px);
        }
        .op-footer-reference .op-footer-message{
            position:relative;
            display:flex;
            min-width:0;
            justify-self:end;
            align-items:flex-start;
            gap:22px;
            padding-top:24px;
        }
        .op-footer-reference .op-footer-message strong{
            display:block;
            color:#315d48;
            font-family:"Nunito Sans",sans-serif;
            font-size:18px;
            font-weight:800;
            line-height:1.35;
            letter-spacing:-.02em;
            white-space:nowrap;
        }
        .op-footer-reference .op-footer-message strong span{
            display:block;
        }
        .op-footer-reference .op-footer-shell-icon{
            width:62px;
            height:56px;
            flex:none;
            transform:rotate(7deg);
            filter:drop-shadow(0 3px 0 rgba(137,73,94,.08));
        }
        .op-footer-reference .op-footer-waves{
            position:absolute;
            z-index:2;
            right:0;
            bottom:0;
            left:0;
            height:185px;
            pointer-events:none;
        }
        .op-footer-reference .op-footer-waves svg{
            position:absolute;
            inset:auto 0 0;
            width:100%;
            height:100%;
            display:block;
        }
        .op-footer-reference .op-footer-cloud{
            position:absolute;
            z-index:3;
            bottom:83px;
            width:160px;
            height:68px;
            opacity:.92;
            pointer-events:none;
        }
        .op-footer-reference .op-footer-cloud.is-left{left:-20px}
        .op-footer-reference .op-footer-cloud.is-right{right:-26px;transform:scaleX(-1)}
        .op-footer-reference .op-footer-bottomline{
            position:absolute;
            z-index:5;
            right:max(26px,calc((100% - 1240px)/2));
            bottom:25px;
            left:max(26px,calc((100% - 1240px)/2));
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            color:#f4fbf4;
            font-size:11px;
            font-weight:700;
            letter-spacing:.01em;
            text-shadow:0 1px 1px rgba(44,83,56,.18);
        }
        @media(max-width:980px){
            .op-footer-reference{min-height:405px}
            .op-footer-reference .op-footer-reference-shell{
                width:min(100% - 48px,900px);
                grid-template-columns:1fr 1fr;
                gap:20px 34px;
                padding-top:50px;
            }
            .op-footer-reference .op-footer-center{
                grid-column:1/-1;
                grid-row:2;
                justify-content:flex-start;
                padding-top:0;
            }
            .op-footer-reference .op-footer-message{
                grid-column:2;
                grid-row:1;
            }
        }
        @media(max-width:680px){
            .op-footer-reference{min-height:510px}
            .op-footer-reference .op-footer-reference-shell{
                width:min(100% - 36px,560px);
                grid-template-columns:1fr;
                gap:20px;
                padding:44px 0 175px;
            }
            .op-footer-reference .op-footer-brand-block img{width:190px}
            .op-footer-reference .op-footer-center,
            .op-footer-reference .op-footer-message{
                grid-column:1;
                grid-row:auto;
                justify-self:start;
            }
            .op-footer-reference .op-footer-center{
                flex-wrap:wrap;
                gap:18px;
            }
            .op-footer-reference .op-footer-links{
                gap:16px;
                padding:0 18px;
                border-left:0;
            }
            .op-footer-reference .op-footer-socials{gap:16px}
            .op-footer-reference .op-footer-message{
                padding-top:4px;
            }
            .op-footer-reference .op-footer-message strong{
                font-size:16px;
            }
            .op-footer-reference .op-footer-waves{height:180px}
            .op-footer-reference .op-footer-bottomline{
                right:18px;
                bottom:20px;
                left:18px;
                align-items:flex-start;
                flex-direction:column;
                gap:4px;
                font-size:10px;
            }
        }
        @media(max-width:430px){
            .op-footer-reference{min-height:535px}
            .op-footer-reference .op-footer-reference-shell{padding-top:38px}
            .op-footer-reference .op-footer-center{display:block}
            .op-footer-reference .op-footer-links{
                width:100%;
                justify-content:space-between;
                gap:10px;
                padding:0 0 15px;
                border:0;
                border-bottom:1px solid rgba(49,93,72,.35);
            }
            .op-footer-reference .op-footer-links a{font-size:12px}
            .op-footer-reference .op-footer-socials{
                margin-top:15px;
                justify-content:flex-start;
            }
            .op-footer-reference .op-footer-shell-icon{width:54px;height:48px}
        }
    </style>

    <footer class="op-footer op-footer-reference">
        <div class="op-footer-reference-shell">
            <div class="op-footer-brand-block">
                <a href="#home" aria-label="Ocean Paws — kembali ke atas">
                    <img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws">
                </a>
                <p>Bringing your favorites closer</p>
            </div>

            <div class="op-footer-center">
                <nav class="op-footer-links" aria-label="Navigasi footer">
                    <a href="#home">Home</a>
                    <a href="#about">Layanan</a>
                    <a href="{{ route('tracking.index') }}">Tracking</a>
                    <a href="#about">FAQ</a>
                </nav>
                <div class="op-footer-socials" aria-label="Media sosial Ocean Paws">
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="#" aria-label="X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a href="#" aria-label="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
                </div>
            </div>

            <div class="op-footer-message">
                <strong><span>Small Orders</span><span>Bigger Happiness ♡</span></strong>
                <svg class="op-footer-shell-icon" viewBox="0 0 96 82" aria-hidden="true">
                    <defs>
                        <linearGradient id="footerShellPink" x1="20" y1="8" x2="72" y2="72" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ffdce7"/>
                            <stop offset="1" stop-color="#eda2ba"/>
                        </linearGradient>
                    </defs>
                    <path d="M48 73C26 73 10 64 10 50c0-8 7-13 14-14-2-10 5-19 15-19 4-8 15-12 23-6 10-1 19 8 18 18 7 3 11 9 11 17 0 17-17 27-43 27Z" fill="url(#footerShellPink)" stroke="#9c5b72" stroke-width="3" stroke-linejoin="round"/>
                    <path d="M48 69V25M48 27 29 64M50 27l17 37M40 28 21 56M56 29l20 27M32 34l-13 13M64 35l13 12" fill="none" stroke="#c77c96" stroke-width="2.7" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <svg class="op-footer-cloud is-left" viewBox="0 0 180 72" aria-hidden="true">
            <path d="M0 57h172c4-13-5-23-19-23-3-17-24-22-36-10-8-20-36-21-45-2-13-9-33 1-31 17C21 35 3 44 0 57Z" fill="#fffaf2"/>
        </svg>
        <svg class="op-footer-cloud is-right" viewBox="0 0 180 72" aria-hidden="true">
            <path d="M0 57h172c4-13-5-23-19-23-3-17-24-22-36-10-8-20-36-21-45-2-13-9-33 1-31 17C21 35 3 44 0 57Z" fill="#fffaf2"/>
        </svg>

        <div class="op-footer-waves" aria-hidden="true">
            <svg viewBox="0 0 1440 230" preserveAspectRatio="none">
                <path d="M0 83C173 35 297 105 457 82c170-24 255-86 421-53 178 35 242 107 562 56V230H0V83Z" fill="#c8e1cb"/>
                <path d="M0 123c176 30 302-59 475-28 175 31 229 93 399 65 192-31 326-52 566 9v61H0V123Z" fill="#9fc7a7"/>
                <path d="M0 171c219-48 307 20 502 8 211-12 252-65 454-37 182 25 284 58 484 37v51H0v-59Z" fill="#78ab83"/>
                <path d="M87 158c41-15 87-14 126 3M337 185c52-24 107-24 163-4M720 155c61-22 124-18 178 3M1090 184c51-21 105-22 161-5" fill="none" stroke="#dcebdc" stroke-width="8" stroke-linecap="round" opacity=".7"/>
                <g fill="#dcebdc" opacity=".75">
                    <ellipse cx="250" cy="137" rx="13" ry="5"/><ellipse cx="271" cy="132" rx="8" ry="3"/>
                    <ellipse cx="622" cy="187" rx="15" ry="5"/><ellipse cx="649" cy="181" rx="9" ry="3"/>
                    <ellipse cx="1002" cy="142" rx="12" ry="4"/><ellipse cx="1023" cy="137" rx="7" ry="3"/>
                </g>
            </svg>
        </div>

        <div class="op-footer-bottomline">
            <span>© {{ now()->year }} OCEANPAWS. All rights reserved.</span>
            <span>For NCT WISH. Always. ♡</span>
        </div>
    </footer>
</div>
</body>
</html>
