<?php
namespace UberlandiaCultural;
use MapasCulturais\i;
use MapasCulturais\App;
/**
 * @method void import(string $components) Importa lista de componentes Vue. * 
 */
class Theme extends \MapasCulturais\Themes\BaseV2\Theme
{
    /**
     * Tipos de espaço da seção Turismo ("onde ir e o que usar como visitante").
     * A seção Espaços ("onde a cultura acontece") lista todos os demais tipos,
     * de modo que cada espaço aparece em uma única seção.
     */
    const TURISMO_SPACE_TYPES = [
        // Pontos turísticos e atrativos naturais
        1000, 1010,
        // Templos e espaços religiosos
        80, 81, 82, 83, 84, 85, 1003, 1004, 1005,
        // Patrimônio histórico (bens culturais de natureza material)
        200, 201, 202, 203, 204, 205, 206, 207,
        // Gastronomia, hospedagem e agências
        1001, 1018, 1019,
        // Compras
        1006, 1007, 1008,
        // Esporte e lazer
        1002, 1011, 1012,
        // Segurança pública
        1017,
    ];

    function getVersion() {
        return 2;
    }
    static function getThemeFolder()
    {
        return __DIR__;
    }
    function _init()
    {
        $app = App::i();
        $this->bodyClasses[] = 'base-v2';
        $this->enqueueStyle('app-v2', 'main', 'css/theme-BaseV2.css');
        $this->assetManager->publishFolder('fonts');
        $this->enqueueScript('app-v2', 'accessibility', 'js/accessibility.js');
        $this->enqueueScript('app-v2', 'terms-navigation', 'js/terms-navigation.js');
        // Culturese Bot temporariamente desativado até a revisão do widget.
        $this->enqueueScript('app-v2', 'geolocalizacao', 'js/geolocalizacao.js');
        $this->enqueueScript('app-v2', 'vlibras', 'js/vlibras-widget.js');
        $this->enqueueScript('app-v2', 'panel-ordering', 'js/panel-ordering.js');
        $this->enqueueScript('app-v2', 'space-card-pills', 'js/space-card-pills.js');
        $this->enqueueScript('app-v2', 'opportunity-edit-tabs', 'js/opportunity-edit-tabs.js');
        $this->assetManager->publishFolder('img');

        // Configuração compartilhada dos mapas Leaflet (sensibilidade do zoom e
        // ícones padrão publicados pelo tema), carregada só em páginas com mapa.
        $app->hook('component(<<mc-map|subsite-config-map>>):before', function () {
            $this->enqueueScript('components', 'uberlandia-leaflet-config', 'js/leaflet-config.js', ['components-init']);
            $this->jsObject['config']['leafletIcons'] = [
                'iconUrl' => $this->asset('img/leaflet/marker-icon.png', false),
                'iconRetinaUrl' => $this->asset('img/leaflet/marker-icon-2x.png', false),
                'shadowUrl' => $this->asset('img/leaflet/marker-shadow.png', false),
            ];
        });

        // Seções Turismo × Espaços: as listagens enviam "@secao" na consulta e a
        // API restringe os tipos. Se o usuário filtrou tipos, vale o filtro dele
        // (o filtro de cada seção só oferece os tipos da própria seção).
        $this->jsObject['config']['sectionSpaceTypes'] = ['turismo' => self::TURISMO_SPACE_TYPES];

        $app->hook('API.<<*>>(space).params', function (&$params) {
            if (!isset($params['@secao'])) {
                return;
            }
            $secao = $params['@secao'];
            unset($params['@secao']);

            if (!empty($params['type']) || !in_array($secao, ['turismo', 'espacos'], true)) {
                return;
            }
            $types = implode(',', self::TURISMO_SPACE_TYPES);
            $params['type'] = $secao === 'turismo' ? "IN({$types})" : "!IN({$types})";
        });

        $app->hook('search-spaces-initial-pseudo-query', function (&$initial_pseudo_query) {
            $initial_pseudo_query['@secao'] = 'espacos';
        });

        $app->hook('component(mc-icon).iconset', function (&$iconset) {
            $iconset['panel-opportunities'] = 'material-symbols:campaign';
            $iconset['panel-registrations'] = 'material-symbols:assignment';
            $iconset['panel-evaluations'] = 'material-symbols:rate-review';
            $iconset['panel-validations'] = 'material-symbols:fact-check';
            $iconset['notification-bell'] = 'material-symbols:notifications-rounded';
        });

        // Espaços legados sem país selecionado usam a visualização internacional,
        // que oculta endereço e mapa de visitantes quando publicLocation é nulo.
        // O mapa só aparece com coordenadas definidas: (0,0) é "sem localização"
        // e mostraria um ponto no oceano.
        $app->hook('template(space.single.international-address-view):end', function () {
            ?>
            <template v-if="entity.publicLocation == null && !verifiedAdress() && entity.endereco && entity.location">
                <div class="international-address-view__title col-12">
                    <label><?= i::__('Endereço') ?></label>
                </div>
                <div class="col-12">
                    <p class="international-address-view__address">{{ entity.endereco }}</p>
                    <entity-map v-if="Number(entity.location.lat ?? entity.location.latitude) || Number(entity.location.lng ?? entity.location.longitude)" :entity="entity"></entity-map>
                </div>
            </template>
            <?php
        });

        $app->hook('entity(Space).validationErrors', function (&$errors) {
            if ($this->acessibilidade !== 'Sim') {
                return;
            }

            $physicalAccessibility = $this->acessibilidade_fisica;
            $physicalAccessibility = is_array($physicalAccessibility)
                ? $physicalAccessibility
                : ($physicalAccessibility ? [$physicalAccessibility] : []);

            $hasResource = false;
            $hasNoAccessibilityOption = false;
            foreach ($physicalAccessibility as $resource) {
                if (!is_string($resource) || trim($resource) === '') {
                    continue;
                }

                if ($resource === '@NA') {
                    $hasNoAccessibilityOption = true;
                } else {
                    $hasResource = true;
                }
            }

            if (!$hasResource || $hasNoAccessibilityOption) {
                $message = i::__('Selecione ao menos um recurso de acessibilidade física. A opção "Não possui" não é válida quando a acessibilidade é "Sim".');
                $errors['acessibilidade'][] = $message;
                $errors['acessibilidade_fisica'][] = $message;
            }
        });

        $app->hook('template(space.edit.mc-card-content-acessibilidade_fisica):begin', function () {
            ?>
            <p v-if="entity.acessibilidade === 'Sim'" class="field__description">
                <?= i::__('Obrigatório: selecione ao menos um recurso de acessibilidade física. "Não possui" não é válido com "Sim".') ?>
            </p>
            <?php
        });

        $app->hook('panel.nav', function (&$navItems) {
            $iconsByRoute = [
                'panel/opportunities' => 'panel-opportunities',
                'panel/registrations' => 'panel-registrations',
                'panel/evaluations' => 'panel-evaluations',
                'panel/validations' => 'panel-validations',
            ];

            if (!isset($navItems['opportunities']['items'])) {
                return;
            }

            foreach ($navItems['opportunities']['items'] as &$item) {
                if (isset($iconsByRoute[$item['route']])) {
                    $item['icon'] = $iconsByRoute[$item['route']];
                }
            }
            unset($item);
        }, 100);

        // Adiciona a rota de turismo ao controller de search
        $app->hook('GET(search.turismo)', function() use ($app) {
            $initial_pseudo_query = ['@secao' => 'turismo'];
            $this->render('turismo', ['initial_pseudo_query' => $initial_pseudo_query]);
        });

        $app->hook('template(<<*>>.head):end', function () {
            echo '<style>
@font-face {
    font-family: "AvenirNext";
    src: url("/assets/fonts/AvenirNextLTPro-BoldCn.woff") format("woff");
    font-weight: bold;
}
.home-entities .card__left--content-title .title {
    font-family: "AvenirNext", sans-serif !important;
    font-size: 3rem !important;
    font-weight: bold !important;
    text-transform: uppercase !important;
}
.home-entities .card__right .button {
    font-size: 1.2rem !important;
    margin-top: 1rem !important;
}
.home-entities .card__right {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
}
@media (max-width: 800px) {
    .home-entities__content--cards .card__left--img {
        aspect-ratio: 778 / 260;
        height: auto !important;
    }
    .home-entities__content--cards .card__left--img img {
        display: block;
        height: 100%;
        min-height: 0 !important;
    }
    .home-entities__content--cards .card__left--content {
        grid-template-columns: minmax(0, 52%) !important;
        justify-items: start !important;
        align-items: center !important;
        padding: 0 1.25rem !important;
    }
    .home-entities .card__left--content-title {
        max-width: 100%;
    }
    .home-entities .card__left--content-title .title {
        display: block;
        font-size: clamp(1.625rem, 8vw, 2.25rem) !important;
        line-height: 0.95 !important;
        overflow-wrap: normal;
    }
    .home-entities .card--opportunities .card__left--content-title .title {
        font-size: clamp(1.125rem, 5.4vw, 1.625rem) !important;
    }
    .home-entities .card--agents .card__left--content-title .title,
    .home-entities .card--projects .card__left--content-title .title {
        font-size: clamp(1.5rem, 7vw, 2rem) !important;
    }
}

.home-header { background: transparent !important; }
.home-header__content::before { background: none !important; }
.home-header__background .img > img { width: 100%; height: 100%; object-fit: cover; }
.home-header__content { min-height: 600px; }
.home-header__main { display: none !important; }
.main-footer__reg { background-color: #0055A5 !important; }
@media (max-width: 768px) {
    .home-header__content {
        aspect-ratio: 1070 / 1338;
        min-height: auto !important;
        padding: 0 !important;
    }
    .home-header__background {
        background-image: url("/assets/img/capa_vertical.png") !important;
        background-size: calc(100% + 2px) 100% !important;
        background-position: center top !important;
        background-repeat: no-repeat !important;
    }
    .home-header__background .img > img {
        display: none !important;
    }
}
</style>';
        });
        $app->hook('view.render(<<*>>):before', function() use($app) {
            $this->addDocumentMetas();
        });
    }

    function register()
    {
        $app = App::i();

        // Registra a taxonomia Status do Espaço Turístico
        $app->registerTaxonomy(
            'MapasCulturais\Entities\Space',
            new \MapasCulturais\Definitions\Taxonomy(
                100,
                'status_turismo',
                'Status do Espaço',
                ['Espaços Oficiais', 'Espaços Cadastrados'],
                false,
                ['MapasCulturais\Entities\Space']
            )
        );
    }

    /**
     * Texto de apresentação de cada seção, usado no topo das listagens
     * (componente search) e nos cards da página inicial (home-entities).
     * "tip" e "link" apontam a seção vizinha, para quem procurava outra coisa.
     */
    function getSectionDescriptions(): array {
        $app = App::i();

        return [
            'turismo' => [
                'text' => i::__('Descubra o que visitar em Uberlândia: atrativos naturais, patrimônio histórico, gastronomia, hospedagem, compras, estruturas esportivas, templos e outros pontos de interesse para moradores e visitantes.'),
                'tip' => i::__('Procurando teatros, museus e bibliotecas?'),
                'link' => ['label' => i::__('Veja a seção Espaços'), 'url' => $app->createUrl('search', 'spaces')],
            ],
            'opportunity' => [
                'text' => i::__('Encontre editais, chamamentos, prêmios, oficinas e outras oportunidades da cultura e do turismo. Confira os prazos e as regras de cada uma e faça sua inscrição pela própria plataforma.'),
            ],
            'agent' => [
                'text' => i::__('Conheça artistas, grupos, coletivos, produtores e instituições que fazem a cultura de Uberlândia. Cada perfil é criado e mantido pelo próprio agente, que escolhe quais informações deseja tornar públicas.'),
            ],
            'event' => [
                'text' => i::__('Confira a agenda cultural da cidade: shows, espetáculos, exposições, oficinas e outras atividades com data, horário e local definidos.'),
                'tip' => i::__('Quer conhecer festivais, programas e editais completos?'),
                'link' => ['label' => i::__('Veja a seção Projetos'), 'url' => $app->createUrl('search', 'projects')],
            ],
            'space' => [
                'text' => i::__('Conheça os locais onde a cultura acontece em Uberlândia: teatros, museus, bibliotecas, centros culturais, salas de cinema e outros espaços dedicados às atividades artísticas e culturais.'),
                'tip' => i::__('Procurando onde comer, se hospedar ou passear?'),
                'link' => ['label' => i::__('Veja a seção Turismo'), 'url' => $app->createUrl('search', 'turismo')],
            ],
            'project' => [
                'text' => i::__('Conheça as iniciativas culturais de médio e longo prazo, como festivais, mostras, programas, leis de incentivo e editais. Um projeto pode reunir vários eventos ao longo do tempo. Por exemplo, o Festival Municipal de Cultura é um projeto, e cada apresentação dentro dele é um evento.'),
                'tip' => i::__('Quer saber o que acontece em uma data específica?'),
                'link' => ['label' => i::__('Veja a seção Eventos'), 'url' => $app->createUrl('search', 'events')],
            ],
        ];
    }

    function addDocumentMetas() {
        $app = App::i();
        $entity = $this->controller->requestedEntity;
        $site_name = $app->siteName;
        $image_url_twitter = $app->view->asset($app->config['share.image_twitter'], false);
        $image_url = $app->view->asset($app->config['share.image'], false);
        $title = $app->view->getTitle($entity);
        if ($entity) {
            $description = $entity->shortDescription ? $entity->shortDescription : $title;
            if ($entity->avatar && $entity->avatar->transform('avatarBig')){
                $image_url = $entity->avatar->transform('avatarBig')->url;
                $image_url_twitter = $entity->avatar->transform('avatarBig')->url;
            }
        } else {
            $description = $app->siteDescription;
        }
        // for google
        $this->documentMeta[] = array("name" => 'description', 'content' => $description);
        $this->documentMeta[] = array("name" => 'keywords', 'content' => $site_name);
        $this->documentMeta[] = array("name" => 'author', 'content' => $site_name);
        $this->documentMeta[] = array("name" => 'copyright', 'content' => $site_name);
        $this->documentMeta[] = array("name" => 'application-name', 'content' => $site_name);
        // for twitter
        $this->documentMeta[] = array("name" => 'twitter:card', 'content' => 'photo');
        $this->documentMeta[] = array("name" => 'twitter:title', 'content' => $title);
        $this->documentMeta[] = array("name" => 'twitter:description', 'content' => $description);
        $this->documentMeta[] = array("name" => 'twitter:image', 'content' => $image_url_twitter);
        // for facebook/Linkedin
        $this->documentMeta[] = array("property" => 'og:title', 'content' => $title);
        $this->documentMeta[] = array("property" => 'og:type', 'content' => 'article');
        $this->documentMeta[] = array("property" => 'og:image', 'content' => $image_url);
        $this->documentMeta[] = array("property" => 'og:image:url', 'content' => $image_url);
        $this->documentMeta[] = array("property" => 'og:description', 'content' => $description);
        $this->documentMeta[] = array("property" => 'og:site_name', 'content' => $site_name);
        $this->documentMeta[] = array("property" => 'og:image:width', 'content' => "1200");
        $this->documentMeta[] = array("property" => 'og:image:height', 'content' => "630");
        
        if ($entity) {
            $this->documentMeta[] = array("property" => 'og:url', 'content' => $entity->singleUrl);
            $this->documentMeta[] = array("property" => 'og:published_time', 'content' => $entity->createTimestamp->format('Y-m-d'));
        }
    }
}
