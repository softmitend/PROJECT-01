<x-layouts.app title="Riwayat Pesanan — Ocean Paws">
    <main class="page order-history-page">
        <div class="order-history-shell">
            <header class="order-history-header">
                <a href="{{ route('services.index') }}" aria-label="Kembali ke layanan"><x-public-icon name="arrow-left" :size="17" /> Kembali</a>
                <span class="micro !text-[9px]">DATA MILIKMU</span>
                <h1>Riwayat pesanan</h1>
                <p>Seluruh jajanan, pembayaran, dan perjalanan paketmu tersimpan di sini.</p>
            </header>

            @if($member)
                <nav class="order-history-filters" aria-label="Filter riwayat pesanan">
                    @foreach([
                        'all' => 'Semua',
                        'unpaid' => 'Belum bayar',
                        'active' => 'Aktif',
                        'history' => 'Selesai',
                        'shipping' => 'Dikirim',
                        'refund' => 'Refund',
                    ] as $value => $label)
                        <a class="{{ $filter === $value ? 'active' : '' }}" href="{{ route('orders.history', $value === 'all' ? [] : ['filter' => $value]) }}">{{ $label }}</a>
                    @endforeach
                </nav>
            @endif

            <section class="profile-order-history">
                <header><div><span class="micro !text-[9px]">{{ $filter === 'all' ? 'SEMUA PESANAN' : 'HASIL FILTER' }}</span><h2>{{ $filter === 'all' ? 'Jajanan kamu' : 'Pesanan ditemukan' }}</h2></div><b>{{ $orders->count() }} dari {{ $allOrderCount }}</b></header>
                @if($user && !$member)
                    <div class="profile-orders-empty"><x-public-icon name="grid" :size="22" /><strong>Ini adalah akun admin.</strong><p>Gunakan akun LINE customer untuk melihat riwayat pesanan pribadi.</p><a href="{{ route('admin.dashboard') }}">Buka dashboard</a></div>
                @elseif(!$member)
                    <div class="profile-orders-empty"><x-public-icon name="bag" :size="22" /><strong>Login untuk melihat riwayatmu.</strong><p>Pesanan dari semua batch akan tersimpan di halaman ini.</p><a href="{{ route('line-auth.redirect') }}">Login dengan LINE</a></div>
                @else
                    <div class="profile-order-list">
                        @forelse($orders as $order)
                            @php($trackingStatus = $order->tracking_status)
                            <article class="profile-order-card">
                                <header>
                                    <span class="profile-order-thumb">@if($order->batch->catalog_image_path)<img src="{{ $order->batch->catalog_image_url }}" alt="">@else<x-public-icon name="box" :size="20" />@endif</span>
                                    <div><small>{{ $order->order_code }}</small><h3>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h3><p>{{ $order->created_at->translatedFormat('d M Y') }} · {{ $order->items->sum('quantity') }} item</p></div>
                                    @if($trackingStatus)<x-status-badge :status="$trackingStatus" />@endif
                                </header>
                                <div class="profile-order-items">
                                    @forelse($order->items as $item)
                                        <div><span><strong>{{ $item->item_name }}</strong><small>{{ $item->variant ?: 'Tanpa variasi' }} · {{ $item->quantity }}×</small></span><b>Rp {{ number_format((float) ($item->subtotal ?? ($item->unit_price * $item->quantity)), 0, ',', '.') }}</b></div>
                                    @empty<p>Belum ada item yang tercatat.</p>@endforelse
                                </div>
                                @if($order->ems_tax_is_published)<x-order-ems-bill :order="$order" compact />@endif
                                <footer><span>{{ $order->paymentStatus?->name ?: $order->payment_type_label }}</span><a href="{{ $order->history_tracking_url }}">Lihat detail <x-public-icon name="arrow-right" :size="13" /></a></footer>
                            </article>
                        @empty
                            <div class="profile-orders-empty"><x-public-icon name="bag" :size="22" /><strong>Tidak ada pesanan di kategori ini.</strong><p>Coba pilih kategori lain atau mulai jajan dari katalog.</p><a href="{{ route('catalog.index') }}">Lihat katalog</a></div>
                        @endforelse
                    </div>
                @endif
            </section>
        </div>
    </main>
</x-layouts.app>
