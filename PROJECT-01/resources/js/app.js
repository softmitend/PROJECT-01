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

document.querySelectorAll('[data-admin-variant-builder]').forEach((builder) => {
    const list = builder.querySelector('[data-admin-variant-list]');
    const template = builder.querySelector('[data-admin-variant-template]');
    const empty = builder.querySelector('[data-admin-variant-empty]');
    const addButton = builder.querySelector('[data-add-admin-variant]');
    let nextIndex = list?.querySelectorAll('[data-admin-variant-row]').length || 0;

    const refresh = () => empty?.toggleAttribute('hidden', Boolean(list?.querySelector('[data-admin-variant-row]')));

    addButton?.addEventListener('click', () => {
        if (!list || !template) return;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        const row = wrapper.firstElementChild;
        if (!row) return;
        list.append(row);
        row.querySelector('input:not([type="hidden"])')?.focus();
        refresh();
    });

    builder.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove-admin-variant]');
        if (!remove) return;
        remove.closest('[data-admin-variant-row]')?.remove();
        refresh();
    });

    refresh();
});

document.querySelectorAll('[data-admin-photo-uploader]').forEach((uploader) => {
    const input = uploader.querySelector('[data-photo-input]');
    const image = uploader.querySelector('[data-photo-image]');
    const placeholder = uploader.querySelector('[data-photo-placeholder]');
    const removeButton = uploader.querySelector('[data-photo-remove]');
    const removeInput = uploader.querySelector('[data-photo-remove-input]');
    const state = uploader.querySelector('[data-photo-state]');
    const fileName = uploader.querySelector('[data-photo-file-name]');
    const buttonLabel = uploader.querySelector('[data-photo-button-label]');
    const error = uploader.querySelector('[data-photo-error]');
    const hasExisting = uploader.dataset.hasExisting === '1';
    const existingSource = hasExisting ? image?.getAttribute('src') || '' : '';
    const existingName = fileName?.textContent || 'Foto tersimpan';
    const maxBytes = 4 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    let draftUrl = '';
    let existingRemoved = false;

    const extensionOf = (name) => String(name || '').toLowerCase().split('.').pop();
    const clearError = () => {
        if (!error) return;
        error.textContent = '';
        error.hidden = true;
    };
    const showError = (message) => {
        if (!error) return;
        error.textContent = message;
        error.hidden = false;
    };
    const revokeDraft = () => {
        if (draftUrl) URL.revokeObjectURL(draftUrl);
        draftUrl = '';
    };
    const renderEmpty = () => {
        if (image) {
            image.hidden = true;
            image.removeAttribute('src');
        }
        if (placeholder) placeholder.hidden = false;
        if (removeButton) removeButton.hidden = true;
        if (state) state.textContent = existingRemoved ? 'Akan dihapus' : 'Belum ada foto';
        if (fileName) fileName.textContent = 'Belum ada file dipilih';
        if (buttonLabel) buttonLabel.textContent = 'Pilih foto';
    };
    const renderExisting = () => {
        if (!hasExisting || existingRemoved) {
            renderEmpty();
            return;
        }
        if (image) {
            image.src = existingSource;
            image.hidden = false;
        }
        if (placeholder) placeholder.hidden = true;
        if (removeButton) removeButton.hidden = false;
        if (state) state.textContent = 'Tersimpan';
        if (fileName) fileName.textContent = existingName;
        if (buttonLabel) buttonLabel.textContent = 'Ganti foto';
    };
    const renderDraft = (file) => {
        revokeDraft();
        draftUrl = URL.createObjectURL(file);
        if (image) {
            image.src = draftUrl;
            image.alt = `Preview ${file.name}`;
            image.hidden = false;
        }
        if (placeholder) placeholder.hidden = true;
        if (removeButton) removeButton.hidden = false;
        if (state) state.textContent = 'Preview baru';
        if (fileName) fileName.textContent = file.name;
        if (buttonLabel) buttonLabel.textContent = 'Ganti pilihan';
    };

    input?.addEventListener('change', () => {
        clearError();
        const file = input.files?.[0];
        if (!file) {
            if (hasExisting && !existingRemoved) renderExisting();
            else renderEmpty();
            return;
        }

        const extensionAllowed = allowedExtensions.includes(extensionOf(file.name));
        const typeAllowed = allowedTypes.includes(file.type);
        if (!extensionAllowed || !typeAllowed) {
            input.value = '';
            showError('Format foto harus JPG, JPEG, PNG, atau WEBP.');
            if (hasExisting && !existingRemoved) renderExisting();
            else renderEmpty();
            return;
        }
        if (file.size <= 0 || file.size > maxBytes) {
            input.value = '';
            showError('Ukuran foto maksimal 4 MB.');
            if (hasExisting && !existingRemoved) renderExisting();
            else renderEmpty();
            return;
        }

        renderDraft(file);
    });

    removeButton?.addEventListener('click', () => {
        clearError();
        if (input?.files?.length) {
            revokeDraft();
            input.value = '';
            if (hasExisting && !existingRemoved) renderExisting();
            else renderEmpty();
            return;
        }

        if (hasExisting && !existingRemoved) {
            existingRemoved = true;
            if (removeInput) removeInput.value = '1';
            renderEmpty();
        }
    });

    uploader.closest('form')?.addEventListener('reset', () => {
        revokeDraft();
        existingRemoved = false;
        if (removeInput) removeInput.value = '0';
        clearError();
        window.setTimeout(() => hasExisting ? renderExisting() : renderEmpty(), 0);
    });
});

const formatRupiah = (value) => `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(value || 0)}`;

document.querySelectorAll('[data-catalog-checkout]').forEach((checkout) => {
    const modal = checkout.querySelector('[data-catalog-variant-modal]');
    const sheet = modal?.querySelector('.catalog-variant-sheet');
    const variants = [...checkout.querySelectorAll('[data-catalog-variant]')];
    const orderList = checkout.querySelector('[data-catalog-order-list]');
    const orderEmpty = checkout.querySelector('[data-catalog-order-empty]');
    const orderHeading = checkout.querySelector('[data-catalog-order-heading]');
    const orderSubtitle = checkout.querySelector('[data-catalog-order-subtitle]');
    const paymentSection = checkout.querySelector('[data-catalog-payment-section]');
    const confirmButton = checkout.querySelector('[data-confirm-variants]');
    const proofInput = checkout.querySelector('[data-payment-proof-input]');
    const proofFileName = checkout.querySelector('[data-proof-file-name]');
    const payButton = checkout.querySelector('[data-catalog-pay-button]');
    const qrisReady = checkout.dataset.qrisReady === 'true';

    const selectedVariants = () => variants.filter((variant) => Number(variant.dataset.quantity || 0) > 0);
    const selectedPaymentType = () => checkout.querySelector('input[name="payment_type"]:checked')?.value || 'dp';
    const priceFor = (variant, type = selectedPaymentType()) => Number(variant.dataset[type === 'full' ? 'fullPrice' : 'dpPrice'] || 0);

    const totals = () => ({
        dp: selectedVariants().reduce((total, variant) => total + priceFor(variant, 'dp') * Number(variant.dataset.quantity), 0),
        full: selectedVariants().reduce((total, variant) => total + priceFor(variant, 'full') * Number(variant.dataset.quantity), 0),
    });

    const refreshConfirmButton = () => {
        if (!confirmButton) return;
        const quantity = selectedVariants().reduce((total, variant) => total + Number(variant.dataset.quantity), 0);
        confirmButton.disabled = quantity === 0;
        confirmButton.textContent = quantity ? `Pilih ${quantity} barang` : 'Pilih minimal 1 barang';
    };

    const refreshPayButton = () => {
        if (!payButton) return;
        const hasSelection = selectedVariants().length > 0;
        const hasProof = Boolean(proofInput?.files?.length);
        const amount = totals()[selectedPaymentType()];
        payButton.disabled = !hasSelection || !hasProof || !qrisReady;
        const label = payButton.querySelector('span');
        if (!label) return;
        if (!qrisReady) label.textContent = 'QRIS belum tersedia';
        else if (!hasProof) label.textContent = 'Upload bukti pembayaran dahulu';
        else label.textContent = `Bayar ${formatRupiah(amount)}`;
    };

    const refreshTotals = () => {
        const calculated = totals();
        checkout.querySelectorAll('[data-catalog-dp-total]').forEach((element) => { element.textContent = formatRupiah(calculated.dp); });
        checkout.querySelectorAll('[data-catalog-full-total]').forEach((element) => { element.textContent = formatRupiah(calculated.full); });
        checkout.querySelectorAll('[data-catalog-selected-subtotal]').forEach((element) => { element.textContent = formatRupiah(calculated.full); });
        checkout.querySelectorAll('[data-catalog-payment-total]').forEach((element) => { element.textContent = formatRupiah(calculated[selectedPaymentType()]); });
        refreshPayButton();
    };

    const renderSummary = () => {
        if (!orderList) return;
        orderList.replaceChildren();
        const selected = selectedVariants();

        selected.forEach((variant, index) => {
            const quantity = Number(variant.dataset.quantity);
            const item = document.createElement('article');
            item.className = 'catalog-order-item';

            const copy = document.createElement('div');
            const name = document.createElement('strong');
            name.textContent = variant.dataset.name;
            const detail = document.createElement('small');
            detail.textContent = `${quantity} × ${formatRupiah(priceFor(variant))}`;
            copy.append(name, detail);

            const amount = document.createElement('b');
            amount.textContent = formatRupiah(quantity * priceFor(variant));

            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = `items[${index}][product_id]`;
            productInput.value = variant.dataset.productId;
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = String(quantity);

            item.append(copy, amount, productInput, quantityInput);
            orderList.append(item);
        });

        const hasSelection = selected.length > 0;
        orderEmpty?.toggleAttribute('hidden', hasSelection);
        paymentSection?.toggleAttribute('hidden', !hasSelection);
        if (orderHeading) orderHeading.textContent = hasSelection ? 'Pesanan Kamu' : 'Pilihan Barang';
        if (orderSubtitle) orderSubtitle.textContent = hasSelection
            ? 'Periksa kembali variasi dan jumlahnya.'
            : 'Pilih variasi yang ingin kamu pesan.';
        const editButton = checkout.querySelector('[data-open-variant-modal]');
        if (editButton) editButton.textContent = hasSelection ? 'Ubah' : 'Pilih variasi';

        refreshTotals();
    };

    const closeModal = () => {
        if (!modal) return;
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('catalog-modal-open');
    };

    checkout.querySelector('[data-open-variant-modal]')?.addEventListener('click', () => {
        if (!modal) return;
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('catalog-modal-open');
        sheet?.focus();
    });

    checkout.querySelectorAll('[data-close-variant-modal]').forEach((button) => button.addEventListener('click', closeModal));

    variants.forEach((variant) => {
        const quantityValue = variant.querySelector('[data-quantity-value]');
        const setQuantity = (quantity) => {
            const safeQuantity = Math.max(0, Math.min(99, quantity));
            variant.dataset.quantity = String(safeQuantity);
            if (quantityValue) quantityValue.textContent = String(safeQuantity);
            refreshConfirmButton();
        };
        variant.querySelector('[data-quantity-minus]')?.addEventListener('click', () => setQuantity(Number(variant.dataset.quantity || 0) - 1));
        variant.querySelector('[data-quantity-plus]')?.addEventListener('click', () => setQuantity(Number(variant.dataset.quantity || 0) + 1));
    });

    confirmButton?.addEventListener('click', () => {
        renderSummary();
        closeModal();
        checkout.querySelector('[data-catalog-order-panel]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    checkout.querySelectorAll('input[name="payment_type"]').forEach((input) => input.addEventListener('change', () => {
        renderSummary();
    }));

    proofInput?.addEventListener('change', () => {
        if (proofFileName) proofFileName.textContent = proofInput.files?.[0]?.name || 'Pilih Bukti Pembayaran';
        refreshPayButton();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal && !modal.hidden) closeModal();
    });

    refreshConfirmButton();
    renderSummary();
});

document.querySelectorAll('[data-mobile-dock]').forEach((dock) => {
    const links = [...dock.querySelectorAll('[data-dock-target]')];

    const setActiveLink = (target) => {
        const activeTarget = target || dock.dataset.activeTarget || 'home';
        links.forEach((link) => link.classList.toggle('is-active', link.dataset.dockTarget === activeTarget));
    };

    const targetFromHash = () => window.location.hash.replace(/^#/, '');
    setActiveLink(targetFromHash());
    links.forEach((link) => link.addEventListener('click', () => setActiveLink(link.dataset.dockTarget)));
    window.addEventListener('hashchange', () => setActiveLink(targetFromHash()));
});
