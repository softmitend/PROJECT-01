@php
    $landingUrl = route('tracking.index', [], false);
    $activeTarget = request()->routeIs('profile.*')
        ? 'profile'
        : (request()->routeIs('tracking.search') ? 'tracking' : 'home');
@endphp

<nav class="tracker-luna-dock" aria-label="Navigasi utama" data-mobile-dock data-active-target="{{ $activeTarget }}">
    <a href="{{ $landingUrl }}#home" data-dock-target="home" @class(['is-active' => $activeTarget === 'home'])>
        <i class="bi bi-house" aria-hidden="true"></i><span>Home</span>
    </a>
    <a href="{{ $landingUrl }}#tracking" data-dock-target="tracking" @class(['is-active' => $activeTarget === 'tracking'])>
        <i class="bi bi-receipt" aria-hidden="true"></i><span>Tagihan</span>
    </a>
    <a href="{{ $landingUrl }}#services" data-dock-target="services">
        <i class="bi bi-grid" aria-hidden="true"></i><span>Layanan</span>
    </a>
    <a href="{{ route('profile.show', [], false) }}" data-dock-target="profile" @class(['is-active' => $activeTarget === 'profile'])>
        <i class="bi bi-person-circle" aria-hidden="true"></i><span>Profil</span>
    </a>
</nav>
