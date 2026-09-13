@php
    $member = auth()->user()?->member;
    $activeTarget = request()->routeIs(['profile.*', 'testimonials.*'])
        ? 'profile'
        : (request()->routeIs(['orders.*', 'billing.*', 'services.*']) ? 'orders' : 'home');
@endphp

<nav class="bottom-nav" aria-label="Navigasi utama">
    <a href="{{ route('home', [], false) }}" class="nav-item {{ $activeTarget === 'home' ? 'active' : '' }}">
        <x-public-icon name="home" :size="18" /><span>Home</span>
    </a>
    @if($member)
        <a href="{{ route('orders.index', [], false) }}" class="nav-item {{ $activeTarget === 'orders' ? 'active' : '' }}">
            <x-public-icon name="bag" :size="18" /><span>Pesanan</span>
        </a>
    @endif
    <a href="{{ route('profile.show', [], false) }}" class="nav-item {{ $activeTarget === 'profile' ? 'active' : '' }}">
        <x-public-icon name="user" :size="18" /><span>Profil</span>
    </a>
</nav>
