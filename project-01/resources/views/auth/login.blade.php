<x-layouts.app title="Login Admin">
    <div class="mx-auto max-w-md rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Login Admin</h1>
        <form method="POST" action="/login" class="mt-5 space-y-4">
            @csrf
            <x-text-input label="Email" name="email" type="email" required autofocus />
            <x-text-input label="Password" name="password" type="password" required />
            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300">
                Ingat sesi ini
            </label>
            <button type="submit" class="w-full rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white">Masuk</button>
        </form>
    </div>
</x-layouts.app>
