(function () {
    'use strict';

    const STORAGE_KEY = 'uberlandia-cultural-accessibility';
    const DEFAULTS = Object.freeze({ fontScale: 1, highContrast: false, highlightLinks: false });
    const MIN_FONT_SCALE = 0.9;
    const MAX_FONT_SCALE = 1.4;
    const FONT_STEP = 0.1;
    let state = loadState();
    let modalReturnFocus = null;

    function loadState() {
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            const fontScale = Number(saved.fontScale);
            return {
                fontScale: Number.isFinite(fontScale)
                    ? Math.min(MAX_FONT_SCALE, Math.max(MIN_FONT_SCALE, fontScale))
                    : DEFAULTS.fontScale,
                highContrast: saved.highContrast === true,
                highlightLinks: saved.highlightLinks === true,
            };
        } catch (error) {
            return { ...DEFAULTS };
        }
    }

    function saveState() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (error) {}
    }

    function applyState() {
        const root = document.documentElement;
        root.style.fontSize = `${Math.round(state.fontScale * 100)}%`;
        root.classList.toggle('a11y-high-contrast', state.highContrast);
        root.classList.toggle('a11y-highlight-links', state.highlightLinks);
        root.dataset.a11yFontScale = String(state.fontScale);
    }

    function announce(menu, message) {
        const status = menu.querySelector('.accessibility-menu__status');
        if (!status) return;
        status.textContent = '';
        window.setTimeout(() => { status.textContent = message; }, 20);
    }

    function setupMenu() {
        const menus = [...document.querySelectorAll('[data-accessibility-menu]')];
        menus.forEach((menu, index) => {
        if (menu.dataset.a11yInitialized === 'true') return;
        menu.dataset.a11yInitialized = 'true';

        const trigger = menu.querySelector('.accessibility-menu__trigger');
        const panel = menu.querySelector('.accessibility-menu__panel');
        const title = menu.querySelector('.accessibility-menu__header h2');
        const closeButton = menu.querySelector('.accessibility-menu__close');
        const contrastButton = menu.querySelector('[data-a11y-action="contrast"]');
        const linksButton = menu.querySelector('[data-a11y-action="links"]');
        const increaseButton = menu.querySelector('[data-a11y-action="font-increase"]');
        const decreaseButton = menu.querySelector('[data-a11y-action="font-decrease"]');
        let returnFocus = trigger;
        const suffix = index + 1;
        panel.id = `accessibility-menu-panel-${suffix}`;
        title.id = `accessibility-menu-title-${suffix}`;
        panel.setAttribute('aria-labelledby', title.id);
        trigger.setAttribute('aria-controls', panel.id);

        function updateControls() {
            contrastButton.setAttribute('aria-pressed', String(state.highContrast));
            linksButton.setAttribute('aria-pressed', String(state.highlightLinks));
            increaseButton.disabled = state.fontScale >= MAX_FONT_SCALE;
            decreaseButton.disabled = state.fontScale <= MIN_FONT_SCALE;
        }

        function openMenu() {
            returnFocus = document.activeElement instanceof HTMLElement ? document.activeElement : trigger;
            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            updateControls();
            window.requestAnimationFrame(() => increaseButton.focus());
        }

        function closeMenu(restoreFocus = true) {
            if (panel.hidden) return;
            panel.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            if (restoreFocus) returnFocus?.focus();
        }

        trigger.addEventListener('click', () => panel.hidden ? openMenu() : closeMenu());
        closeButton.addEventListener('click', () => closeMenu());
        menu.addEventListener('click', event => {
            const action = event.target.closest('[data-a11y-action]')?.dataset.a11yAction;
            if (!action) return;

            if (action === 'font-increase') {
                state.fontScale = Math.min(MAX_FONT_SCALE, Number((state.fontScale + FONT_STEP).toFixed(1)));
                announce(menu, `Tamanho da fonte: ${Math.round(state.fontScale * 100)}%.`);
            } else if (action === 'font-decrease') {
                state.fontScale = Math.max(MIN_FONT_SCALE, Number((state.fontScale - FONT_STEP).toFixed(1)));
                announce(menu, `Tamanho da fonte: ${Math.round(state.fontScale * 100)}%.`);
            } else if (action === 'contrast') {
                state.highContrast = !state.highContrast;
                announce(menu, `Alto contraste ${state.highContrast ? 'ativado' : 'desativado'}.`);
            } else if (action === 'links') {
                state.highlightLinks = !state.highlightLinks;
                announce(menu, `Destaque de links ${state.highlightLinks ? 'ativado' : 'desativado'}.`);
            } else if (action === 'reset') {
                state = { ...DEFAULTS };
                try { localStorage.removeItem(STORAGE_KEY); } catch (error) {}
                announce(menu, 'Configurações de acessibilidade restauradas.');
            }

            applyState();
            saveState();
            updateControls();
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !panel.hidden) {
                event.preventDefault();
                closeMenu();
            }
        });
        document.addEventListener('pointerdown', event => {
            if (!panel.hidden && !menu.contains(event.target)) closeMenu(false);
        });
        updateControls();
        });
    }

    function setLabel(element, label) {
        if (element && !element.getAttribute('aria-label') && !(element.textContent || '').trim()) {
            element.setAttribute('aria-label', label);
        }
    }

    function enhanceRenderedContent() {
        setupMenu();
        document.querySelectorAll('.theme-logo').forEach(link => {
            if (!link.getAttribute('aria-label')) link.setAttribute('aria-label', 'Página inicial');
            link.querySelectorAll('img').forEach(image => image.setAttribute('alt', ''));
        });

        setLabel(document.querySelector('.mc-header-menu__btn-mobile'), 'Abrir menu principal');
        document.querySelectorAll('.mc-header-menu__btn-mobile').forEach(control => {
            control.setAttribute('role', 'button');
            control.setAttribute('aria-haspopup', 'true');
        });
        document.querySelectorAll('.mc-header-menu .close__btn, .mc-header-menu-user .close__btn').forEach(control => {
            setLabel(control, 'Fechar menu');
            control.setAttribute('role', 'button');
        });
        document.querySelectorAll('.mc-header-menu-user__desktop .user, .notification-modal__menu-mobile, .clear-filter').forEach(control => {
            control.setAttribute('role', 'button');
            control.tabIndex = 0;
        });
        document.querySelectorAll('.mc-header-menu-user__desktop .user, .notification-modal__menu-mobile').forEach(control => {
            control.setAttribute('aria-haspopup', 'true');
        });

        document.querySelectorAll('.search-filter__actions-input').forEach(input => {
            if (!input.getAttribute('aria-label')) input.setAttribute('aria-label', 'Pesquisar');
        });
        document.querySelectorAll('.search-filter__actions-button').forEach(button => {
            setLabel(button, 'Executar pesquisa');
            button.setAttribute('type', 'submit');
        });
        document.querySelectorAll('.search-list__order select').forEach(select => {
            if (!select.getAttribute('aria-label')) select.setAttribute('aria-label', 'Ordenar resultados');
        });
        document.querySelectorAll('.search-filter select').forEach(select => {
            if (!select.getAttribute('aria-label') && !select.labels?.length) {
                const nearbyLabel = select.closest('.field')?.querySelector('label');
                select.setAttribute('aria-label', nearbyLabel?.textContent.trim() || 'Filtrar resultados');
            }
        });
        document.querySelectorAll('select').forEach(select => {
            if (select.getAttribute('aria-label') || select.getAttribute('aria-labelledby') || select.labels?.length) return;
            const nearbyLabel = select.closest('.field, form')?.querySelector('label');
            select.setAttribute('aria-label', nearbyLabel?.textContent.trim() || 'Selecionar opção');
        });
        document.querySelectorAll('input:not([type="hidden"])').forEach(input => {
            if (input.getAttribute('aria-label') || input.getAttribute('aria-labelledby') || input.labels?.length || input.placeholder) return;
            const container = input.closest('.field, .entity-field, [class*="entity-field"]');
            const nearbyLabel = container?.querySelector('label, .field__title, .entity-field__title');
            const fallback = input.disabled ? 'Valor informado' : 'Preencher campo';
            input.setAttribute('aria-label', nearbyLabel?.textContent.trim() || fallback);
        });
        document.querySelectorAll('.search-filter .dp__input').forEach(input => {
            if (!input.getAttribute('aria-label')) input.setAttribute('aria-label', 'Período dos eventos');
        });
        const intervalButtons = document.querySelectorAll('.search-filter .filter-btn button');
        setLabel(intervalButtons[0], 'Período anterior');
        setLabel(intervalButtons[1], 'Próximo período');
        document.querySelectorAll('button').forEach(button => {
            if ((button.textContent || '').trim() || button.getAttribute('aria-label') || button.getAttribute('aria-labelledby') || button.title) return;
            const iconName = button.querySelector('[data-icon]')?.getAttribute('data-icon') || '';
            const label = iconName.includes('search') ? 'Executar pesquisa'
                : iconName.includes('close') ? 'Fechar'
                : iconName.includes('filter') ? 'Filtrar'
                : 'Abrir opções';
            button.setAttribute('aria-label', label);
        });
        document.querySelectorAll('.selectButton.disabled').forEach(control => {
            control.style.setProperty('color', '#595959', 'important');
            control.style.setProperty('opacity', '1', 'important');
            control.setAttribute('aria-disabled', 'true');
            control.tabIndex = -1;
        });

        document.querySelectorAll('[role="tab"]').forEach((tab, index) => {
            const panelId = (tab.getAttribute('aria-controls') || tab.getAttribute('href') || '').replace(/^#/, '');
            if (!panelId) return;
            tab.setAttribute('aria-controls', panelId);
            if (!tab.id) tab.id = `tab-${panelId}-${index}`;
            tab.tabIndex = tab.getAttribute('aria-selected') === 'true' ? 0 : -1;
            const panel = document.getElementById(panelId)
                || document.querySelector(`.tab-component.${CSS.escape(panelId)}`);
            if (panel) {
                panel.id = panelId;
                panel.setAttribute('aria-labelledby', tab.id);
            }
        });

        document.querySelectorAll('.panel-nav__left, .panel-nav__right').forEach(container => {
            container.removeAttribute('role');
            container.querySelectorAll(':scope > li').forEach(item => item.setAttribute('role', 'presentation'));
        });
        document.querySelectorAll('main.panel-entity-card__main').forEach(cardMain => {
            cardMain.setAttribute('role', 'presentation');
        });
        document.querySelectorAll('dl.metadata__id').forEach(list => {
            if (list.querySelector(':scope > dt, :scope > dd')) return;
            const entityData = list.querySelector(':scope > .entity-data');
            const label = entityData?.querySelector(':scope > .entity-data__label');
            const data = entityData?.querySelector(':scope > .entity-data__data');
            if (!label || !data) return;
            const term = document.createElement('dt');
            const definition = document.createElement('dd');
            term.className = label.className;
            definition.className = data.className;
            term.textContent = label.textContent;
            while (data.firstChild) definition.appendChild(data.firstChild);
            entityData.replaceWith(term, definition);
        });

        const outerMain = document.getElementById('main-content');
        const nestedPageMain = outerMain?.querySelector('main:not(.panel-entity-card__main):not(.mc-card__content)');
        if (outerMain) outerMain.setAttribute('role', nestedPageMain ? 'presentation' : 'main');
        const main = nestedPageMain
            || outerMain
            || document.querySelector('main:not(.panel-entity-card__main)')
            || document.querySelector('[role="main"]')
            || document.getElementById('main-app');
        if (main && main.tagName !== 'MAIN') main.setAttribute('role', 'main');
        if (main && !document.querySelector('h1') && !document.querySelector('[data-a11y-page-title]')) {
            const heading = document.createElement('h1');
            heading.className = 'sr-only';
            heading.dataset.a11yPageTitle = '';
            heading.textContent = document.title || 'Conteúdo principal';
            main.prepend(heading);
        }
    }

    function setupKeyboardEnhancements() {
        document.addEventListener('click', event => {
            const trigger = event.target.closest('button, a[role="button"]');
            if (!trigger) return;
            const text = (trigger.textContent || trigger.getAttribute('aria-label') || '').trim();
            if (/^(criar|adicionar|inscrever)/i.test(text)) modalReturnFocus = trigger;
        }, true);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                const openDialog = [...document.querySelectorAll('[role="dialog"], .modal')]
                    .find(element => element.id !== 'mc-chat-panel'
                        && !element.closest('[data-accessibility-menu]')
                        && element.getClientRects().length
                        && getComputedStyle(element).visibility !== 'hidden');
                if (openDialog && modalReturnFocus) {
                    const trigger = modalReturnFocus;
                    window.setTimeout(() => {
                        if (trigger.isConnected) trigger.focus();
                    }, 150);
                }

                const openPopover = [...document.querySelectorAll('.user-menu, .notification-modal')]
                    .find(element => element.getClientRects().length && getComputedStyle(element).visibility !== 'hidden');
                const popoverRoot = openPopover?.closest('.mc-popover');
                const popoverTrigger = popoverRoot?.querySelector('.user, .notification-modal__menu-desk, .notification-modal__menu-mobile');
                if (popoverTrigger) {
                    event.preventDefault();
                    popoverTrigger.click();
                    popoverTrigger.focus();
                    return;
                }
            }

            const clickable = event.target.closest('a[role="button"]');
            if (clickable && (event.key === ' ' || (event.key === 'Enter' && !clickable.hasAttribute('href')))) {
                event.preventDefault();
                clickable.click();
                return;
            }

            const tab = event.target.closest('[role="tab"]');
            if (!tab || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            const tablist = tab.closest('[role="tablist"]');
            if (!tablist) return;
            const tabs = [...tablist.querySelectorAll('[role="tab"]:not([aria-disabled="true"])')];
            const current = tabs.indexOf(tab);
            if (current < 0) return;
            event.preventDefault();
            let next = current;
            if (event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
            if (event.key === 'ArrowRight') next = (current + 1) % tabs.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = tabs.length - 1;
            tabs[next].focus();
            tabs[next].click();
        });
    }

    function init() {
        setupMenu();
        setupKeyboardEnhancements();
        let scheduled = false;
        const scheduleEnhancement = () => {
            if (scheduled) return;
            scheduled = true;
            window.requestAnimationFrame(() => {
                scheduled = false;
                enhanceRenderedContent();
            });
        };
        new MutationObserver(scheduleEnhancement).observe(document.body, {
            attributes: true,
            attributeFilter: ['class'],
            childList: true,
            subtree: true,
        });
        scheduleEnhancement();
        window.setTimeout(scheduleEnhancement, 500);
        window.setTimeout(scheduleEnhancement, 1500);
    }

    applyState();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
