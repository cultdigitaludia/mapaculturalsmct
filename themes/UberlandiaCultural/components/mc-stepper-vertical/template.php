<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 *
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: o acionador "Expandir/Diminuir" era um par
 * de <a> sem href (fora da ordem do Tab, sem Enter/Espaço e sem estado). Agora é
 * um único <button type="button"> com aria-expanded e aria-controls apontando
 * para o painel da etapa; como o elemento não é trocado, o foco permanece nele.
 * O nome da etapa entra no nome acessível em texto oculto (sr-only).
 * O id do painel também é entregue aos slots (panel-id) para acionadores
 * personalizados. O componente só é usado na gestão de Oportunidades. Ao
 * atualizar o MapasCulturais, compare este arquivo com a nova versão oficial.
 */

use MapasCulturais\i;

$this->import('
    mc-icon
')
?>
<div class="mc-stepper-vertical-wrapper">
<?php $this->applyTemplateHook('mc-stepper-vertical:before'); ?>
    <ol class="mc-stepper-vertical">
        <?php $this->applyTemplateHook('mc-stepper-vertical:begin'); ?>
        <template v-for="(step, index) in steps">
            <li :class="{active: step.active}">
                <section class="stepper-step">
                    <header :class="['stepper-header', {'open':step.active}]">
                        <slot class="stepper-header-title" name="header" :index="index" :step="step" :item="step.item" :panel-id="`mc-stepper-vertical-${$.uid}-${index}`">
                            <slot name="header-title" :index="index" :step="step" :item="step.item">{{step.item.name || step.item.title || step.item.label}}</slot>
                            <slot name="header-actions" :index="index" :step="step" :item="step.item" :panel-id="`mc-stepper-vertical-${$.uid}-${index}`">
                                <button type="button" class="expand-stepper" :aria-expanded="step.active ? 'true' : 'false'" :aria-controls="step.active ? `mc-stepper-vertical-${$.uid}-${index}` : null" @click="step.toggle()">
                                    <span v-if="step.active" class="expand-stepper__label"><?= i::__('Diminuir') ?></span>
                                    <span v-else class="expand-stepper__label"><?= i::__('Expandir') ?></span>
                                    <span v-if="step.item.name || step.item.title || step.item.label" class="sr-only">: {{step.item.name || step.item.title || step.item.label}}</span>
                                    <mc-icon :name="step.active ? 'arrowPoint-up' : 'arrowPoint-down'" aria-hidden="true"></mc-icon>
                                </button>
                            </slot>
                        </slot>
                    </header>
                    <main v-if="step.active" :id="`mc-stepper-vertical-${$.uid}-${index}`">
                        <slot :index="index" :step="step" :item="step.item">
                            o slot <strong><code>#default</code></strong> é obrigatório para o componente <strong><code>mc-stepper-vertical</code></strong>
                        </slot>
                    </main>
                </section>
            </li>
            <div class="add-phase">
                <slot name="after-li" :index="index" :step="step" :item="step.item"></slot>
            </div>
        </template>
        <?php $this->applyTemplateHook('mc-stepper-vertical:end'); ?>
    </ol>
    <?php $this->applyTemplateHook('mc-stepper-vertical:after'); ?>
</div>
