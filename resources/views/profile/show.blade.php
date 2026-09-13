@php
    $isMember = (bool) $member;
    $isAdminUser = (bool) ($user && !$member);
    $loginUrl = route('line-auth.redirect');
    $profileName = $member?->display_name ?: ($isAdminUser ? $user->name : 'Login');
    $profileActionUrl = $isMember ? route('orders.history') : ($isAdminUser ? route('admin.dashboard') : $loginUrl);
    $months = collect(range(5, 0))->map(fn ($offset) => now()->subMonths($offset));
    $monthlyOrders = $months->map(fn ($month) => $orders->filter(fn ($order) => $order->created_at->isSameMonth($month))->count());
    $maxMonthlyOrders = max(1, (int) $monthlyOrders->max());
    $monthStartOffset = now()->startOfMonth()->dayOfWeek;
    $daysTogether = $member
        ? max(0, (int) floor($member->created_at->copy()->startOfDay()->diffInDays(now()->startOfDay())))
        : 0;
@endphp

<x-layouts.app title="Profil Member — Ocean Paws">
    <main class="page">
        <div class="page-narrow profile-stack">
            @error('line')
                <div class="profile-login-notice" role="alert">{{ $message }}</div>
            @enderror

            <section class="profile-main">
                <div class="profile-top">
                    <div class="profile-person">
                        @if($isMember)
                            <div class="profile-photo">
                                @if($member->avatar_url)<img src="{{ $member->avatar_url }}" alt="Foto profil {{ $member->display_name }}">@else<span>{{ mb_strtolower(mb_substr($member->display_name, 0, 1)) }}</span>@endif
                            </div>
                            <div><strong class="block text-[14px]">{{ $profileName }}</strong><span class="member-pill">MEMBER <x-public-icon name="chevron-right" :size="10" /></span></div>
                        @elseif($isAdminUser)
                            <a href="{{ route('admin.dashboard') }}" class="profile-person">
                                <div class="profile-photo"><span>{{ mb_strtolower(mb_substr($profileName, 0, 1)) }}</span></div>
                                <div><strong class="block text-[14px]">{{ $profileName }}</strong><span class="profile-login-label">ADMIN <x-public-icon name="chevron-right" :size="10" /></span></div>
                            </a>
                        @else
                            <a href="{{ $loginUrl }}" class="profile-person">
                                <div class="profile-photo is-empty"><span aria-hidden="true"></span></div>
                                <div><strong class="block text-[14px]" data-i18n-id="Login" data-i18n-en="Login">Login</strong><span class="profile-login-label"><span data-i18n-id="MASUK DENGAN LINE" data-i18n-en="LOGIN WITH LINE">MASUK DENGAN LINE</span> <x-public-icon name="chevron-right" :size="10" /></span></div>
                            </a>
                        @endif
                    </div>

                    @if($isMember)
                        <button type="button" class="settings-btn" aria-label="Buka pengaturan" aria-expanded="false" aria-controls="profile-settings-panel" data-profile-settings-button><x-public-icon name="settings" :size="18" /></button>
                    @elseif($isAdminUser)
                        <a href="{{ route('admin.dashboard') }}" class="settings-btn" aria-label="Dashboard admin"><x-public-icon name="grid" :size="18" /></a>
                    @endif
                </div>

                @if($isMember)
                    <section id="profile-settings-panel" class="profile-settings-panel" data-profile-settings-panel hidden>
                        <header>
                            <div><span class="micro" data-i18n-id="PREFERENSI" data-i18n-en="PREFERENCES">PREFERENSI</span><strong data-i18n-id="Pengaturan akun" data-i18n-en="Account settings">Pengaturan akun</strong></div>
                            <button type="button" aria-label="Tutup pengaturan" data-profile-settings-close><x-public-icon name="close" :size="15" /></button>
                        </header>

                        <label class="profile-preference-card">
                            <span class="profile-preference-icon"><x-public-icon name="radio" :size="17" /></span>
                            <span class="profile-preference-copy"><strong data-i18n-id="Bahasa" data-i18n-en="Language">Bahasa</strong><small data-i18n-id="Bahasa antarmuka" data-i18n-en="Interface language">Bahasa antarmuka</small></span>
                            <span class="profile-switch-choice"><b>ID</b><input type="checkbox" data-profile-language-switch aria-label="Gunakan bahasa Inggris"><span class="profile-toggle-track"><i></i></span><b>EN</b></span>
                        </label>

                        <label class="profile-preference-card">
                            <span class="profile-preference-icon"><x-public-icon name="sparkle" :size="17" /></span>
                            <span class="profile-preference-copy"><strong data-i18n-id="Tampilan" data-i18n-en="Appearance">Tampilan</strong><small data-i18n-id="Mode halaman" data-i18n-en="Page theme">Mode halaman</small></span>
                            <span class="profile-switch-choice is-theme"><b data-i18n-id="Light" data-i18n-en="Light">Light</b><input type="checkbox" data-profile-theme-switch aria-label="Gunakan mode gelap"><span class="profile-toggle-track"><i></i></span><b data-i18n-id="Dark" data-i18n-en="Dark">Dark</b></span>
                        </label>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="profile-logout-button"><span data-i18n-id="Keluar dari akun" data-i18n-en="Log out">Keluar dari akun</span></button>
                        </form>
                    </section>
                @endif

                <div class="coin-row">
                    <div class="coin-left"><span class="coin-icon"><x-public-icon name="link" :size="16" /></span><div><strong class="block text-[10px]">Ocean Coin</strong><span class="block text-[10px] text-[#5680aa]" data-i18n-id="Kumpulkan 500 untuk ditukar" data-i18n-en="Collect 500 to redeem">Kumpulkan 500 untuk ditukar</span></div></div>
                    <div class="flex items-center gap-1 font-extrabold">0 <x-public-icon name="chevron-right" :size="13" /></div>
                </div>
            </section>

            @if(!$user && !$lineConfigured)
                <p class="profile-line-config"><x-public-icon name="link" :size="14" /> LINE Login belum dikonfigurasi oleh admin.</p>
            @endif

            <section class="section-card orders-card">
                <h2 class="text-[12px] font-black" data-i18n-id="Pesanan Saya" data-i18n-en="My Orders">Pesanan Saya</h2>
                <div class="orders-icons">
                    @foreach([
                        ['wallet', 'Belum Bayar', 'Unpaid', $stats['unpaid'], 'unpaid', 'orders.unpaid'],
                        ['document', 'EMS', 'EMS & Tax', $stats['ems'], 'ems', 'billing.ems'],
                        ['history', 'History Jajanan', 'Order History', $stats['history'], 'history', 'orders.history'],
                        ['truck', 'Pengiriman', 'Shipping', $stats['shipping'], 'shipping', 'orders.shipping'],
                        ['swap', 'Refund', 'Refund', $stats['refund'], 'refund', 'orders.refunds'],
                    ] as [$icon, $label, $englishLabel, $count, $filter, $routeName])
                        <a class="order-link" href="{{ $isMember ? route($routeName) : $profileActionUrl }}" data-profile-filter="{{ $filter }}">
                            <span class="order-bubble"><x-public-icon :name="$icon" :size="18" />@if($count)<b>{{ $count }}</b>@endif</span><small data-i18n-id="{{ $label }}" data-i18n-en="{{ $englishLabel }}">{{ $label }}</small>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="section-card activity-card">
                <div class="activity-head"><span class="icon-chip"><x-public-icon name="sparkle" :size="20" /></span><div><strong class="block text-[12px]" data-i18n-id="Rekap aktivitas kamu" data-i18n-en="Your activity recap">Rekap aktivitas kamu</strong><span class="block text-[10px] text-[#5a7fa4]" data-i18n-id="{{ $isMember ? 'Semua aktivitas akun LINE kamu.' : 'Login untuk melihat aktivitasmu.' }}" data-i18n-en="{{ $isMember ? 'All activity from your LINE account.' : 'Login to view your activity.' }}">{{ $isMember ? 'Semua aktivitas akun LINE kamu.' : 'Login untuk melihat aktivitasmu.' }}</span></div></div>
                <div class="activity-summary">
                    <div class="metric-card"><span class="tiny-icon"><x-public-icon name="history" :size="16" /></span><div class="metric-copy"><strong>{{ $summary['orders'] }}</strong><span data-i18n-id="Total pesanan" data-i18n-en="Total orders">Total pesanan</span></div></div>
                    <div class="metric-card"><span class="tiny-icon"><x-public-icon name="box" :size="16" /></span><div class="metric-copy"><strong>{{ $summary['items'] }}</strong><span data-i18n-id="Jajanan tercatat" data-i18n-en="Recorded items">Jajanan tercatat</span></div></div>
                    <div class="metric-card"><span class="tiny-icon"><x-public-icon name="truck" :size="16" /></span><div class="metric-copy"><strong>{{ $stats['shipping'] }}</strong><span data-i18n-id="Paket dikirim" data-i18n-en="Shipped packages">Paket dikirim</span></div></div>
                    <div class="metric-card"><span class="tiny-icon"><x-public-icon name="calendar" :size="16" /></span><div class="metric-copy"><strong>{{ $daysTogether }}</strong><span data-i18n-id="Hari bareng Ocean Paws" data-i18n-en="Days with Ocean Paws">Hari bareng Ocean Paws</span></div></div>
                </div>
                <div class="chart-area">
                    <div class="chart-head"><span class="micro !text-[9px]" data-i18n-id="6 BULAN TERAKHIR" data-i18n-en="LAST 6 MONTHS">6 BULAN TERAKHIR</span><span class="chart-profile-total" data-i18n-id="{{ $monthlyOrders->sum() }} pesanan" data-i18n-en="{{ $monthlyOrders->sum() }} orders">{{ $monthlyOrders->sum() }} pesanan</span></div>
                    <div class="chart-grid">
                        <div class="chart-y"><span>{{ $maxMonthlyOrders }}</span><span>{{ (int) ceil($maxMonthlyOrders / 2) }}</span><span>0</span></div>
                        @foreach($months as $index => $month)
                            @php($barHeight = $monthlyOrders[$index] ? max(12, ($monthlyOrders[$index] / $maxMonthlyOrders) * 100) : 5)
                            <div class="bar-wrap"><div class="chart-bar" style="height:{{ $barHeight }}%; animation-delay:{{ $index * .07 }}s"></div><span class="bar-label">{{ $month->translatedFormat('M') }}</span></div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-card calendar-card">
                @php($monthItemCount = $orders->filter(fn ($order) => $order->created_at->isSameMonth(now()))->sum(fn ($order) => $order->items->sum('quantity')))
                <div class="calendar-head"><span class="cal-arrow"><x-public-icon name="arrow-left" :size="14" /></span><div class="calendar-title"><strong>{{ now()->translatedFormat('F Y') }}</strong><small data-i18n-id="{{ $monthItemCount }} Item Jajanan Bulan Ini" data-i18n-en="{{ $monthItemCount }} Items This Month">{{ $monthItemCount }} Item Jajanan Bulan Ini</small></div><span class="cal-arrow"><x-public-icon name="arrow-right" :size="14" /></span></div>
                <div class="weekdays">@foreach([['Min','Sun'],['Sen','Mon'],['Sel','Tue'],['Rab','Wed'],['Kam','Thu'],['Jum','Fri'],['Sab','Sat']] as [$day, $englishDay])<span data-i18n-id="{{ $day }}" data-i18n-en="{{ $englishDay }}">{{ $day }}</span>@endforeach</div>
                <div class="calendar-grid">
                    @for($blank = 0; $blank < $monthStartOffset; $blank++)<span class="calendar-day"></span>@endfor
                    @for($day = 1; $day <= now()->daysInMonth; $day++)<span class="calendar-day">{{ $day }}</span>@endfor
                </div>
            </section>

        </div>
    </main>
</x-layouts.app>
