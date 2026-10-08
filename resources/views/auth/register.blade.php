<x-layouts.app title="Daftar Customer">
    <div class="mx-auto max-w-lg rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Daftar Customer</h1>
        <p class="mt-2 text-sm text-zinc-600">Buat akun untuk memantau pesanan dan pembayaran.</p>
        @if($groups->isEmpty())
            <p class="mt-5 rounded-xl bg-amber-50 p-4 text-sm text-amber-900">Registrasi belum tersedia karena belum ada group aktif. Hubungi admin.</p>
        @else
            <form method="POST" action="{{ route('register') }}" class="mt-5 space-y-4">
                @csrf
                <x-text-input label="Nama" name="name" autocomplete="name" maxlength="255" required autofocus />
                <x-text-input label="Username" name="username" autocomplete="username" minlength="3" maxlength="50" pattern="[a-zA-Z0-9][a-zA-Z0-9._-]*" required />
                <p class="text-xs text-zinc-500">Gunakan huruf, angka, titik, garis bawah, atau tanda hubung. Username disimpan dalam huruf kecil.</p>
                <x-text-input label="Email" name="email" type="email" autocomplete="email" maxlength="255" required />
                <label class="block"><span class="text-sm font-semibold">Group</span>
                    <select name="customer_group_id" class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3" required>
                        <option value="">Pilih group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" @selected((string) old('customer_group_id') === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </label>
                <x-text-input label="Password (minimal 8 karakter)" name="password" type="password" autocomplete="new-password" minlength="8" required />
                <x-text-input label="Konfirmasi password" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required />
                <button type="submit" class="w-full rounded-xl bg-violet-700 px-4 py-3 font-semibold text-white">Daftar akun</button>
            </form>
        @endif
        <p class="mt-5 text-sm text-zinc-600">Sudah punya akun? <a class="font-semibold text-violet-700 underline" href="{{ route('login') }}">Login</a></p>
    </div>
</x-layouts.app>
