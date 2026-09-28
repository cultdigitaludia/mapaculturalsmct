// Mantém a aba principal ativa visível (centralizada quando possível) na edição
// administrativa de Oportunidades. Altera apenas o scrollLeft da lista de abas:
// não mexe em classes do Vue, no hash, na navegação ou na rolagem vertical.
(function () {
    const ROOT_SELECTOR = '.controller-opportunity.action-edit > #main-app > .main-app';
    const SCROLLER_SELECTOR = `${ROOT_SELECTOR} > .tabs-component > .tabs-component__header .tabs-component__buttons`;
    const ACTIVE_CLASS = 'tabs-component__button--active';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    let scroller = null;
    let classObserver = null;
    let frame = null;

    function findScroller() {
        return document.querySelector(SCROLLER_SELECTOR);
    }

    function findActiveTab() {
        return scroller.querySelector(`:scope > .${ACTIVE_CLASS}`)
            || scroller.querySelector(':scope > li > a[aria-selected="true"]')?.parentElement
            || null;
    }

    function centerActiveTab() {
        frame = null;

        if (!scroller?.isConnected) {
            attach(findScroller());
        }

        if (!scroller) {
            return;
        }

        const maxScroll = scroller.scrollWidth - scroller.clientWidth;
        const tab = findActiveTab();

        if (maxScroll <= 1 || !tab) {
            return;
        }

        const scrollerRect = scroller.getBoundingClientRect();
        const tabRect = tab.getBoundingClientRect();
        const offset = tabRect.left - scrollerRect.left - scroller.clientLeft;
        const target = scroller.scrollLeft + offset - (scroller.clientWidth - tabRect.width) / 2;
        const left = Math.round(Math.min(Math.max(target, 0), maxScroll));

        if (Math.abs(left - scroller.scrollLeft) < 1) {
            return;
        }

        scroller.scrollTo({
            left,
            behavior: reducedMotion.matches ? 'instant' : 'smooth',
        });
    }

    function scheduleCenter() {
        if (frame === null) {
            frame = requestAnimationFrame(centerActiveTab);
        }
    }

    function attach(element) {
        if (element === scroller) {
            return;
        }

        classObserver?.disconnect();
        scroller?.removeEventListener('click', scheduleCenter);
        scroller?.removeEventListener('focusin', scheduleCenter);

        scroller = element;
        classObserver = null;

        if (!scroller) {
            return;
        }

        // Observa somente o atributo class dos itens da lista principal. Como o
        // script altera apenas scrollLeft, as mutações não se retroalimentam.
        classObserver = new MutationObserver((mutations) => {
            if (mutations.some((mutation) => mutation.target.classList?.contains(ACTIVE_CLASS))) {
                scheduleCenter();
            }
        });
        classObserver.observe(scroller, { attributes: true, attributeFilter: ['class'], subtree: true });

        scroller.addEventListener('click', scheduleCenter);
        scroller.addEventListener('focusin', scheduleCenter);
    }

    function waitForTabs(root) {
        const found = findScroller();

        if (found) {
            attach(found);
            scheduleCenter();
            return;
        }

        // Aguarda a montagem do Vue e desconecta assim que a lista de abas surge.
        const mountObserver = new MutationObserver(() => {
            const element = findScroller();

            if (element) {
                mountObserver.disconnect();
                attach(element);
                scheduleCenter();
            }
        });
        mountObserver.observe(root, { childList: true, subtree: true });
    }

    function init() {
        const root = document.querySelector('.controller-opportunity.action-edit > #main-app');

        if (!root) {
            return;
        }

        waitForTabs(root);
        window.addEventListener('hashchange', scheduleCenter);
        window.addEventListener('resize', scheduleCenter);
        window.addEventListener('load', scheduleCenter, { once: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
