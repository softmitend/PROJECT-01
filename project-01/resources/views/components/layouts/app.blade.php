<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Rekap Jajanan') }}</title>
        @if (file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @elseif (file_exists(public_path('build/manifest.json')))
            @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
            @if (isset($manifest['resources/css/app.css']['file']))
                <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
            @endif
            @if (isset($manifest['resources/js/app.js']['file']))
                <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
            @endif
        @endif
    </head>
    <body class="bg-zinc-50 text-zinc-900 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-zinc-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="/" class="text-lg font-semibold">Rekap Jajanan Member</a>
                    <nav class="flex flex-wrap items-center gap-2 text-sm">
                        <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="/">Tracking</a>
                        @auth
                            <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin">Admin</a>
                            <form method="POST" action="/logout">
                                @csrf
                                <button class="rounded-md bg-zinc-900 px-3 py-2 text-white" type="submit">Logout</button>
                            </form>
                        @else
                            <a class="rounded-md bg-zinc-900 px-3 py-2 text-white" href="/login">Login Admin</a>
                        @endauth
                    </nav>
                </div>
            </header>

            @auth
                <div class="border-b border-zinc-200 bg-white">
                    <nav class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 py-2 text-sm sm:px-6 lg:px-8">
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/members">Member</a>
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/batches">Batch</a>
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/member-orders">Pesanan</a>
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/products">Jajanan</a>
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/order-statuses">Status</a>
                        <a class="shrink-0 rounded-md px-3 py-2 hover:bg-zinc-100" href="/admin/status-histories">Riwayat</a>
                    </nav>
                </div>
            @endauth

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
