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

    // ─── BOTÃO "MINHA LOCALIZAÇÃO" (para o usuário re-centralizar) ────────────
    function adicionarBotaoRelocalizar() {
        const style = document.createElement('style');
        style.textContent = `
            #geo-btn-relocalizar {
                position: fixed;
                bottom: 24px;
                right: 16px;
                z-index: 9998;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #fff;
                border: 2px solid #bbb;
                font-size: 18px;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0,0,0,0.25);
                display: none;
                align-items: center;
                justify-content: center;
                transition: box-shadow 0.2s, transform 0.1s;
            }
            #geo-btn-relocalizar:hover {
                box-shadow: 0 4px 14px rgba(0,0,0,0.35);
                transform: scale(1.08);
            }
            .geo-mapa-ativo #geo-btn-relocalizar {
                display: flex;
            }
        `;
        document.head.appendChild(style);

        const btn = document.createElement('button');
        btn.id = 'geo-btn-relocalizar';
        btn.innerHTML = '📍';
        btn.title = 'Centralizar na minha localização';
        btn.setAttribute('aria-label', 'Centralizar na minha localização');

        btn.onclick = function () {
            // Força nova busca limpando cache
            sessionStorage.removeItem(GEO_STORAGE_KEY);
            document.querySelectorAll('.leaflet-container').forEach(el => {
                if (el._leaflet_map) el._geo_aplicado = false;
            });
            solicitarLocalizacao();
        };

        document.body.appendChild(btn);

        // Mostra botão só quando há mapa visível
        new MutationObserver(function () {
            const temMapa = !!document.querySelector('.leaflet-container');
            document.body.classList.toggle('geo-mapa-ativo', temMapa);
        }).observe(document.body, { childList: true, subtree: true });
    }

    // ─── INIT ─────────────────────────────────────────────────────────────────
    function init() {
        if (!navigator.geolocation) return;
        adicionarBotaoRelocalizar();
        solicitarLocalizacao();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
