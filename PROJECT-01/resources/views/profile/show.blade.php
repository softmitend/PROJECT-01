<x-layouts.app title="Profil Member — Ocean Paws">
    <div class="member-profile-page">
        <div class="member-profile-shell">
            @if(!$user)
                <header class="member-profile-simple-head">
                    <a href="{{ route('tracking.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i></a>
                    <div><small>OCEAN PAWS</small><strong>Profil Member</strong></div>
                </header>

                <section class="member-login-card">
                    <div class="member-login-art" aria-hidden="true">
                        <span class="member-login-bubble is-one"></span>
                        <span class="member-login-bubble is-two"></span>
                        <i class="bi bi-person-heart"></i>
                    </div>
                    <span class="member-profile-eyebrow">MEMBER AREA</span>
                    <h1>Masuk untuk melihat semua titipanmu.</h1>
                    <p>Gunakan akun LINE agar pesanan, pembayaran, dan perjalanan Group Order tersimpan dalam satu profil.</p>
                    <a href="{{ route('line-auth.redirect') }}" class="line-login-button">
                        <i class="bi bi-line" aria-hidden="true"></i>
                        <span>Login dengan LINE</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    @unless($lineConfigured)
                        <small class="line-login-config-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Admin perlu mengisi Channel ID dan Channel Secret LINE sebelum login dapat digunakan.</small>
                    @endunless
                </section>

                <section class="member-login-benefits">
                    <span>SETELAH LOGIN</span>
                    <div>
                        <article><i class="bi bi-bag-check" aria-hidden="true"></i><strong>Pesananmu</strong><small>Semua batch dalam satu akun</small></article>
                        <article><i class="bi bi-receipt" aria-hidden="true"></i><strong>Tagihan</strong><small>DP, pelunasan, dan EMS</small></article>
                        <article><i class="bi bi-truck" aria-hidden="true"></i><strong>Perjalanan GO</strong><small>Ikuti progres sampai tiba</small></article>
                    </div>
                </section>
            @elseif(!$member)
                <section class="member-login-card">
                    <span class="member-profile-eyebrow">AKUN ADMIN</span>
                    <h1>{{ $user->name }}</h1>
                    <p>Akun admin tidak memiliki profil pesanan member.</p>
                    <a href="{{ route('admin.dashboard') }}" class="member-admin-link">Buka dashboard admin <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                </section>
            @else
                <section class="member-account-card">
                    <header>
                        <span class="member-account-avatar">
                            @if($member->avatar_url)<img src="{{ $member->avatar_url }}" alt="Foto profil {{ $member->display_name }}">@else<i class="bi bi-person" aria-hidden="true"></i>@endif
                        </span>
                        <div><h1>{{ $member->display_name }}</h1><span>MEMBER <i class="bi bi-chevron-right" aria-hidden="true"></i></span></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" aria-label="Logout" title="Logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></button>
                        </form>
                    </header>
                    <div class="member-line-status">
                        <i class="bi bi-line" aria-hidden="true"></i>
                        <span><strong>Akun LINE</strong><small>Terhubung sebagai {{ $member->display_name }}</small></span>
                        <b>Aktif</b>
                    </div>

                    <dl class="member-account-details">
                        <div><dt>Kode member</dt><dd>{{ $member->member_code }}</dd></div>
                        <div><dt>Username LINE</dt><dd>{{ $member->username ? '@'.$member->username : 'Belum dilengkapi' }}</dd></div>
                        <div><dt>Nomor telepon</dt><dd>{{ $member->phone ?: 'Belum dilengkapi' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $member->email ?: 'Belum dilengkapi' }}</dd></div>
                        <div class="is-address"><dt>Alamat pengiriman</dt><dd>{{ $member->address ?: 'Belum ada alamat pengiriman.' }}</dd></div>
                    </dl>
                </section>

                <section class="member-profile-summary" aria-label="Ringkasan akun">
                    <article><i class="bi bi-bag-heart" aria-hidden="true"></i><span><strong>{{ $summary['orders'] }}</strong><small>Total pesanan</small></span></article>
                    <article><i class="bi bi-box-seam" aria-hidden="true"></i><span><strong>{{ $summary['items'] }}</strong><small>Total item</small></span></article>
                    <article><i class="bi bi-wallet2" aria-hidden="true"></i><span><strong>Rp {{ number_format($summary['payment_submitted'], 0, ',', '.') }}</strong><small>Pembayaran dikirim</small></span></article>
                    <article><i class="bi bi-receipt" aria-hidden="true"></i><span><strong>Rp {{ number_format($summary['ems_outstanding'], 0, ',', '.') }}</strong><small>EMS belum dibayar</small></span></article>
                </section>

                <section class="member-orders-overview">
                    <div class="member-section-heading"><div><span>RINGKASAN STATUS</span><h2>Pesanan Saya</h2></div><strong>{{ $summary['orders'] }} pesanan</strong></div>
                    <div>
                        <article class="is-unpaid"><span><i class="bi bi-wallet2" aria-hidden="true"></i>@if($stats['unpaid'])<b>{{ $stats['unpaid'] }}</b>@endif</span><small>Belum Bayar</small></article>
                        <article class="is-active"><span><i class="bi bi-bag-check" aria-hidden="true"></i>@if($stats['active'])<b>{{ $stats['active'] }}</b>@endif</span><small>Jajanan Aktif</small></article>
                        <article class="is-history"><span><i class="bi bi-clock-history" aria-hidden="true"></i>@if($stats['history'])<b>{{ $stats['history'] }}</b>@endif</span><small>History Jajanan</small></article>
                        <article class="is-shipping"><span><i class="bi bi-truck" aria-hidden="true"></i>@if($stats['shipping'])<b>{{ $stats['shipping'] }}</b>@endif</span><small>Pengiriman</small></article>
                        <article class="is-refund"><span><i class="bi bi-arrow-left-right" aria-hidden="true"></i>@if($stats['refund'])<b>{{ $stats['refund'] }}</b>@endif</span><small>Refund</small></article>
                    </div>
                </section>

                <a href="{{ route('catalog.index') }}" class="member-profile-banner">
                    <span><i class="bi bi-gift" aria-hidden="true"></i></span>
                    <div><small>OPEN PRE-ORDER</small><strong>Cari titipan K-pop berikutnya</strong><p>Lihat batch yang sedang dibuka</p></div>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>

                <section class="member-all-orders" id="pesanan-saya">
                    <header class="member-all-orders-heading">
                        <div><span>DATA MILIKMU</span><h2>Semua pesanan</h2><p>Seluruh batch, item, pembayaran, dan tagihan yang terhubung dengan akun LINE ini.</p></div>
                        <b>{{ $orders->count() }} pesanan</b>
                    </header>

                    <div class="member-order-list">
                        @forelse($orders as $order)
                            @php
                                $trackingStatus = $order->tracking_status;
                                $orderTotal = (float) ($order->total_amount ?? $order->items->sum('subtotal'));
                            @endphp
                            <article class="member-order-card">
                                <header class="member-order-card-head">
                                    <div class="member-order-batch-image">
                                        @if($order->batch->catalog_image_path)
                                            <img src="{{ $order->batch->catalog_image_url }}" alt="{{ $order->batch->batch_name ?: $order->batch->batch_number }}">
                                        @else
                                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                    <div class="member-order-title">
                                        <small>{{ $order->order_code }} · {{ $order->created_at->format('d M Y') }}</small>
                                        <h3>{{ $order->batch->batch_name ?: $order->batch->batch_number }}</h3>
                                        <p>Batch {{ $order->batch->batch_number }}</p>
                                    </div>
                                    <div class="member-order-statuses">
                                        @if($trackingStatus)
                                            <span class="member-status-pill" style="--member-status: {{ $trackingStatus->color }}">{{ $trackingStatus->name }}</span>
                                        @endif
                                        <span class="member-payment-pill {{ $order->paymentStatus?->code === 'lunas' ? 'is-paid' : 'is-pending' }}">
                                            {{ $order->paymentStatus?->name ?: $order->payment_type_label }}
                                        </span>
                                    </div>
                                </header>

                                <div class="member-order-items">
                                    <h4><i class="bi bi-bag-check" aria-hidden="true"></i> Item pesanan</h4>
                                    @forelse($order->items as $item)
                                        <div class="member-order-item">
                                            <span class="member-order-item-qty">{{ $item->quantity }}×</span>
                                            <div><strong>{{ $item->item_name }}</strong><small>{{ $item->variant ?: 'Tanpa variasi' }}</small></div>
                                            <div class="member-order-item-price"><strong>Rp {{ number_format((float) ($item->subtotal ?? ($item->unit_price * $item->quantity)), 0, ',', '.') }}</strong><small>{{ $item->effective_status?->name ?: 'Mengikuti status batch' }}</small></div>
                                        </div>
                                    @empty
                                        <p class="member-order-no-items">Belum ada item yang tercatat.</p>
                                    @endforelse
                                </div>

                                <dl class="member-order-finance">
                                    <div><dt>Total pesanan</dt><dd>Rp {{ number_format($orderTotal, 0, ',', '.') }}</dd></div>
                                    <div><dt>Pembayaran dipilih</dt><dd>{{ $order->payment_type_label }}</dd></div>
                                    <div><dt>Nominal dikirim</dt><dd>{{ $order->payment_amount !== null ? 'Rp '.number_format((float) $order->payment_amount, 0, ',', '.') : 'Belum ada' }}</dd></div>
                                    <div><dt>Bukti pembayaran</dt><dd>
                                        @if($order->payment_proof_path)
                                            <a href="{{ route('profile.orders.payment-proof', $order) }}" target="_blank" rel="noopener">Lihat bukti <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                                        @else
                                            Belum diunggah
                                        @endif
                                    </dd></div>
                                </dl>

                                @if($order->ems_tax_is_published)
                                    <div class="member-order-ems {{ $order->ems_tax_status === 'paid' ? 'is-paid' : 'is-due' }}">
                                        <i class="bi bi-receipt-cutoff" aria-hidden="true"></i>
                                        <div><strong>Pajak & EMS · {{ $order->ems_tax_status_label }}</strong><small>{{ $order->ems_tax_due_date ? 'Jatuh tempo '.$order->ems_tax_due_date->format('d M Y') : 'Tanpa tanggal jatuh tempo' }}{{ $order->ems_tax_notes ? ' · '.$order->ems_tax_notes : '' }}</small></div>
                                        <b>Rp {{ number_format((float) $order->ems_tax_amount, 0, ',', '.') }}</b>
                                    </div>
                                @endif

                                @if($order->notes)
                                    <p class="member-order-note"><i class="bi bi-chat-left-text" aria-hidden="true"></i><span><strong>Catatan</strong>{{ $order->notes }}</span></p>
                                @endif

                                <footer>
                                    <span>{{ $order->items->sum('quantity') }} item dalam pesanan ini</span>
                                    <a href="{{ $order->profile_tracking_url }}">Lihat detail & perjalanan <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                                </footer>
                            </article>
                        @empty
                            <div class="member-orders-empty"><i class="bi bi-bag-heart" aria-hidden="true"></i><strong>Belum ada pesanan.</strong><p>Batch yang kamu pesan akan muncul di sini.</p><a href="{{ route('catalog.index') }}">Lihat katalog</a></div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
