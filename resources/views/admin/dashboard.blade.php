<x-layouts.app title="Dashboard Admin">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span> Ringkasan hari ini</div>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Selamat datang, {{ auth()->user()->name }}.</h1>
            <p class="dashboard-welcome-subtitle mt-2 text-sm leading-6 text-zinc-500">Pantau pelanggan, batch, dan perjalanan pesanan OceanPaws.</p>
        </div>
    </div>

    @php
        $summaryCards = [
            ['label' => 'Total pesanan', 'value' => $ordersCount, 'note' => 'Semua pesanan tercatat', 'tone' => 'bg-violet-100 text-violet-700', 'dot' => 'bg-violet-500', 'icon' => 'bag'],
            ['label' => 'Pelanggan aktif', 'value' => $activeMembers, 'note' => 'Siap dipilih saat input', 'tone' => 'bg-cyan-100 text-cyan-700', 'dot' => 'bg-cyan-500', 'icon' => 'users'],
            ['label' => 'Batch aktif', 'value' => $activeBatches, 'note' => 'Sedang berjalan', 'tone' => 'bg-blue-100 text-blue-700', 'dot' => 'bg-blue-500', 'icon' => 'layers'],
            ['label' => 'Pesanan selesai', 'value' => $completedOrders, 'note' => 'Proses telah tuntas', 'tone' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500', 'icon' => 'check'],
        ];
        $totalForRatio = max($ordersCount, 1);
        $processingRatio = min(100, round(($processingOrders / $totalForRatio) * 100));
        $completedRatio = min(100, round(($completedOrders / $totalForRatio) * 100));
        $problemRatio = min(100, round(($problemOrders / $totalForRatio) * 100));
    @endphp

    <section>
        <div class="mb-4 flex items-center justify-between">
            <div><h2 class="text-lg font-bold">Aktivitas operasional</h2><p class="dashboard-section-subtitle mt-1 text-xs text-zinc-500">Angka utama dari seluruh data aktif.</p></div>
            <span class="hidden text-xs font-medium text-zinc-400 sm:block">Diperbarui otomatis</span>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <article class="admin-panel dashboard-card dashboard-stat-card p-5">
                    <div class="flex items-start justify-between gap-4">
                        <span class="{{ $card['tone'] }} grid h-11 w-11 place-items-center rounded-2xl">
                            @if($card['icon'] === 'users')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
                            @elseif($card['icon'] === 'layers')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 17l9 5 9-5"/></svg>
                            @elseif($card['icon'] === 'check')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>
                            @endif
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-zinc-400"><span class="{{ $card['dot'] }} h-1.5 w-1.5 rounded-full"></span> Live</span>
                    </div>
                    <div class="mt-5 text-3xl font-bold tracking-tight">{{ number_format($card['value'], 0, ',', '.') }}</div>
                    <div class="mt-1 text-sm font-semibold">{{ $card['label'] }}</div>
                    <p class="mt-2 text-xs text-zinc-400">{{ $card['note'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <section class="admin-panel dashboard-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div><h2 class="text-lg font-bold">Kesehatan pesanan</h2><p class="mt-1 text-xs text-zinc-500">Komposisi progres berdasarkan seluruh pesanan.</p></div>
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-600">{{ $ordersCount }} total</span>
            </div>
            <div class="mt-8 space-y-6">
                <div>
                    <div class="mb-2 flex items-center justify-between text-sm"><span class="font-semibold">Dalam proses</span><span class="text-xs text-zinc-500">{{ $processingOrders }} · {{ $processingRatio }}%</span></div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-blue-500" style="width: {{ $processingRatio }}%"></div></div>
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between text-sm"><span class="font-semibold">Selesai</span><span class="text-xs text-zinc-500">{{ $completedOrders }} · {{ $completedRatio }}%</span></div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-500" style="width: {{ $completedRatio }}%"></div></div>
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between text-sm"><span class="font-semibold">Perlu perhatian</span><span class="text-xs text-zinc-500">{{ $problemOrders }} · {{ $problemRatio }}%</span></div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-rose-500" style="width: {{ $problemRatio }}%"></div></div>
                </div>
            </div>
            <div class="dashboard-card-group mt-8 grid grid-cols-3 gap-2 rounded-2xl bg-zinc-50 p-3 text-center">
                <div class="dashboard-mini-card rounded-xl bg-white px-2 py-3"><div class="text-lg font-bold text-violet-700">{{ $processingOrders }}</div><div class="mt-1 text-[10px] font-bold uppercase tracking-wide text-zinc-400">Proses</div></div>
                <div class="dashboard-mini-card rounded-xl bg-white px-2 py-3"><div class="text-lg font-bold text-emerald-700">{{ $completedOrders }}</div><div class="mt-1 text-[10px] font-bold uppercase tracking-wide text-zinc-400">Selesai</div></div>
                <div class="dashboard-mini-card rounded-xl bg-white px-2 py-3"><div class="text-lg font-bold text-rose-700">{{ $problemOrders }}</div><div class="mt-1 text-[10px] font-bold uppercase tracking-wide text-zinc-400">Kendala</div></div>
            </div>
        </section>

        <section class="admin-panel dashboard-card dashboard-table-card dashboard-latest-batches">
            <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-5 sm:px-6">
                <div><h2 class="text-lg font-bold">Batch terbaru</h2><p class="mt-1 text-xs text-zinc-500">Batch yang baru dibuat.</p></div>
                <a href="{{ route('admin.batches.index') }}" class="text-xs font-bold text-violet-700 hover:text-violet-900">Lihat semua</a>
            </div>
            <div class="divide-y divide-zinc-100 px-5 sm:px-6">
                @forelse ($latestBatches as $batch)
                    <a href="{{ route('admin.batches.show', $batch, false) }}" class="group flex items-center justify-between gap-3 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-50 text-xs font-bold text-blue-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="min-w-0"><div class="truncate font-semibold group-hover:text-violet-700">{{ $batch->batch_number }}</div><div class="mt-1 truncate text-xs text-zinc-400">{{ $batch->batch_name ?: 'Tanpa nama batch' }}</div></div>
                        </div>
                        <x-status-badge :status="$batch->currentStatus" />
                    </a>
                @empty
                    <p class="py-10 text-center text-sm text-zinc-500">Belum ada batch.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="admin-panel dashboard-card dashboard-table-card mt-6">
        <div class="flex flex-col justify-between gap-3 border-b border-zinc-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
            <div><h2 class="text-lg font-bold">Aktivitas status terbaru</h2><p class="mt-1 text-xs text-zinc-500">Perubahan status paling baru dari tim admin.</p></div>
            <a href="{{ route('admin.status-histories.index') }}" class="text-xs font-bold text-violet-700">Buka audit log →</a>
        </div>
        <div class="grid divide-y divide-zinc-100 px-5 md:grid-cols-2 md:divide-x md:divide-y-0 sm:px-6">
            @forelse ($latestHistories->chunk(4) as $historyGroup)
                <div class="divide-y divide-zinc-100 md:px-5 md:first:pl-0 md:last:pr-0">
                    @foreach($historyGroup as $history)
                        <div class="flex items-start gap-3 py-4 text-sm">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-violet-500 ring-4 ring-violet-100"></span>
                            <div class="min-w-0 flex-1">
                                <div><span class="font-semibold">{{ class_basename($history->trackable_type) }}</span> berubah ke <x-status-badge :status="$history->newStatus" /></div>
                                <div class="mt-1.5 text-xs text-zinc-400">{{ $history->changedBy?->name ?: 'Sistem' }} &middot; {{ $history->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="col-span-2 py-10 text-center text-sm text-zinc-500">Belum ada riwayat.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
