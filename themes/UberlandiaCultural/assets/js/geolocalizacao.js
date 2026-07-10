/**
 * Geolocalização Global - Mapa Cultural de Uberlândia
 * - Pede permissão ao carregar o site
 * - Centraliza TODOS os mapas na posição do usuário
 * - Não bloqueia navegação livre pelo mapa
 * - 100% gratuito (navigator.geolocation + OpenStreetMap)
 */

(function () {
    'use strict';

    const GEO_STORAGE_KEY = 'mc_user_location';
    const GEO_CACHE_MINUTOS = 10; // reusa a localização por 10 min sem pedir GPS de novo

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

    // ─── APLICAR NOS MAPAS ────────────────────────────────────────────────────
    function aplicarLocalizacao(lat, lng) {
        // 1. Sobrescreve o centro padrão do Mapas Culturais
        //    Todos os mc-map usam $MAPAS.config.map.center como default
        if (window.$MAPAS && window.$MAPAS.config && window.$MAPAS.config.map) {
            window.$MAPAS.config.map.center = { lat, lng };
            window.$MAPAS.config.map.defaultZoom = 14;
        }

        // 2. Centraliza mapas Leaflet já instanciados (se usuário já estava na página)
        document.querySelectorAll('.leaflet-container').forEach(function (el) {
            if (el._leaflet_map) {
                const mapa = el._leaflet_map;
                mapa.setView([lat, lng], 14);

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

        // 3. Observa novos mapas que apareçam depois (ex: usuário clica aba Mapa)
        const observer = new MutationObserver(function () {
            document.querySelectorAll('.leaflet-container').forEach(function (el) {
                if (el._leaflet_map && !el._geo_aplicado) {
                    el._geo_aplicado = true;
                    const mapa = el._leaflet_map;
                    mapa.setView([lat, lng], 14);

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
        if (!navigator.geolocation) return;
        solicitarLocalizacao();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
