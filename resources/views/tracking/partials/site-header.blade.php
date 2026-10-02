<div class="tracking-site-header" aria-label="Navigasi Ocean Paws">
    <header class="op-browser op-header tracking-site-browser op-mobile-navbar">
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
                <a href="{{ route('home') }}">
                    <svg class="tracking-nav-icon tracking-nav-icon-home" viewBox="2 2 20 20" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M12 2.5 2.8 10a1 1 0 0 0 .63 1.78h1.1V20a1.5 1.5 0 0 0 1.5 1.5h4.15v-5.7h3.64v5.7h4.15a1.5 1.5 0 0 0 1.5-1.5v-8.22h1.1A1 1 0 0 0 21.2 10L12 2.5Z"/>
                    </svg>
                    <span>Home</span>
                </a>

                <a class="is-active" href="{{ route('tracking.index') }}" aria-current="page">
                    <svg class="tracking-nav-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M5 3h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-.5a2.5 2.5 0 0 1-5 0h-3a2.5 2.5 0 0 1-5 0H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 3v5h12V6H6Zm1.5 9.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
                    </svg>
                    <span>Tracking</span>
                </a>

                @auth
                    <a href="{{ route('profile.show') }}">
                        <svg class="tracking-nav-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="currentColor" fill-rule="evenodd" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm0 16a7.96 7.96 0 0 1-6.1-2.8C6.8 14.7 9.15 13 12 13s5.2 1.7 6.1 4.2A7.96 7.96 0 0 1 12 20Z" clip-rule="evenodd"/>
                        </svg>
                        <span>Profile</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>
</div>
