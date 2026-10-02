<footer class="op-footer op-footer-reference tracking-landing-footer" id="footer">
    <div class="op-footer-reference-shell">
        <div class="op-footer-brand-block">
            <a href="{{ route('home') }}" aria-label="Ocean Paws — kembali ke beranda">
                <img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws">
            </a>
        </div>

        <div class="op-footer-center">
            <nav class="op-footer-links" aria-label="Navigasi footer">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('home') }}#about">Layanan</a>
                <a href="{{ route('tracking.index') }}">Tracking</a>
                <a href="{{ route('home') }}#about">FAQ</a>
            </nav>

            <nav class="op-footer-mobile-links" aria-label="Navigasi footer seluler">
                <a href="{{ route('home') }}"><i class="bi bi-house-door-fill" aria-hidden="true"></i><span>Home</span><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <a href="{{ route('home') }}#about"><i class="bi bi-grid-fill" aria-hidden="true"></i><span>Layanan</span><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <a href="{{ route('tracking.index') }}"><i class="bi bi-truck-front-fill" aria-hidden="true"></i><span>Tracking</span><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <a href="{{ route('home') }}#about"><i class="bi bi-chat-square-dots-fill" aria-hidden="true"></i><span>FAQ</span><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </nav>

            <div class="op-footer-socials" aria-label="Media sosial Ocean Paws">
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                <a href="#" aria-label="X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                <a href="#" aria-label="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                <a href="#" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="op-footer-message">
            <strong><span>Small Orders</span><span>Bigger Happiness ♡</span></strong>
            <img class="op-footer-shell-icon" src="{{ asset('assets/oceanpaws-footer-shell-v1.png') }}" alt="" aria-hidden="true">
        </div>
    </div>

    <div class="op-footer-bottomline">
        <span>© {{ now()->year }} OCEANPAWS. All rights reserved.</span>
    </div>
</footer>