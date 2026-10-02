<style>
    html[data-customer-theme="dark"] body.ocean-profile {
        --profile-ink: #edf6ef;
        --profile-outline: #78977f;
        --profile-pink: #563642;
        --profile-pink-strong: #ef86a3;
        --profile-mint: #31483a;
        --profile-paper: #1b3027;
        --profile-muted: #aec0b4;
        color-scheme: dark;
        background:
            linear-gradient(rgba(151,181,159,.055) 1px, transparent 1px),
            linear-gradient(90deg, rgba(151,181,159,.055) 1px, transparent 1px),
            #12251e;
        background-size: 42px 42px;
        color: var(--profile-ink);
    }

    html[data-customer-theme="dark"] .ocean-profile .customer-public-shell,
    html[data-customer-theme="dark"] .ocean-profile .public-landing-main,
    html[data-customer-theme="dark"] .ocean-profile .profile-page,
    html[data-customer-theme="dark"] .profile-site-header {
        background: transparent !important;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-browser {
        border-color: #78977f;
        background: #1b3027;
        box-shadow: 0 3px 0 #081810;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-browserbar {
        border-bottom-color: #78977f;
        background: #2b4336;
        color: #dce9df;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-navrow {
        background: #1b3027;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-nav a,
    html[data-customer-theme="dark"] .profile-site-header .op-nav a i {
        color: #c4d4c9;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-nav a:hover,
    html[data-customer-theme="dark"] .profile-site-header .op-nav a.is-active,
    html[data-customer-theme="dark"] .profile-site-header .op-nav a.is-active i {
        color: #f08aa5;
    }

    html[data-customer-theme="dark"] .profile-site-header .op-nav a.is-active::after {
        background: #f08aa5;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-main,
    html[data-customer-theme="dark"] .ocean-profile .section-card,
    html[data-customer-theme="dark"] .ocean-profile .calendar-card,
    html[data-customer-theme="dark"] .ocean-profile .profile-snack-card {
        border-color: #78977f !important;
        background: #1b3027 !important;
        color: #edf6ef !important;
        box-shadow: -6px 6px 0 #081810 !important;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-top {
        border-color: #78977f;
        background: #4d303a;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-person strong,
    html[data-customer-theme="dark"] .ocean-profile .profile-orders-section h2,
    html[data-customer-theme="dark"] .ocean-profile .activity-head strong,
    html[data-customer-theme="dark"] .ocean-profile .profile-snack-heading h2,
    html[data-customer-theme="dark"] .ocean-profile .profile-snack-name,
    html[data-customer-theme="dark"] .ocean-profile .calendar-title strong {
        color: #edf6ef !important;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-photo,
    html[data-customer-theme="dark"] .ocean-profile .settings-btn {
        border-color: #78977f !important;
        background: #20382e !important;
        color: #edf6ef !important;
        box-shadow: 3px 3px 0 #081810 !important;
    }

    html[data-customer-theme="dark"] .ocean-profile .member-pill {
        border-color: #ad6078;
        background: #3f2b33;
        color: #f2a0b6 !important;
        box-shadow: 2px 2px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-settings-panel {
        border-color: #78977f;
        background: #182d24;
        box-shadow: -5px 5px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-settings-panel > header strong,
    html[data-customer-theme="dark"] .ocean-profile .profile-preference-copy strong {
        color: #edf6ef;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-settings-panel > header button,
    html[data-customer-theme="dark"] .ocean-profile .profile-preference-icon {
        border-color: #78977f;
        background: #4d303a;
        color: #f08aa5;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-preference-card {
        border-color: #557863;
        background: #20382e;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-preference-copy small,
    html[data-customer-theme="dark"] .ocean-profile .profile-switch-choice b,
    html[data-customer-theme="dark"] .ocean-profile .activity-head span:last-child,
    html[data-customer-theme="dark"] .ocean-profile .metric-copy span,
    html[data-customer-theme="dark"] .ocean-profile .profile-snack-date,
    html[data-customer-theme="dark"] .ocean-profile .chart-month-labels,
    html[data-customer-theme="dark"] .ocean-profile .profile-line-chart .chart-y {
        color: #aec0b4 !important;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-order-hub .order-link {
        border-color: #78977f;
        background: #3f2b33;
        box-shadow: 3px 3px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-order-hub .order-link:nth-child(2) {
        background: #263c30;
        box-shadow: 3px 3px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .order-link small {
        color: #edf6ef;
    }

    html[data-customer-theme="dark"] .ocean-profile .activity-head {
        border-color: #78977f !important;
        background: #263c30;
    }

    html[data-customer-theme="dark"] .ocean-profile .activity-summary,
    html[data-customer-theme="dark"] .ocean-profile .chart-area {
        border-color: #78977f !important;
        background: #1b3027 !important;
    }

    html[data-customer-theme="dark"] .ocean-profile .metric-card {
        border-color: #668a70 !important;
        background: #34282d !important;
        box-shadow: 3px 3px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .metric-card:nth-child(even) {
        background: #24382c !important;
        box-shadow: 3px 3px 0 #081810;
    }

    html[data-customer-theme="dark"] .ocean-profile .chart-profile-total,
    html[data-customer-theme="dark"] .ocean-profile .profile-snack-count {
        border-color: #ad6078;
        background: #462f38;
        color: #f08aa5;
    }

    html[data-customer-theme="dark"] .ocean-profile .chart-line-plot {
        background: repeating-linear-gradient(to bottom, transparent 0, transparent 49px, rgba(174,192,180,.18) 50px, transparent 51px);
    }

    html[data-customer-theme="dark"] .ocean-profile .chart-line-point {
        border-color: #1b3027;
        background: #ed7697;
        box-shadow: 0 0 0 1.5px #ad6078;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-snack-heading {
        border-color: #668a70;
    }

    html[data-customer-theme="dark"] .ocean-profile .profile-snack-list li {
        border-color: #557863;
        background: #20382e;
    }

    html[data-customer-theme="dark"] .ocean-profile .calendar-card,
    html[data-customer-theme="dark"] .ocean-profile .calendar-grid,
    html[data-customer-theme="dark"] .ocean-profile .weekdays {
        color: #dce9df;
    }
</style>

<div class="profile-site-header">
    <header class="op-browser op-header op-mobile-navbar">
        <div class="op-browserbar">
            <span class="op-window-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        </div>
        <div class="op-navrow">
            <a class="op-brand" href="{{ route('home') }}" aria-label="Ocean Paws home"><span class="op-brand-logo"><img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws — Bringing your favorites closer"></span></a>
            <nav class="op-nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}">
                    <svg class="profile-nav-icon profile-nav-icon-home" viewBox="2 2 20 20" width="22" height="22" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M12 2.5 2.8 10a1 1 0 0 0 .63 1.78h1.1V20a1.5 1.5 0 0 0 1.5 1.5h4.15v-5.7h3.64v5.7h4.15a1.5 1.5 0 0 0 1.5-1.5v-8.22h1.1A1 1 0 0 0 21.2 10L12 2.5Z"/>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('tracking.index') }}">
                    <svg class="profile-nav-icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M5 3h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-.5a2.5 2.5 0 0 1-5 0h-3a2.5 2.5 0 0 1-5 0H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 3v5h12V6H6Zm1.5 9.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
                    </svg>
                    <span>Tracking</span>
                </a>
                @auth
                    <a class="is-active" href="{{ route('profile.show') }}" aria-current="page">
                        <svg class="profile-nav-icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                            <path fill="currentColor" fill-rule="evenodd" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm0 16a7.96 7.96 0 0 1-6.1-2.8C6.8 14.7 9.15 13 12 13s5.2 1.7 6.1 4.2A7.96 7.96 0 0 1 12 20Z" clip-rule="evenodd"/>
                        </svg>
                        <span>Profile</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>
</div>
