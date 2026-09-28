<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 *
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: "Exibir/Ocultar" era um par de <h5> com
 * clique; agora é um único <button type="button"> com aria-expanded e
 * aria-controls para o texto da seção. Como o elemento não é trocado, o foco
 * permanece nele. Ao atualizar o MapasCulturais, compare este arquivo com a nova
 * versão oficial.
 */

use MapasCulturais\i;

$this->import('
    mc-icon
')
?>
<div class="registration-evaluation-info">
    <section v-for="(info, index, position) in infos" class="registration-evaluation-info__section">
        <div v-if=" showInfo(index)">
            <div class="registration-evaluation-info__header">
                <p v-if="index == 'general'" class="registration-evaluation-info__title semibold"><?= i::__('Informações gerais')?></p>
                <p v-if="index != 'general'" class="registration-evaluation-info__title semibold">{{index}}</p>
                <button type="button" class="registration-evaluation-info__toggle" @click="toggle(index)" :aria-expanded="activeItems[index] ? 'true' : 'false'" :aria-controls="activeItems[index] ? `registration-evaluation-info-${$.uid}-${position}` : null">
                    <span v-if="activeItems[index]"><?= i::__('Ocultar');?></span>
                    <span v-else><?= i::__('Exibir');?></span>
                    <span v-if="index == 'general'" class="sr-only">: <?= i::__('Informações gerais')?></span>
                    <span v-else class="sr-only">: {{index}}</span>
                    <mc-icon :name="activeItems[index] ? 'arrowPoint-up' : 'arrowPoint-down'" aria-hidden="true"></mc-icon>
                </button>
            </div>
            <div v-if="activeItems[index]" class="registration-evaluation-info__content" :id="`registration-evaluation-info-${$.uid}-${position}`">
                <h6>{{info}}</h6>
            </div>
        </div>
    </section>
</div>
