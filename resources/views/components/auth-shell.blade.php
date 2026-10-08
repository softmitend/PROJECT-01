@props(['register' => false, 'admin' => false])

<section class="auth-shell {{ $register ? 'auth-shell-register' : '' }}" aria-label="Akun Ocean Paws">
    <aside class="auth-story">
        <span class="auth-sticker">OCEAN PAWS CLUB <span aria-hidden="true">✦</span></span>
        <h2>{{ $admin ? 'Kelola dengan mudah.' : ($register ? 'Mulai perjalananmu bersama kami.' : 'Semua pesanan, satu tempat.') }}</h2>
        <p>{{ $admin ? 'Customer, group, dan perjalanan pesanan dalam satu workspace.' : 'Pantau perjalanan pesanan, cek tagihan, dan kelola profilmu di Ocean Paws.' }}</p>
        <div class="auth-ticket" aria-hidden="true">
            <div class="auth-ticket-top"><span>YOUR PAWS PASS</span><i class="bi bi-stars"></i></div>
            <strong>Good things<br>are on the way.</strong>
            <div class="auth-ticket-bottom"><span>OCEAN PAWS</span><i class="bi bi-box-seam"></i><span>MEMBER CLUB</span></div>
        </div>
        <div class="auth-benefits"><span><i class="bi bi-box-seam" aria-hidden="true"></i> Tracking pesanan</span><span><i class="bi bi-receipt" aria-hidden="true"></i> Status pembayaran</span></div>
    </aside>
    <div class="auth-card">{{ $slot }}</div>
</section>
