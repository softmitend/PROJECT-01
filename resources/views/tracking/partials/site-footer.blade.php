<footer class="op-footer op-footer-reference tracking-landing-footer" id="footer">
    <div class="op-footer-reference-shell">
        <div class="op-footer-brand-block">
            <a href="{{ route('home') }}" aria-label="Ocean Paws — kembali ke beranda">
                <img src="{{ asset('assets/oceanpaws-logo-no-sticker.png') }}" alt="Ocean Paws">
            </a>
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