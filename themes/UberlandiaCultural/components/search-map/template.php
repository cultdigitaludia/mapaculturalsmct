<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

$cityCenter = json_encode([
    (float) ($app->config['maps.latitude'] ?? -18.9186),
    (float) ($app->config['maps.longitude'] ?? -48.2772),
]);

$this->import('
    mc-map
    mc-map-card
    mc-loading
');
?>
<div class="search-map">
    <div class="search-map__filter">
        <div class="search-map__filter--filter">
            <slot name="filter" :count="entities.metadata?.count"></slot>
        </div>
    </div>

    <mc-map
        :entities="entities"
        @ready="$event.options.wheelPxPerZoomLevel = 180; $event.options.wheelDebounceTime = 80; type === 'space' && $event.setView(<?= $cityCenter ?>, 12); $emit('ready', $event)"
        @close-popup="$emit('closePopup', $event)"
        @open-popup="openPopUp($event)">
        <template #popup="{entity}">
            <mc-map-card :entity="entity"></mc-map-card>
        </template>
    </mc-map>
    <mc-loading :condition="loading"></mc-loading>
</div>
