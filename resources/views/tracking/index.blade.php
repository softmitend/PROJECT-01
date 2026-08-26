<x-layouts.app title="Cek Pesanan dan Riwayat">
    <section class="ocean-landing relative isolate overflow-hidden bg-white">
        <div class="site-grid absolute inset-0 -z-20"></div>
        <div class="absolute left-1/2 top-28 -z-10 h-64 w-64 -translate-x-[115%] rounded-full bg-sky-300/35 blur-3xl sm:h-96 sm:w-96"></div>
        <div class="absolute left-1/2 top-40 -z-10 h-64 w-64 translate-x-[15%] rounded-full bg-amber-100/70 blur-3xl sm:h-96 sm:w-96"></div>

        <div class="mx-auto max-w-7xl px-4 pb-20 pt-20 text-center sm:px-6 sm:pb-28 sm:pt-28 lg:px-8 lg:pt-32">
            <div class="mx-auto inline-flex items-center rounded-full border border-sky-100 bg-white/85 px-4 py-2 text-xs font-semibold text-[#176fa9] shadow-sm backdrop-blur">
                Ocean Paws order tracker
            </div>
            <h1 class="mx-auto mt-7 max-w-5xl text-5xl font-bold leading-[0.98] tracking-[-0.045em] text-zinc-950 sm:text-7xl lg:text-[5.75rem]">
                Semua pesananmu,<br><span class="gradient-title">jelas progresnya.</span>
            </h1>
            <p class="mx-auto mt-7 max-w-2xl text-base leading-7 text-zinc-500 sm:text-xl sm:leading-8">
                Cek perjalanan satu pesanan dengan kode, atau temukan seluruh riwayat pembelian lewat username LINE. Cepat, transparan, tanpa akun customer.
            </p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="#smart-search" class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-[#123c5a] px-7 text-sm font-semibold text-white shadow-lg shadow-sky-950/15 transition hover:-translate-y-0.5 hover:bg-[#176fa9]">
                    Cek status pesanan
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="h-4 w-4" stroke-width="1.8" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                </a>
                <a href="#smart-search" class="inline-flex h-12 items-center justify-center rounded-full border border-sky-100 bg-white/70 px-7 text-sm font-semibold text-[#123c5a] shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-sky-300">Lihat riwayat pembelian</a>
            </div>
            <div class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs font-medium text-zinc-500 sm:text-sm">
                <span class="inline-flex items-center gap-2"><span class="text-emerald-500">●</span> Tanpa login</span>
                <span class="inline-flex items-center gap-2"><span class="text-sky-500">●</span> Status per produk</span>
                <span class="inline-flex items-center gap-2"><span class="text-cyan-500">●</span> Riwayat real-time</span>
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="absolute inset-x-20 top-10 -z-10 h-40 bg-gradient-to-r from-sky-200/60 via-cyan-100/50 to-amber-100/70 blur-3xl"></div>
            <div id="smart-search" class="w-full scroll-mt-28">
                <form method="POST" action="{{ route('tracking.search', [], false) }}#smart-search" class="ocean-search-card text-left">
                    @csrf
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.18em] text-[#176fa9]">Tracking & riwayat pesanan</span>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#123c5a] sm:text-3xl">Cari pesananmu di satu tempat</h2>
                            <p class="mt-2 text-sm leading-6 text-zinc-500">Masukkan kode tracking untuk melihat satu pesanan, atau username LINE untuk membuka seluruh riwayat.</p>
                        </div>
                        <img src="{{ asset('img/Picsart_26-08-23_02-05-04-834.png') }}" alt="" class="hidden h-20 w-20 shrink-0 object-contain sm:block">
                    </div>

                    <label for="query" class="mt-7 block text-sm font-bold text-[#123c5a]">Kode tracking atau username LINE</label>
                    <div class="ocean-search-row mt-2">
                        <span class="text-[#2f8fd0]">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8" aria-hidden="true"><path d="m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                        </span>
                        <input id="query" name="query" value="{{ old('query', $searchQuery ?? '') }}" placeholder="Masukkan kode atau username LINE" required autocomplete="off" class="min-w-0 flex-1 bg-transparent text-sm text-zinc-900 outline-none placeholder:text-zinc-400 sm:text-base">
                        <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#123c5a] px-5 text-sm font-bold text-white transition hover:bg-[#176fa9] focus:outline-none focus:ring-4 focus:ring-sky-200">
                            Cari pesanan
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="h-4 w-4" stroke-width="1.8" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                        </button>
                    </div>
                    <div class="mt-3 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-zinc-500">Jenis pencarian dikenali otomatis dari data yang kamu masukkan.</p>
                        @error('query')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </form>

                @isset($orderResult)
                    @php
                        $progressSteps = $timeline->sortBy('created_at')->filter(fn ($history) => $history->newStatus)->unique('new_status_id')->values();
                    @endphp
                    <details open class="ocean-result-panel mt-5" data-smart-search-result="tracking">
                        <summary class="ocean-result-summary">
                            <span class="ocean-result-icon grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-sky-100 text-[#176fa9]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                            </span>
                            <span class="ocean-result-copy min-w-0 flex-1">
                                <span class="ocean-result-kicker block text-xs font-bold uppercase tracking-[0.16em] text-[#2f8fd0]">Hasil tracking</span>
                                <span class="ocean-result-title mt-1 block truncate text-lg font-bold text-[#123c5a]">{{ $orderResult->order_code }}</span>
                            </span>
                            <span class="ocean-result-badge"><x-status-badge :status="$orderResult->tracking_status" /></span>
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="ocean-chevron h-5 w-5 shrink-0 text-zinc-400" stroke-width="1.8" aria-hidden="true"><path d="m5 7 5 5 5-5"/></svg>
                        </summary>
                        <div class="border-t border-sky-100 p-5 sm:p-7">
                            <div class="grid gap-px overflow-hidden rounded-2xl border border-sky-100 bg-sky-100 sm:grid-cols-4">
                                <div class="bg-white p-4"><span class="ocean-data-label">Pelanggan</span><strong class="ocean-data-value">{{ $orderResult->member->display_name }}</strong></div>
                                <div class="bg-white p-4"><span class="ocean-data-label">Batch</span><strong class="ocean-data-value">{{ $orderResult->batch->batch_name ?: $orderResult->batch->batch_number }}</strong></div>
                                <div class="bg-white p-4"><span class="ocean-data-label">Jumlah</span><strong class="ocean-data-value">{{ $orderResult->items->sum('quantity') }} item</strong></div>
                                <div class="bg-white p-4"><span class="ocean-data-label">Pembayaran</span><strong class="ocean-data-value">{{ $orderResult->paymentStatus?->name ?: 'Belum ditentukan' }}</strong></div>
                            </div>

                            <div class="mt-7">
                                <p class="ocean-section-label">Perjalanan pesanan</p>
                                <div class="ocean-progress-list mt-4">
                                    @forelse ($progressSteps as $step)
                                        <div class="ocean-progress-step">
                                            <span class="ocean-progress-dot"></span>
                                            <strong>{{ $step->newStatus->name }}</strong>
                                            <small>{{ $step->created_at->format('d M Y') }}</small>
                                        </div>
                                    @empty
                                        <div class="ocean-progress-step is-current">
                                            <span class="ocean-progress-dot"></span>
                                            <strong>{{ $orderResult->tracking_status?->name ?: 'Pesanan tercatat' }}</strong>
                                            <small>{{ $orderResult->updated_at->format('d M Y') }}</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-7 flex items-center justify-between gap-3">
                                <div><p class="ocean-section-label">Produk dalam pesanan</p><p class="mt-1 text-xs text-zinc-500">Status setiap produk dapat berbeda dari status utama.</p></div>
                                <a href="{{ URL::temporarySignedRoute('tracking.progress', now()->addMinutes(15), ['orderCode' => $orderResult->order_code]) }}" class="text-sm font-bold text-[#176fa9] hover:underline">Buka detail lengkap</a>
                            </div>
                            <div class="mt-3 divide-y divide-sky-100 rounded-2xl border border-sky-100 bg-white px-4">
                                @forelse ($orderResult->items as $item)
                                    <div class="flex items-center justify-between gap-4 py-4">
                                        <div><strong class="block text-sm text-[#123c5a]">{{ $item->item_name }}</strong><span class="mt-1 block text-xs text-zinc-500">{{ $item->variant ?: 'Tanpa varian' }} · Qty {{ $item->quantity }}</span></div>
                                        <x-status-badge :status="$orderResult->is_refunded ? $orderResult->tracking_status : ($item->overrideStatus ?: $orderResult->effective_status)" />
                                    </div>
                                @empty
                                    <p class="py-4 text-sm text-zinc-500">Belum ada produk yang tercatat.</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                @endisset

                @isset($memberResult)
                    <details open class="ocean-result-panel mt-5" data-smart-search-result="history">
                        <summary class="ocean-result-summary">
                            <span class="ocean-result-icon grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-amber-100 text-amber-700">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8" aria-hidden="true"><path d="M3 6.5h18v11H3z"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <span class="ocean-result-copy min-w-0 flex-1">
                                <span class="ocean-result-kicker block text-xs font-bold uppercase tracking-[0.16em] text-amber-700">Riwayat pembelian</span>
                                <span class="ocean-result-title mt-1 block truncate text-lg font-bold text-[#123c5a]">{{ $memberResult->display_name }}</span>
                            </span>
                            <span class="ocean-result-badge"><span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-[#176fa9]">{{ $memberResult->orders->count() }} pesanan</span></span>
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="ocean-chevron h-5 w-5 shrink-0 text-zinc-400" stroke-width="1.8" aria-hidden="true"><path d="m5 7 5 5 5-5"/></svg>
                        </summary>
                        <div class="space-y-3 border-t border-sky-100 p-5 sm:p-7">
                            @forelse ($memberResult->orders as $order)
                                <details class="ocean-order-toggle">
                                    <summary>
                                        <span class="min-w-0 flex-1"><strong class="block font-mono text-sm text-[#176fa9]">{{ $order->order_code }}</strong><span class="mt-1 block truncate text-xs text-zinc-500">{{ $order->batch->batch_name ?: $order->batch->batch_number }} · {{ $order->items->sum('quantity') }} item</span></span>
                                        <x-status-badge :status="$order->tracking_status" />
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" class="ocean-chevron h-4 w-4 shrink-0 text-zinc-400" stroke-width="1.8" aria-hidden="true"><path d="m5 7 5 5 5-5"/></svg>
                                    </summary>
                                    <div class="border-t border-sky-100 px-4 pb-4 pt-3">
                                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-xs text-zinc-500"><span>Diperbarui {{ $order->updated_at->format('d M Y, H:i') }}</span><span>Pembayaran: <strong class="text-zinc-700">{{ $order->paymentStatus?->name ?: 'Belum ditentukan' }}</strong></span></div>
                                        <div class="divide-y divide-sky-50">
                                            @forelse ($order->items as $item)
                                                <div class="flex items-center justify-between gap-4 py-2.5"><span class="text-sm text-[#123c5a]">{{ $item->item_name }} <small class="text-zinc-400">× {{ $item->quantity }}</small></span><x-status-badge :status="$order->is_refunded ? $order->tracking_status : ($item->overrideStatus ?: $order->effective_status)" /></div>
                                            @empty
                                                <p class="py-2 text-sm text-zinc-500">Belum ada produk yang tercatat.</p>
                                            @endforelse
                                        </div>
                                        <a href="{{ URL::temporarySignedRoute('tracking.order', now()->addMinutes(15), ['memberCode' => $memberResult->member_code, 'memberOrder' => $order]) }}" class="mt-3 inline-flex text-sm font-bold text-[#176fa9] hover:underline">Lihat tracking lengkap</a>
                                    </div>
                                </details>
                            @empty
                                <p class="rounded-2xl bg-sky-50 p-4 text-sm text-zinc-500">Belum ada riwayat pesanan untuk username LINE ini.</p>
                            @endforelse
                        </div>
                    </details>
                @endisset
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
    <section class="overflow-hidden border-y border-sky-950 bg-[#0e3550] py-16 text-white">
        <div class="mx-auto mb-10 max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-200">Satu alur yang mudah dipahami</p>
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
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#176fa9]">Sesederhana itu</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-5xl">Informasi yang kamu butuhkan, tanpa langkah berlebih.</h2>
            </div>
            <div class="mt-12 grid gap-px overflow-hidden rounded-3xl border border-zinc-200 bg-zinc-200 md:grid-cols-3">
                <article class="bg-white p-7 sm:p-9">
                    <span class="font-mono text-sm text-[#176fa9]">01</span>
                    <h3 class="mt-8 text-xl font-bold">Masukkan kode</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-500">Gunakan kode unik dari admin untuk mengecek progres satu pesanan secara langsung.</p>
                </article>
                <article class="bg-white p-7 sm:p-9">
                    <span class="font-mono text-sm text-cyan-700">02</span>
                    <h3 class="mt-8 text-xl font-bold">Cari username LINE</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-500">Masukkan username LINE yang terdaftar untuk membuka daftar dan riwayat seluruh pembelian.</p>
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
