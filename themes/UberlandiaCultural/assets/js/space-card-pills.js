(function () {
    const pillRoutes = ['/espacos', '/search/turismo'];
    const currentPath = window.location.pathname.toLowerCase();
    const shouldEnhanceSpaceCards = pillRoutes.some((route) => currentPath.startsWith(route));

    function organizeOpportunityStatus(root = document) {
        const cards = [];

        if (root.nodeType === Node.ELEMENT_NODE && root.matches?.('.entity-card:not([data-opportunity-status-ready])')) {
            cards.push(root);
        }

        cards.push(...root.querySelectorAll?.('.entity-card:not([data-opportunity-status-ready])') || []);

        cards.forEach((card) => {
            const status = card.querySelector('.openSubscriptions');
            const details = card.querySelector('.user-info__attr') || card.querySelector('.user-info');

            if (!status || !details) {
                return;
            }

            details.appendChild(status);
            card.dataset.opportunityStatusReady = 'true';
        });
    }

    function enhanceTerms(root = document) {
        root.querySelectorAll('.entity-card__content--terms .terms:not([data-pills-ready])').forEach((container) => {
            const terms = container.textContent
                .split(',')
                .map((term) => term.trim())
                .filter(Boolean);

            if (!terms.length) {
                return;
            }

            container.textContent = '';
            container.dataset.pillsReady = 'true';

            terms.forEach((term) => {
                const pill = document.createElement('span');
                pill.className = 'entity-card__term-pill';
                pill.textContent = term;
                container.appendChild(pill);
            });
        });
    }

    function normalizeLabels(root = document) {
        root.querySelectorAll('.entity-card:not([data-labels-ready])').forEach((card) => {
            const descriptions = card.querySelectorAll('.entity-card__content--description');
            const locationLabel = descriptions[0]?.querySelector('.entity-card__content--description-local');
            const accessibilityLabel = descriptions[1]?.querySelector('label');
            const areaLabel = card.querySelector('.area__title');
            const tagLabel = card.querySelector('.tag__title');

            if (locationLabel) {
                locationLabel.textContent = 'Onde:';
            }

            if (accessibilityLabel?.firstChild) {
                accessibilityLabel.firstChild.textContent = 'Acessibilidade:';
            }

            if (areaLabel) {
                const count = card.querySelectorAll('.entity-card__content--terms-area .entity-card__term-pill').length;
                areaLabel.textContent = `Áreas de atuação (${count})`;
            }

            if (tagLabel) {
                const count = card.querySelectorAll('.entity-card__content--terms-tag .entity-card__term-pill').length;
                tagLabel.textContent = `Tags (${count})`;
            }

            card.dataset.labelsReady = 'true';
        });
    }

    function normalizeResultCount(root = document) {
        const counters = [];

        if (root.nodeType === Node.ELEMENT_NODE && root.matches?.('.foundResults')) {
            counters.push(root);
        }

        counters.push(...root.querySelectorAll?.('.foundResults:not([data-count-ready])') || []);

        counters.forEach((counter) => {
            const match = counter.textContent.trim().match(/^(\d+)\s+.+?\s+(encontrados?|encontradas?)$/i);

            if (match) {
                counter.textContent = `${match[1]} registros`;
                counter.dataset.countReady = 'true';
            }
        });
    }

    function init() {
        if (shouldEnhanceSpaceCards) {
            enhanceTerms();
            normalizeLabels();
        }

        organizeOpportunityStatus();

        normalizeResultCount();

        const observer = new MutationObserver((mutations) => {
            mutations.forEach(({ addedNodes }) => {
                addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (shouldEnhanceSpaceCards) {
                            enhanceTerms(node);
                            normalizeLabels(node);
                        }

                        organizeOpportunityStatus(node);

                        normalizeResultCount(node);
                    }
                });
            });
        });

        observer.observe(document.querySelector('#main-app') || document.body, {
            childList: true,
            subtree: true,
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
