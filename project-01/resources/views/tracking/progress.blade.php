<x-layouts.app title="Progress {{ $order->order_code }}">
    <div class="mx-auto max-w-4xl">
        <div class="mb-8 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Tracking pesanan</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-5xl">{{ $order->order_code }}</h1>
                <p class="mt-3 text-zinc-500">Batch {{ $order->batch->batch_number }} &middot; {{ $order->batch->batch_name ?: 'K-pop merchandise' }}</p>
            </div>
            <x-status-badge :status="$order->effective_status" />
        </div>

        <section class="relative overflow-hidden rounded-3xl border border-violet-100 bg-white p-6 shadow-xl shadow-violet-100/40">
            <div class="absolute -right-16 -top-24 h-52 w-52 rounded-full bg-violet-200/50 blur-3xl"></div>
            <div class="grid gap-5 sm:grid-cols-3">
                <div class="relative"><div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Status terkini</div><div class="mt-2 font-semibold">{{ $order->effective_status?->name ?: 'Belum ada status' }}</div></div>
                <div class="relative"><div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Jumlah item</div><div class="mt-2 font-semibold">{{ $order->items->sum('quantity') }} item</div></div>
                <div class="relative"><div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Update terakhir</div><div class="mt-2 font-semibold">{{ $order->updated_at->format('d M Y, H:i') }}</div></div>
            </div>
            @if($order->batch->notes)
                <p class="relative mt-5 border-t border-violet-100 pt-5 text-sm leading-6 text-zinc-600">{{ $order->batch->notes }}</p>
            @endif
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_.9fr]">
            <section class="soft-card rounded-3xl p-6">
                <h2 class="font-bold">Isi pesanan</h2>
                <div class="mt-4 divide-y divide-zinc-100">
                    @foreach($order->items as $item)
                        <div class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div>
                                <div class="font-semibold">{{ $item->item_name }}</div>
                                <div class="mt-1 text-sm text-zinc-500">{{ $item->variant ?: 'Tanpa varian' }} &middot; Qty {{ $item->quantity }}</div>
                            </div>
                            <x-status-badge :status="$item->effective_status" />
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="soft-card rounded-3xl p-6">
                <h2 class="font-bold">Perjalanan pesanan</h2>
                <div class="mt-5 space-y-4">
                    @forelse($timeline as $history)
                        <div class="relative border-l border-violet-200 pb-1 pl-5">
                            <span class="absolute -left-[5px] top-1 h-2.5 w-2.5 rounded-full bg-violet-600 ring-4 ring-violet-100"></span>
                            <div class="font-semibold">{{ $history->newStatus?->name ?: 'Status diperbarui' }}</div>
                            <div class="mt-1 text-xs text-zinc-500">{{ $history->created_at->format('d M Y, H:i') }}</div>
                            @if($history->note)<p class="mt-1 text-sm leading-6 text-zinc-600">{{ $history->note }}</p>@endif
                        </div>
                    @empty
                        <p class="text-sm leading-6 text-zinc-500">Belum ada pembaruan perjalanan. Status terbaru tetap dapat dilihat di atas.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="mt-6 rounded-2xl bg-zinc-950 p-5 text-center text-sm text-white">
            Punya pesanan lain? <a class="font-semibold underline decoration-white/40 underline-offset-4 hover:decoration-white" href="{{ route('tracking.index') }}#history-search">Cari seluruh riwayat dengan email</a>
        </div>
    </div>
</x-layouts.app>
