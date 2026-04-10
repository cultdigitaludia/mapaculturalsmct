<?php
namespace UberlandiaCultural;
use MapasCulturais\i;
use MapasCulturais\App;
/**
 * @method void import(string $components) Importa lista de componentes Vue. * 
 */
class Theme extends \MapasCulturais\Themes\BaseV2\Theme
{
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
        $this->enqueueStyle('app-v2', 'main', 'css/theme-UberlandiaCultural.css');
        $this->assetManager->publishFolder('fonts');
        $this->enqueueScript('app-v2', 'chatbot', 'js/chatbot-widget.js');
        $this->assetManager->publishFolder('img'); 

        // Adiciona a rota de turismo ao controller de search
        $app->hook('GET(search.turismo)', function() use ($app) {
            $initial_pseudo_query = [
                'type' => [1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019]
            ];
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

.home-header { background: transparent !important; }
.home-header__content::before { background: none !important; }
.home-header__background .img > img { width: 100%; height: 100%; object-fit: cover; }
.home-header__content { min-height: 600px; }
.home-header__main { display: none !important; }
.main-footer__reg { background-color: #0055A5 !important; }
@media (max-width: 768px) {
    .home-header__background {
        background-image: url("/assets/img/capa_vertical.img.png") !important;
        background-size: cover !important;
        background-position: center !important;
    }
    .home-header__background .img > img {
        display: none !important;
    }
}
</style>';
            echo "<script>
                    document.addEventListener('DOMContentLoaded', (e) => {
                        let opacity = 0.01;
                        globalThis.opacityInterval = setInterval(() => {
                            if(opacity >= 1) {
                                clearInterval(globalThis.opacityInterval);
                            }
                            document.body.style.opacity = opacity;
                            opacity += 0.02;
                        },5);
                    });
                </script>";
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
        // for responsive
        $this->documentMeta[] = array("name" => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1.0');
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