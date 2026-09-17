/**
 * Geolocalização Global - Mapa Cultural de Uberlândia
 * - Pede permissão ao carregar o site
 * - Centraliza o mapa inicial e a listagem de espaços na posição do usuário
 * - Mantém os mapas de cadastro centralizados na localização da entidade
 * - Não bloqueia navegação livre pelo mapa
 * - 100% gratuito (navigator.geolocation + OpenStreetMap)
 */

(function () {
    'use strict';

    const GEO_STORAGE_KEY = 'mc_user_location';
    const GEO_CACHE_MINUTOS = 10; // reusa a localização por 10 min sem pedir GPS de novo
    const GEO_ZOOM = 12;
    const WHEEL_PX_PER_ZOOM_LEVEL = 180;
    const WHEEL_DEBOUNCE_TIME = 80;

    function configurarInteracaoMapa(mapa) {
        if (!mapa || mapa._uberlandiaInteracaoConfigurada) return;

        mapa._uberlandiaInteracaoConfigurada = true;
        mapa.options.wheelPxPerZoomLevel = WHEEL_PX_PER_ZOOM_LEVEL;
        mapa.options.wheelDebounceTime = WHEEL_DEBOUNCE_TIME;

        const container = mapa.getContainer?.();
        if (container) {
            container._leaflet_map = mapa;
        }

        if (!mapa.zoomControl && window.L?.control?.zoom) {
            mapa.zoomControl = L.control.zoom({ position: 'topleft' }).addTo(mapa);
        }
    }

    function configurarMapasLeaflet() {
        if (!window.L?.Map || L.Map.prototype._uberlandiaInteracaoHook) return;

        L.Map.prototype._uberlandiaInteracaoHook = true;
        L.Map.addInitHook(function () {
            configurarInteracaoMapa(this);
        });
    }

    // ─── LER CACHE DE LOCALIZAÇÃO ─────────────────────────────────────────────
    function lerCache() {
        try {
            const raw = sessionStorage.getItem(GEO_STORAGE_KEY);
            if (!raw) return null;
            const data = JSON.parse(raw);
            const idade = (Date.now() - data.timestamp) / 1000 / 60;
            if (idade > GEO_CACHE_MINUTOS) return null;
            return data;
        } catch (e) {
            return null;
        }
    }

    function salvarCache(lat, lng) {
        try {
            sessionStorage.setItem(GEO_STORAGE_KEY, JSON.stringify({
                lat, lng, timestamp: Date.now()
            }));
        } catch (e) {}
    }

    function paginaTemMapaDaCidade() {
        return Boolean(
            document.querySelector('home-map, .home-map') ||
            document.body.matches('.controller-search.action-spaces')
        );
    }

    function mapasDaCidade() {
        return document.querySelectorAll([
            '.home-map .leaflet-container',
            'body.controller-search.action-spaces .leaflet-container',
        ].join(','));
    }

    // ─── APLICAR NOS MAPAS COM VISÃO DA CIDADE ────────────────────────────────
    function aplicarLocalizacao(lat, lng) {
        if (!paginaTemMapaDaCidade()) return;

        // 1. Sobrescreve o centro padrão do Mapas Culturais
        //    antes da montagem do mapa da página inicial ou da listagem.
        if (window.$MAPAS && window.$MAPAS.config && window.$MAPAS.config.map) {
            window.$MAPAS.config.map.center = { lat, lng };
            window.$MAPAS.config.map.defaultZoom = GEO_ZOOM;
        }

        // 2. Centraliza os mapas da cidade que já estiverem instanciados.
        mapasDaCidade().forEach(function (el) {
            if (el._leaflet_map) {
                const mapa = el._leaflet_map;
                mapa.setView([lat, lng], GEO_ZOOM);

                // Marcador "Você está aqui"
                if (window._geoMarcadorUsuario) {
                    try { mapa.removeLayer(window._geoMarcadorUsuario); } catch(e) {}
                }
                window._geoMarcadorUsuario = L.circleMarker([lat, lng], {
                    radius: 9,
                    fillColor: '#1a73e8',
                    color: '#ffffff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 1
                })
                .addTo(mapa)
                .bindTooltip('📍 Você está aqui', { permanent: false, direction: 'top' });
            }
        });

        // 3. Aguarda os mapas caso o Vue ainda não os tenha montado.
        const observer = new MutationObserver(function () {
            mapasDaCidade().forEach(function (el) {
                if (el._leaflet_map && !el._geo_aplicado) {
                    el._geo_aplicado = true;
                    const mapa = el._leaflet_map;
                    mapa.setView([lat, lng], GEO_ZOOM);

                    L.circleMarker([lat, lng], {
                        radius: 9,
                        fillColor: '#1a73e8',
                        color: '#ffffff',
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 1
                    })
                    .addTo(mapa)
                    .bindTooltip('📍 Você está aqui', { permanent: false, direction: 'top' });
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    // ─── SOLICITAR LOCALIZAÇÃO ────────────────────────────────────────────────
    function solicitarLocalizacao() {
        if (!navigator.geolocation) return;

        // Verifica cache primeiro — evita pedir GPS repetidamente
        const cache = lerCache();
        if (cache) {
            aplicarLocalizacao(cache.lat, cache.lng);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                salvarCache(lat, lng);
                aplicarLocalizacao(lat, lng);
            },
            function (erro) {
                // Usuário negou ou erro — não faz nada, mapa abre normalmente
                console.info('[Geo] Localização não disponível:', erro.message);
            },
            {
                timeout: 10000,
                maximumAge: 300000, // aceita posição de até 5 min atrás do GPS
                enableHighAccuracy: false // false = mais rápido, economiza bateria
            }
        );
    }

    // ─── INIT ─────────────────────────────────────────────────────────────────
    function init() {
        configurarMapasLeaflet();

        if (!navigator.geolocation) return;
        solicitarLocalizacao();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
