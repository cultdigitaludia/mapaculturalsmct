/**
 * Configuração compartilhada dos mapas Leaflet - Mapa Cultural de Uberlândia
 *
 * Registrado no grupo "components" com dependência de "components-init", este
 * arquivo executa logo depois do vue-init.js (que define o L) e antes da
 * montagem do Vue, ou seja, antes de qualquer mapa existir.
 *
 * - Sensibilidade do zoom: todos os mapas usam a mesma interação da página
 *   inicial, como padrão da classe L.Map. Centro e zoom inicial continuam
 *   definidos por cada mapa.
 * - Ícones padrão: o @vue-leaflet redefine L.Icon.Default a cada mapa com URLs
 *   geradas pelo laravel-mix do núcleo (/images/vendor/leaflet/dist/...), que
 *   não existem na pasta pública e respondem 404. As três imagens (Leaflet
 *   1.7.1) são versionadas no tema e publicadas pelo asset manager; as URLs
 *   publicadas chegam em $MAPAS.config.leafletIcons.
 */
(function () {
    'use strict';

    const L = globalThis.L;
    if (!L || !L.Map || !L.Icon || !L.Icon.Default) {
        return;
    }

    // Sensibilidade do zoom (mesmos valores do mapa da página inicial).
    L.Map.mergeOptions({
        wheelPxPerZoomLevel: 180,
        wheelDebounceTime: 80,
    });

    // Referência do mapa no container, usada pelo geolocalizacao.js para
    // marcar a posição do usuário nos mapas da cidade.
    L.Map.addInitHook(function () {
        this.getContainer()._leaflet_map = this;
    });

    const icones = globalThis.$MAPAS?.config?.leafletIcons;
    if (!icones || L.Icon.Default._uberlandiaIcones) {
        return;
    }

    const porArquivo = {
        'marker-icon.png': icones.iconUrl,
        'marker-icon-2x.png': icones.iconRetinaUrl,
        'marker-shadow.png': icones.shadowUrl,
    };

    function urlPublicada(url) {
        if (typeof url !== 'string' || url.indexOf('/images/vendor/leaflet/') === -1) {
            return url;
        }
        const arquivo = url.split('?')[0].split('/').pop();
        return porArquivo[arquivo] || url;
    }

    const mergeOptions = L.Icon.Default.mergeOptions;
    L.Icon.Default._uberlandiaIcones = true;
    L.Icon.Default.mergeOptions = function (options) {
        if (options) {
            ['iconUrl', 'iconRetinaUrl', 'shadowUrl'].forEach(function (chave) {
                if (options[chave]) {
                    options[chave] = urlPublicada(options[chave]);
                }
            });
        }
        return mergeOptions.call(this, options);
    };

    // Mesmo efeito do @vue-leaflet (URLs explícitas), já com os arquivos publicados.
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconUrl: icones.iconUrl,
        iconRetinaUrl: icones.iconRetinaUrl,
        shadowUrl: icones.shadowUrl,
    });
})();
