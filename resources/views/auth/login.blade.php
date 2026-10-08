<x-layouts.app :title="$adminLogin ? 'Login Admin' : 'Login Customer'">
    <div class="mx-auto max-w-md rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ $adminLogin ? 'Login Admin' : 'Login Customer' }}</h1>
        <p class="mt-2 text-sm text-zinc-600">{{ $adminLogin ? 'Masuk ke pengelolaan Ocean Paws.' : 'Masuk untuk melihat profil, pesanan, dan tagihanmu.' }}</p>
        <form method="POST" action="{{ $adminLogin ? route('admin.login.store') : route('login') }}" class="mt-5 space-y-4">
            @csrf
            @if($adminLogin)
                <x-text-input label="Email admin" name="email" type="email" autocomplete="username" required autofocus />
            @else
                <x-text-input label="Username" name="username" autocomplete="username" required autofocus />
            @endif
            <x-text-input label="Password" name="password" type="password" autocomplete="current-password" required />
            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300">
                Ingat sesi ini
            </label>
            <button type="submit" class="w-full rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white">Masuk</button>
        </form>
        @unless($adminLogin)
            <p class="mt-5 text-sm text-zinc-600">Belum punya akun? <a class="font-semibold text-violet-700 underline" href="{{ route('register') }}">Daftar terlebih dahulu</a></p>
            <p class="mt-3 text-xs text-zinc-500">Customer LINE lama: hubungi admin untuk menyiapkan username dan password pada akun yang sama.</p>
        @endunless
        <a class="mt-5 inline-block text-sm text-zinc-600 underline" href="{{ $adminLogin ? route('login') : route('admin.login') }}">{{ $adminLogin ? 'Login customer' : 'Login admin' }}</a>
    </div>
</x-layouts.app>

