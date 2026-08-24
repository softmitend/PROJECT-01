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
        const selector = '.admin-shell select:not([data-native-select]):not(.select2-hidden-accessible)';
        const candidates = $(root).is(selector) ? $(root) : $(root).find(selector);

        candidates
            .each(function () {
                const select = $(this);
                const modal = select.closest('.status-modal');
                select.select2({
                    width: '100%',
                    dropdownParent: modal.length ? modal : $(document.body),
                    dropdownCssClass: select.closest('.admin-form-shell').length
                        ? 'admin-form-select2-dropdown'
                        : '',
                    minimumResultsForSearch: select.find('option').length > 6 ? 0 : Infinity,
                });
            });
    };

    document.addEventListener('admin:enhance-selects', (event) => {
        enhanceSelects(event.detail?.root || document);
    });

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

document.querySelectorAll('[data-batch-product-picker]').forEach((picker) => {
    const selectedProducts = picker.closest('.admin-form-section')?.querySelector('[data-batch-selected-products]');
    const productList = selectedProducts?.querySelector('[data-selected-product-list]');
    const emptyState = selectedProducts?.querySelector('[data-selected-product-empty]');
    const count = selectedProducts?.querySelector('[data-selected-product-count]');
    if (!selectedProducts || !productList) return;

    const updateSelectedProductsState = () => {
        const total = productList.querySelectorAll('[data-selected-product]').length;
        if (count) count.textContent = `${total} produk`;
        emptyState?.toggleAttribute('hidden', total > 0);
    };

    const createSelectedProduct = (option) => {
        if (!option?.value || productList.querySelector(`[data-product-id="${option.value}"]`)) return;

        const item = document.createElement('div');
        item.className = 'batch-selected-product';
        item.dataset.selectedProduct = '';
        item.dataset.productId = option.value;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_ids[]';
        input.value = option.value;

        const identity = document.createElement('div');
        const name = document.createElement('strong');
        name.textContent = option.dataset.name || option.textContent.trim();
        const variant = document.createElement('small');
        variant.textContent = option.dataset.variant || 'Tanpa varian';
        identity.append(name, variant);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.dataset.removeSelectedProduct = '';
        remove.textContent = 'Hapus';
        remove.setAttribute('aria-label', `Hapus ${name.textContent}`);

        item.append(input, identity, remove);
        productList.append(item);
        option.disabled = true;
        picker.value = '';
        if ($(picker).hasClass('select2-hidden-accessible')) $(picker).val(null).trigger('change.select2');
        updateSelectedProductsState();
    };

    $(picker).on('select2:select', (event) => {
        createSelectedProduct(event.params?.data?.element || picker.options[picker.selectedIndex]);
    });

    picker.addEventListener('change', () => createSelectedProduct(picker.options[picker.selectedIndex]));

    selectedProducts.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove-selected-product]');
        if (!remove) return;

        const item = remove.closest('[data-selected-product]');
        const option = [...picker.options].find((candidate) => candidate.value === item.dataset.productId);
        if (option) option.disabled = false;
        item.remove();
        if ($(picker).hasClass('select2-hidden-accessible')) $(picker).trigger('change.select2');
        updateSelectedProductsState();
    });

    updateSelectedProductsState();
});

// List filters apply as soon as a choice changes. Text searches remain explicit
// so the page does not reload while an admin is still typing.
const automaticFilterTimers = new WeakMap();

const submitAutomaticFilter = (field) => {
    const form = field.closest('[data-auto-filter]');
    if (!form) return;

    clearTimeout(automaticFilterTimers.get(form));
    automaticFilterTimers.set(form, setTimeout(() => form.requestSubmit(), 0));
};

document.addEventListener('change', (event) => {
    if (event.target.matches('[data-auto-filter] select, [data-auto-filter] input[type="date"]')) {
        submitAutomaticFilter(event.target);
    }
});

$(document).on('select2:select select2:clear', '[data-auto-filter] select', function () {
    submitAutomaticFilter(this);
});

const activateStatusFolder = (folderMap, scope, updateUrl = true) => {
    const tabs = folderMap.querySelectorAll('[data-status-folder-tab]');
    const panels = folderMap.querySelectorAll('[data-status-folder-panel]');
    const scopeInput = document.querySelector('[data-status-folder-input]');

    tabs.forEach((tab) => {
        const active = tab.dataset.statusFolderTab === scope;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', String(active));
        tab.setAttribute('tabindex', active ? '0' : '-1');
    });

    panels.forEach((panel) => {
        panel.hidden = panel.dataset.statusFolderPanel !== scope;
    });

    if (scopeInput) scopeInput.value = scope;

    if (updateUrl) {
        const url = new URL(window.location.href);
        url.searchParams.set('scope', scope);
        window.history.replaceState({}, '', url);
    }
};

document.querySelectorAll('[data-status-folder-map]').forEach((folderMap) => {
    const tabs = [...folderMap.querySelectorAll('[data-status-folder-tab]')];

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateStatusFolder(folderMap, tab.dataset.statusFolderTab));

        tab.addEventListener('keydown', (event) => {
            let nextIndex;

            if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
            if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = tabs.length - 1;
            if (nextIndex === undefined) return;

            event.preventDefault();
            const nextTab = tabs[nextIndex];
            activateStatusFolder(folderMap, nextTab.dataset.statusFolderTab);
            nextTab.focus();
        });
    });
});

document.querySelectorAll('[data-status-modal]').forEach((modal) => {
    const modalName = modal.dataset.statusModal;
    let modalTrigger = null;

    const closeModal = () => {
        if (modal.hidden) return;

        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('status-modal-open');
        modalTrigger?.focus();
        modalTrigger = null;
    };

    document.querySelectorAll(`[data-status-modal-open="${modalName}"]`).forEach((button) => {
        button.addEventListener('click', (event) => {
            modalTrigger = event.currentTarget;
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('status-modal-open');
            modal.querySelector('.status-modal-surface')?.focus();
        });
    });

    modal.querySelectorAll('[data-status-modal-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (modal.hidden) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusable = [...modal.querySelectorAll('button:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')]
            .filter((element) => !element.closest('[hidden]'));
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
});
