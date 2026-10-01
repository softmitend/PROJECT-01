@php
    $member = auth()->user()?->member;
    $homeActive = request()->routeIs(['home', 'tracking.*']);
    $ordersActive = request()->routeIs(['orders.*', 'billing.*', 'services.*']);
    $profileActive = request()->routeIs(['profile.*', 'testimonials.*']);
    $isPortalDetail = request()->routeIs('billing.*') || (request()->routeIs('orders.*') && ! request()->routeIs('orders.index'));
@endphp

@if($isPortalDetail)
    <header aria-label="Akun customer" style="position:absolute;z-index:60;top:clamp(22px,2.5vw,34px);left:0;right:0;pointer-events:none;">
        <div style="width:min(calc(100% - clamp(28px,3.2vw,44px)),1180px);margin:0 auto;display:flex;justify-content:flex-end;align-items:center;">
            @if($member)
                <a href="{{ route('profile.show') }}" class="public-account-chip" style="pointer-events:auto;">
                    @if($member->avatar_url)
                        <img src="{{ $member->avatar_url }}" alt="Foto profil {{ $member->display_name }}">
                    @else
                        <span>{{ mb_strtoupper(mb_substr($member->display_name, 0, 1)) }}</span>
                    @endif
                    <strong>{{ $member->display_name }}</strong>
                </a>
            @else
                <a href="{{ route('line-auth.redirect') }}" class="public-line-login" style="pointer-events:auto;">Login LINE</a>
            @endif
        </div>
    </header>
@else
    <header class="public-topbar">
        <div class="public-topbar-inner">
            <nav class="public-desktop-nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="{{ $homeActive ? 'active' : '' }}">
                    <x-public-icon name="home" :size="16" /><span>Home</span>
                </a>
                @if($member)
                    <a href="{{ route('orders.index') }}" class="{{ $ordersActive ? 'active' : '' }}">
                        <x-public-icon name="bag" :size="16" /><span>Pesanan</span>
                    </a>
                @endif
                <a href="{{ route('profile.show') }}" class="{{ $profileActive ? 'active' : '' }}">
                    <x-public-icon name="user" :size="16" /><span>Profil</span>
                </a>
            </nav>

            @if($member)
                <a href="{{ route('profile.show') }}" class="public-account-chip">
                    @if($member->avatar_url)
                        <img src="{{ $member->avatar_url }}" alt="">
                    @else
                        <span>{{ mb_strtoupper(mb_substr($member->display_name, 0, 1)) }}</span>
                    @endif
                    <strong>{{ $member->display_name }}</strong>
                </a>
            @else
                <a href="{{ route('line-auth.redirect') }}" class="public-line-login">Login LINE</a>
            @endif
        </div>
    </header>
@endif
