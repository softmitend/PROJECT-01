import $ from 'jquery';

window.$ = window.jQuery = $;

const pad = (value) => String(value).padStart(2, '0');

const updateAdminClock = () => {
    const now = new Date();
    document.querySelectorAll('[data-realtime-clock]').forEach((element) => {
        element.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    });
    document.querySelectorAll('[data-realtime-date]').forEach((element) => {
        element.textContent = new Intl.DateTimeFormat('id-ID', {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
        }).format(now);
    });
};

const formatLoginDuration = (totalSeconds) => {
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);

    if (hours > 0) {
        return `${hours} jam ${minutes} menit`;
    }

    return `${minutes} menit`;
};

const updateLoginDuration = () => {
    document.querySelectorAll('[data-login-duration]').forEach((element) => {
        const loginAt = Number(element.dataset.loginAt || Date.now());
        const elapsed = Math.max(0, Math.floor((Date.now() - loginAt) / 1000));
        element.textContent = formatLoginDuration(elapsed);
    });
};

document.querySelectorAll('[data-user-menu]').forEach((menu) => {
    const button = menu.querySelector('[data-user-menu-button]');
    const panel = menu.querySelector('[data-user-menu-panel]');

    button?.addEventListener('click', () => {
        const willOpen = panel.hasAttribute('hidden');
        document.querySelectorAll('[data-user-menu-panel]').forEach((otherPanel) => otherPanel.setAttribute('hidden', ''));
        panel.toggleAttribute('hidden', !willOpen);
        button.setAttribute('aria-expanded', String(willOpen));
    });
});

document.addEventListener('click', (event) => {
    document.querySelectorAll('[data-user-menu]').forEach((menu) => {
        if (!menu.contains(event.target)) {
            menu.querySelector('[data-user-menu-panel]')?.setAttribute('hidden', '');
            menu.querySelector('[data-user-menu-button]')?.setAttribute('aria-expanded', 'false');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('[data-user-menu-panel]').forEach((panel) => panel.setAttribute('hidden', ''));
        document.querySelectorAll('[data-user-menu-button]').forEach((button) => button.setAttribute('aria-expanded', 'false'));
    }
});

updateAdminClock();
updateLoginDuration();
setInterval(updateAdminClock, 1000);
setInterval(updateLoginDuration, 30000);

const initializeAdminSelects = async () => {
    const { default: attachSelect2 } = await import('select2');
    if (typeof $.fn.select2 !== 'function') attachSelect2(window, $);

    const enhanceSelects = (root = document) => {
        $(root)
            .find('.admin-shell select:not(.select2-hidden-accessible)')
            .each(function () {
                const select = $(this);
                select.select2({
                    width: '100%',
                    dropdownCssClass: select.closest('.admin-form-shell').length
                        ? 'admin-form-select2-dropdown'
                        : '',
                    minimumResultsForSearch: select.find('option').length > 6 ? 0 : Infinity,
                });
            });
    };

    enhanceSelects();

    const adminShell = document.querySelector('.admin-shell');
    if (!adminShell) return;

    new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) enhanceSelects(node);
            });
        });
    }).observe(adminShell, { childList: true, subtree: true });
};

initializeAdminSelects();
