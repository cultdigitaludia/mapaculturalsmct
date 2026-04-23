<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

$uri = $_SERVER['REQUEST_URI'] ?? '';

$banners = [
    'turismo'       => 'img/bannertopo5.png',
    'opportunities' => 'img/bannertopo1.png',
    'events'        => 'img/bannertopo3.png',
    'spaces'        => 'img/bannertopo4.png',
    'agents'        => 'img/bannertopo2.png',
    'projects'      => 'img/bannertopo6.png',
];

$routes = [
    'turismo'               => 'turismo',
    '/search/turismo'       => 'turismo',
    '/opportunities'        => 'opportunities',
    '/oportunidades'        => 'opportunities',
    '/search/opportunities' => 'opportunities',
    '/events'               => 'events',
    '/eventos'              => 'events',
    '/search/events'        => 'events',
    '/spaces'               => 'spaces',
    '/espacos'              => 'spaces',
    '/search/spaces'        => 'spaces',
    '/agents'               => 'agents',
    '/agentes'              => 'agents',
    '/search/agents'        => 'agents',
    '/projects'             => 'projects',
    '/projetos'             => 'projects',
    '/search/projects'      => 'projects',
];

$current = '';
foreach ($routes as $route => $section) {
    if (strpos($uri, $route) !== false) {
        $current = $section;
        break;
    }
}

if ($current && isset($banners[$current])):
    $banner_url = $this->asset($banners[$current], false);
?>
<div class="page-banner">
    <img src="<?= $banner_url ?>" alt="" style="width:100%; height:auto; display:block;" />
</div>
<?php endif; ?>
