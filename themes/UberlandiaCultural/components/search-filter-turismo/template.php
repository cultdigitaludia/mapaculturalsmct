<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
use MapasCulturais\i;
$this->import('
    mc-multiselect
    search-filter
');
?>
<search-filter :position="position" :pseudo-query="pseudoQuery">
    <label class="form__label">
        <?= i::_e('Filtros de turismo') ?>
    </label>
    <form class="form" @submit="$event.preventDefault()">
        <div class="field search-filter__filter-space-status">
            <label><?php i::_e('Status do espaço') ?></label>
            <label class="verified">
                <input v-model="pseudoQuery['@verified']" type="checkbox">
                <?php i::_e('Espaços oficiais') ?>
            </label>
            <label>
                <input v-model="pseudoQuery['@unverified']" type="checkbox">
                <?php i::_e('Espaços cadastrados') ?>
            </label>
        </div>
        <div class="field search-filter__filter-space-types">
            <label><?php i::_e('Tipos de espaços turísticos') ?></label>
            <mc-multiselect :model="pseudoQuery['type']" :items="types" placeholder="<?= i::esc_attr__('Selecione os tipos') ?>" hide-filter hide-button></mc-multiselect>
        </div>
    </form>
    <a class="clear-filter" @click="clearFilters()"><?php i::_e('Limpar todos os filtros') ?></a>
</search-filter>
