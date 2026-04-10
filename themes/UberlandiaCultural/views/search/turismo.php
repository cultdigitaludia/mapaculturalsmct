<?php 
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
use MapasCulturais\i;
$this->import('
    search-filter
    mc-tab
    mc-tabs
    search
    search-filter-turismo
    search-list
    search-map
');
$this->breadcrumb = [
    ['label' => i::__('Inicio'), 'url' => $app->createUrl('site', 'index')],
    ['label' => i::__('Turismo'),  'url' => $app->createUrl('search', 'turismo')],
];
?>
<search page-title="<?= htmlspecialchars($this->text('title', i::__('Turismo'))) ?>" entity-type="space" :initial-pseudo-query="{'@verified': undefined, 'status_turismo': [], type:[1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019]}">
    <template #default="{pseudoQuery, changeTab}">
        <mc-tabs @changed="changeTab($event)" class="search__tabs" sync-hash>
            <template #before-tablist>
                <label class="search__tabs--before">
                    <?= i::_e('Visualizar como:') ?>
                </label>
            </template>
            <mc-tab icon="list" label="<?php i::esc_attr_e('Lista') ?>" slug="list">
                <div class="search__tabs--list">
                    <search-list :pseudo-query="pseudoQuery" type="space" select="name,type,shortDescription,files.avatar,seals,endereco,terms,acessibilidade">
                        <template #filter>
                            <search-filter-turismo :pseudo-query="pseudoQuery"></search-filter-turismo>
                        </template>
                    </search-list>
                </div>
            </mc-tab>
            <mc-tab icon="map" label="<?php i::esc_attr_e('Mapa') ?>" slug="map">
                <div class="search__tabs--map">
                    <search-map type="space" :pseudo-query="pseudoQuery">
                        <template #filter>
                            <search-filter-turismo :pseudo-query="pseudoQuery" position="map"></search-filter-turismo>
                        </template>
                    </search-map>
                </div>
            </mc-tab>
        </mc-tabs>
    </template>
</search>
