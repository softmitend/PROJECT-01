<x-layouts.app :title="$adminLogin ? 'Login Admin' : 'Login Customer'">
    <x-auth-shell :admin="$adminLogin">
        <span class="auth-kicker">{{ $adminLogin ? 'ADMIN ACCESS' : 'WELCOME BACK' }}</span>
        <h1 class="auth-title">{{ $adminLogin ? 'Login Admin' : 'Login Customer' }}</h1>
        <p class="auth-description">{{ $adminLogin ? 'Masuk ke pengelolaan Ocean Paws.' : 'Masuk untuk melihat profil, pesanan, dan tagihanmu.' }}</p>
        <form method="POST" action="{{ $adminLogin ? route('admin.login.store') : route('login') }}" class="auth-form">
            @csrf
            @if($adminLogin)
                <x-text-input label="Email admin" name="email" type="email" autocomplete="username" required autofocus />
            @else
                <x-text-input label="Username" name="username" autocomplete="username" required autofocus />
            @endif
            <x-text-input label="Password" name="password" type="password" autocomplete="current-password" required />
            <label class="auth-remember">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Ingat sesi ini
            </label>
            <button type="submit" class="auth-submit">Masuk <span aria-hidden="true">↗</span></button>
        </form>
        @unless($adminLogin)
            <p class="auth-switch">Belum punya akun? <a class="auth-link" href="{{ route('register') }}">Daftar terlebih dahulu</a></p>
            <p class="auth-note">Customer LINE lama: hubungi admin untuk menyiapkan username dan password pada akun yang sama.</p>
        @endunless
        <a class="auth-secondary-link" href="{{ $adminLogin ? route('login') : route('admin.login') }}">{{ $adminLogin ? 'Login customer' : 'Login admin' }}</a>
    </x-auth-shell>
</x-layouts.app>

