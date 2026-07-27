(() => {
    const orderingSelector = '.panel-evaluations__filter .order';
    const searchSelector = [
        '.panel-evaluations__filter .search .input',
        '.registrations__filter .search .input',
    ].join(',');
    const defaultOrder = 'createTimestamp DESC';
    const searchPlaceholder = 'Buscar por palavras-chave';
    const panelTitleReplacements = new Map([
        ['Painel de controle', 'Painel de Controle'],
        ['Minhas oportunidades', 'Minhas Oportunidades'],
        ['Minhas inscrições', 'Minhas Inscrições'],
        ['Minhas avaliações', 'Minhas Avaliações'],
        ['Minhas validações', 'Minhas Validações'],
        ['Meus agentes', 'Meus Agentes'],
        ['Meus espaços', 'Meus Espaços'],
        ['Meus eventos', 'Meus Eventos'],
        ['Meus projetos', 'Meus Projetos'],
        ['Meus aplicativos', 'Meus Aplicativos'],
        ['Conta e privacidade', 'Conta e Privacidade'],
        ['Meu perfil', 'Meu Perfil'],
    ]);

    const findMatches = (root, selector) => root.matches?.(selector)
        ? [root]
        : root.querySelectorAll?.(selector) ?? [];

    const normalizePanelTitle = (textNode) => {
        const currentText = textNode.nodeValue.trim();
        const replacement = panelTitleReplacements.get(currentText);

        if (replacement) {
            textNode.nodeValue = textNode.nodeValue.replace(currentText, replacement);
        }
    };

    const normalizePanelTitles = (root) => {
        if (root.nodeType === Node.TEXT_NODE) {
            normalizePanelTitle(root);
            return;
        }

        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        let textNode;

        while ((textNode = walker.nextNode())) {
            normalizePanelTitle(textNode);
        }
    };

    const initializePanelFilters = (root = document) => {
        findMatches(root, orderingSelector).forEach((select) => {
            if (!select.value) {
                select.value = defaultOrder;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        findMatches(root, searchSelector).forEach((input) => {
            input.placeholder = searchPlaceholder;
        });

        if (document.body.classList.contains('controller-panel')) {
            normalizePanelTitles(root);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        initializePanelFilters();

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    initializePanelFilters(node);
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
})();
