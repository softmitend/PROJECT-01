<div class="tracking-site-header">
    <header class="op-browser op-header op-mobile-navbar">
        <div class="op-browserbar">
            <span class="op-window-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        </div>
        <div class="op-navrow">
            <a class="op-brand" href="{{ route('home') }}" aria-label="Ocean Paws home">
                <span class="op-brand-logo">
                    <img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws — Bringing your favorites closer">
                </span>
            </a>
            <nav class="op-nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}"><i class="bi bi-house-heart-fill"></i><span>Home</span></a>
                <a class="is-active" href="{{ route('tracking.index') }}" aria-current="page"><i class="bi bi-truck-front-fill"></i><span>Tracking</span></a>
                @auth
                    <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i><span>Profile</span></a>
                @endauth
            </nav>
        </div>
    </header>
</div>
