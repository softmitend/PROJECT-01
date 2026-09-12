@php
    $activeTarget = request()->routeIs(['profile.*', 'testimonials.*'])
        ? 'profile'
        : (request()->routeIs('billing.*')
            ? 'tagihan'
            : (request()->routeIs(['services.*', 'tracking.*', 'orders.*']) ? 'layanan' : 'home'));
@endphp

<nav class="bottom-nav" aria-label="Navigasi utama">
    <a href="{{ route('home', [], false) }}" class="nav-item {{ $activeTarget === 'home' ? 'active' : '' }}">
        <x-public-icon name="home" :size="17" /><span>Home</span>
    </a>
    <a href="{{ route('billing.index', [], false) }}" class="nav-item {{ $activeTarget === 'tagihan' ? 'active' : '' }}">
        <x-public-icon name="card" :size="17" /><span>Tagihan</span>
    </a>
    <a href="{{ route('services.index', [], false) }}" class="nav-item {{ $activeTarget === 'layanan' ? 'active' : '' }}">
        <x-public-icon name="grid" :size="17" /><span>Layanan</span>
    </a>
    <a href="{{ route('profile.show', [], false) }}" class="nav-item {{ $activeTarget === 'profile' ? 'active' : '' }}">
        <x-public-icon name="user" :size="17" /><span>Profil</span>
    </a>
</nav>
