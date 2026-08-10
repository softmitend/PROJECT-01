<x-layouts.app title="Cek Pesanan dan Riwayat">
    <section class="relative isolate overflow-hidden bg-white">
        <div class="site-grid absolute inset-0 -z-20"></div>
        <div class="absolute left-1/2 top-28 -z-10 h-64 w-64 -translate-x-[115%] rounded-full bg-violet-300/35 blur-3xl sm:h-96 sm:w-96"></div>
        <div class="absolute left-1/2 top-40 -z-10 h-64 w-64 translate-x-[15%] rounded-full bg-cyan-200/40 blur-3xl sm:h-96 sm:w-96"></div>

        <div class="mx-auto max-w-7xl px-4 pb-20 pt-20 text-center sm:px-6 sm:pb-28 sm:pt-28 lg:px-8 lg:pt-32">
            <div class="mx-auto inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/80 px-3 py-1.5 text-xs font-semibold text-zinc-600 shadow-sm backdrop-blur">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                K-pop merch order tracker
            </div>
            <h1 class="mx-auto mt-7 max-w-5xl text-5xl font-bold leading-[0.98] tracking-[-0.045em] text-zinc-950 sm:text-7xl lg:text-[5.75rem]">
                Semua pesananmu,<br><span class="gradient-title">jelas progresnya.</span>
            </h1>
            <p class="mx-auto mt-7 max-w-2xl text-base leading-7 text-zinc-500 sm:text-xl sm:leading-8">
                Cek perjalanan satu pesanan dengan kode, atau temukan seluruh riwayat pembelian lewat email. Cepat, transparan, tanpa akun customer.
            </p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="#track-code" class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-zinc-950 px-7 text-sm font-semibold text-white shadow-lg shadow-zinc-950/15 transition hover:-translate-y-0.5 hover:bg-violet-700">
                    Cek status pesanan
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="h-4 w-4" stroke-width="1.8" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                </a>
                <a href="#history-search" class="inline-flex h-12 items-center justify-center rounded-full border border-zinc-200 bg-white/70 px-7 text-sm font-semibold text-zinc-800 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-zinc-400">Lihat riwayat pembelian</a>
            </div>
            <div class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs font-medium text-zinc-500 sm:text-sm">
                <span class="inline-flex items-center gap-2"><span class="text-emerald-500">●</span> Tanpa login</span>
                <span class="inline-flex items-center gap-2"><span class="text-violet-500">●</span> Status per produk</span>
                <span class="inline-flex items-center gap-2"><span class="text-cyan-500">●</span> Riwayat real-time</span>
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="absolute inset-x-20 top-10 -z-10 h-40 bg-gradient-to-r from-violet-200/50 via-blue-200/40 to-cyan-200/50 blur-3xl"></div>
            <div class="grid gap-5 lg:grid-cols-2">
                <form id="track-code" method="POST" action="{{ route('tracking.lookup', [], false) }}" class="soft-card scroll-mt-28 rounded-3xl p-5 text-left sm:p-7">
                    @csrf
                    <div class="mb-7 flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.18em] text-violet-600">Cek progress</span>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight">Lacak dengan kode</h2>
                            <p class="mt-2 text-sm leading-6 text-zinc-500">Lihat status batch dan setiap produk dalam satu pesanan.</p>
                        </div>
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-violet-100 text-violet-700">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8" aria-hidden="true"><path d="m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                        </span>
                    </div>
                    <x-text-input label="Kode pesanan" name="lookup" placeholder="Contoh: ORD-GO-NCT-0002" required autocomplete="off" />
                    <button class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-zinc-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-violet-700 focus:outline-none focus:ring-4 focus:ring-violet-200" type="submit">Tampilkan progress</button>
                </form>

                <form id="history-search" method="POST" action="{{ route('tracking.history.lookup', [], false) }}" class="soft-card scroll-mt-28 rounded-3xl p-5 text-left sm:p-7">
                    @csrf
                    <div class="mb-7 flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Riwayat pembelian</span>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight">Cari dengan email</h2>
                            <p class="mt-2 text-sm leading-6 text-zinc-500">Temukan semua pembelian yang didaftarkan admin untukmu.</p>
                        </div>
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-cyan-100 text-cyan-700">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8" aria-hidden="true"><path d="M3 6.5h18v11H3z"/><path d="m3 7 9 6 9-6"/></svg>
                        </span>
                    </div>
                    <x-text-input label="Email pelanggan" name="email" type="email" placeholder="nama@email.com" required autocomplete="email" />
                    <button class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-zinc-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-200" type="submit">Tampilkan semua riwayat</button>
                </form>
            </div>
        </div>
    </section>

    @php
        $stages = [
            ['Dipesan', 'Data pembelian tercatat', 'bg-violet-500'],
            ['Warehouse', 'Merch tiba di gudang', 'bg-blue-500'],
            ['Pengiriman', 'Sedang menuju Indonesia', 'bg-cyan-500'],
            ['Tiba di Admin', 'Pesanan sedang disortir', 'bg-amber-500'],
            ['Siap Dikirim', 'Menunggu pengiriman lokal', 'bg-emerald-500'],
            ['Selesai', 'Pesanan diterima customer', 'bg-zinc-900'],
        ];
    @endphp
    <section class="overflow-hidden border-y border-zinc-200 bg-zinc-950 py-16 text-white">
        <div class="mx-auto mb-10 max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-300">Satu alur yang mudah dipahami</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Dari checkout sampai tiba di tanganmu.</h2>
        </div>
        <div class="flex w-max tracking-marquee">
            @foreach ([...$stages, ...$stages] as $stage)
                <div class="mx-2 w-72 shrink-0 rounded-2xl border border-white/10 bg-white/[0.06] p-5">
                    <div class="flex items-center gap-3">
                        <span class="h-2.5 w-2.5 rounded-full {{ $stage[2] }}"></span>
                        <span class="font-semibold">{{ $stage[0] }}</span>
                    </div>
                    <p class="mt-3 text-sm text-zinc-400">{{ $stage[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white px-4 py-24 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Sesederhana itu</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-5xl">Informasi yang kamu butuhkan, tanpa langkah berlebih.</h2>
            </div>
            <div class="mt-12 grid gap-px overflow-hidden rounded-3xl border border-zinc-200 bg-zinc-200 md:grid-cols-3">
                <article class="bg-white p-7 sm:p-9">
                    <span class="font-mono text-sm text-violet-600">01</span>
                    <h3 class="mt-8 text-xl font-bold">Masukkan kode</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-500">Gunakan kode unik dari admin untuk mengecek progres satu pesanan secara langsung.</p>
                </article>
                <article class="bg-white p-7 sm:p-9">
                    <span class="font-mono text-sm text-cyan-700">02</span>
                    <h3 class="mt-8 text-xl font-bold">Cari email</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-500">Masukkan email yang terdaftar untuk membuka daftar dan riwayat seluruh pembelian.</p>
                </article>
                <article class="bg-white p-7 sm:p-9">
                    <span class="font-mono text-sm text-emerald-700">03</span>
                    <h3 class="mt-8 text-xl font-bold">Pantau dengan tenang</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-500">Status batch dan produk tersaji jelas, dari pembelian hingga pesanan selesai.</p>
                </article>
            </div>
        </div>
    </section>
</x-layouts.app>
