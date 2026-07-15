(function () {
    'use strict';

    const WEBGL_CACHE_KEY = 'vlibras_webgl_support';

    function hasWebGLSupport() {
        try {
            if (sessionStorage.getItem(WEBGL_CACHE_KEY) === '1') return true;
        } catch (e) {}

        const canvas = document.createElement('canvas');
        const supported = !!canvas.getContext('webgl');
        if (supported) {
            try {
                sessionStorage.setItem(WEBGL_CACHE_KEY, '1');
            } catch (e) {}
        }
        return supported;
    }

    function init() {
        const widget = document.querySelector('[vw]');
        if (!widget) return;

        function abort(reason) {
            widget.remove();
            if (reason) console.info('[VLibras] ' + reason);
        }

        if (!hasWebGLSupport()) {
            abort('WebGL indisponível; widget não inicializado.');
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://vlibras.gov.br/app/vlibras-plugin.js';
        script.onload = function () {
            if (!window.VLibras) {
                abort('Script carregado, mas window.VLibras não foi definido.');
                return;
            }
            try {
                new window.VLibras.Widget({
                    rootPath: 'https://vlibras.gov.br/app',
                    position: 'L',
                });
            } catch (e) {
                abort('Falha ao inicializar o widget: ' + e.message);
            }
        };
        script.onerror = function () {
            abort('Falha ao carregar vlibras-plugin.js.');
        };
        document.head.appendChild(script);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
