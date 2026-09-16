(function () {
    'use strict';

    const ACTIVE_SELECTOR = '.tabs-component__button--active';
    const NAVIGATION_SELECTOR = '.mapas-terms .tabs-component__buttons';

    function getScrollContainer(navigation) {
        const candidates = [
            navigation,
            navigation.closest('.tabs-component__header--left'),
            navigation.closest('.tabs-component__header'),
        ].filter(Boolean);

        return candidates.reduce((selected, candidate) => {
            const overflow = candidate.scrollWidth - candidate.clientWidth;
            const selectedOverflow = selected.scrollWidth - selected.clientWidth;
            return overflow > selectedOverflow ? candidate : selected;
        }, candidates[0]);
    }

    function centerActiveTab(navigation, behavior = 'smooth') {
        const activeTab = navigation.querySelector(ACTIVE_SELECTOR);
        if (!activeTab) return;

        const scrollContainer = getScrollContainer(navigation);
        const containerRect = scrollContainer.getBoundingClientRect();
        const activeRect = activeTab.getBoundingClientRect();
        const offset = (activeRect.left + (activeRect.width / 2))
            - (containerRect.left + (containerRect.width / 2));

        if (Math.abs(offset) > 1) {
            scrollContainer.scrollBy({ left: offset, behavior });
        }
    }

    function setupNavigation(navigation) {
        if (navigation.dataset.termsNavigationReady === 'true') return;
        navigation.dataset.termsNavigationReady = 'true';

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const update = () => window.requestAnimationFrame(() => {
            centerActiveTab(navigation, reducedMotion ? 'auto' : 'smooth');
        });

        navigation.addEventListener('click', event => {
            if (!event.target.closest('.tabs-component__button')) return;
            window.requestAnimationFrame(update);
        });

        navigation.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            window.requestAnimationFrame(update);
        });

        const observer = new MutationObserver(mutations => {
            if (mutations.some(mutation => mutation.attributeName === 'class')) update();
        });

        observer.observe(navigation, {
            attributes: true,
            attributeFilter: ['class'],
            subtree: true,
        });

        window.addEventListener('hashchange', update);
        window.addEventListener('resize', update);
        centerActiveTab(navigation, 'auto');
    }

    function setupAll() {
        document.querySelectorAll(NAVIGATION_SELECTOR).forEach(setupNavigation);
    }

    setupAll();

    const pageObserver = new MutationObserver(setupAll);
    pageObserver.observe(document.documentElement, { childList: true, subtree: true });
})();
