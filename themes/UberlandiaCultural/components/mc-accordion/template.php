<?php

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 *
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: com with-text (acordeões "Expandir/Diminuir"
 * da configuração de fases e de recurso de Oportunidades), o acionador era uma
 * <div> com clique, fora da ordem do Tab e sem estado. Nesse caso ele passa a
 * ser um <button type="button"> com aria-expanded e aria-controls. Sem with-text
 * (FAQ, planilhas, filtros de tabelas, critérios) a marcação oficial é mantida.
 * Ao atualizar o MapasCulturais, compare este arquivo com a nova versão oficial.
 */

use MapasCulturais\i;

$this->import('
    mc-tag-list
    mc-title
');
?>
<section  class="mc-accordion">
    <header @click="toggle()" :class="{ 'mc-accordion__header--active': active }" class="mc-accordion__header">
        <mc-title tag="h3" class="bold mc-accordion__title">
            <slot name="title"></slot>
        </mc-title>

        <button v-if="withText" type="button" ref="icon" @click.stop="toggle(true)" class="mc-accordion__icon mc-accordion__toggle" :aria-expanded="active ? 'true' : 'false'" :aria-controls="active ? `mc-accordion-${$.uid}` : null">
            <slot name="icon">
                <span class="mc-accordion__icon">
                    <span v-if="active" class="mc-accordion__label">
                        <?= i::__('Diminuir') ?>
                    </span>
                    <span v-else class="mc-accordion__label">
                        <?= i::__('Expandir') ?>
                    </span>
                </span>
            </slot>
            <span v-if="$slots.icon" class="sr-only">
                <template v-if="active"><?= i::__('Diminuir') ?></template>
                <template v-else><?= i::__('Expandir') ?></template>
            </span>
            <mc-icon :name="active ? 'arrowPoint-up' : 'arrowPoint-down'" class="primary__color" aria-hidden="true"></mc-icon>
        </button>

        <div v-else ref="icon" @click.stop="toggle(true)" class="mc-accordion__icon">
            <slot name="icon"></slot>
            <mc-icon :name="active ? 'arrowPoint-up' : 'arrowPoint-down'" class="primary__color"></mc-icon>
        </div>
    </header>
    <div v-if="active" class="mc-accordion__content" :id="`mc-accordion-${$.uid}`">
        <slot name="content"></slot>
    </div>
</section>
