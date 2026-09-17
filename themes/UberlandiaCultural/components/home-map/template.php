<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
use MapasCulturais\i;
$cityCenter = json_encode([
    (float) ($app->config['maps.latitude'] ?? -18.9186),
    (float) ($app->config['maps.longitude'] ?? -48.2772),
]);
$this->import('
    mc-map 
    mc-map-card
');
?>
<!--TODO: Trocar a constante "true" abaixo por uma configuração que força a desativação do mapa. Ex: global.disableHomeMap = true -->
<div v-if="global.enabledEntities.spaces || global.enabledEntities.agents" class="home-map">
    <div class="home-map__header">
        <label class="title"><?= $this->text('title', i::__('Visualize também no mapa')) ?></label>
        <label class="description"><?= $this->text('description', i::__('Os agentes, espaços e eventos cadastrados contam com a geolocalização de seus endereços, encontre-os aqui:')) ?></label>
    </div>

    <div class="home-map__content">
        <mc-map
            :entities="entities"
            @ready="$event.setView(<?= $cityCenter ?>, 12); $event.options.wheelPxPerZoomLevel = 180; $event.options.wheelDebounceTime = 80">
            <template #popup="{entity}">
                <mc-map-card :entity="entity"></mc-map-card>
            </template>
        </mc-map>
    </div>
</div>
