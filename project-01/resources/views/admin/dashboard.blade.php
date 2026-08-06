<x-layouts.app title="Dashboard Admin">
    <x-page-heading title="Dashboard" description="Ringkasan member, batch, pesanan, dan riwayat status terbaru." />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            'Member aktif' => $activeMembers,
            'Batch aktif' => $activeBatches,
            'Total pesanan' => $ordersCount,
            'Pesanan selesai' => $completedOrders,
            'Dalam proses' => $processingOrders,
            'Bermasalah' => $problemOrders,
        ] as $label => $value)
            <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-600">{{ $label }}</div>
                <div class="mt-2 text-3xl font-semibold">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
            <h2 class="font-semibold">Batch Terbaru</h2>
            <div class="mt-4 divide-y divide-zinc-100">
                @forelse ($latestBatches as $batch)
                    <a href="{{ route('admin.batches.show', $batch, false) }}" class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <div class="font-medium">{{ $batch->batch_number }}</div>
                            <div class="text-sm text-zinc-500">{{ $batch->batch_name ?: 'Tanpa nama batch' }}</div>
                        </div>
                        <x-status-badge :status="$batch->currentStatus" />
                    </a>
                @empty
                    <p class="py-8 text-sm text-zinc-500">Belum ada batch.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
            <h2 class="font-semibold">Riwayat Status Terbaru</h2>
            <div class="mt-4 divide-y divide-zinc-100">
                @forelse ($latestHistories as $history)
                    <div class="py-3 text-sm">
                        <div><span class="text-zinc-500">{{ class_basename($history->trackable_type) }}</span> berubah ke <x-status-badge :status="$history->newStatus" /></div>
                        <div class="mt-1 text-zinc-500">{{ $history->changedBy?->name ?: 'Sistem' }} · {{ $history->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <p class="py-8 text-sm text-zinc-500">Belum ada riwayat.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
