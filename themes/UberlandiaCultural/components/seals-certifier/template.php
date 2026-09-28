<?php

use MapasCulturais\i;

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 *
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: "Adicionar selo" (abre o seletor de selos)
 * era uma <div> com clique e "remover selo" (abre a confirmação) era um ícone
 * com clique. Os dois agora são <button type="button"> com aria-haspopup e nome
 * acessível; os ícones ficam ocultos para leitores de tela. O link de cada selo
 * (navegação real) continua <a href> e ganhou o nome do selo. Ao atualizar o
 * MapasCulturais, compare este arquivo com a nova versão oficial.
 */

$this->import('
   select-entity
   mc-accordion
   mc-avatar
   mc-confirm-button
   mc-icon
');

?>

<div v-if="entity.seals.length > 0 || editable" class="seals-certifier col-12">
    <mc-accordion :withText="true" open>
        <template #title>
            <h3><?= i::__('Selos certificadores') ?></h3>
        </template>
        <template #content>
            <h4 class="seals-certifier__title bold"> {{title}} <?php i::_e(' para proponentes') ?> </h4>
            <div class="seals-certifier__proponents">
                <div v-for="(seals, proponentType) in proponentSeals" :key="proponentType" class="seals-certifier__proponent field">
                    <label v-if="proponentType !== 'default'"><?php i::_e('Selecione o(s) selo(s) para {{ proponentType }}:') ?></label>
                    <label v-else><?php i::_e('Selecione o(s) selo(s) para todos proponentes:') ?></label>
                    <div class="seals-certifier__proponent--seals">
                        <div v-for="seal in seals" :key="seal.id" class="seals-certifier__proponent--seal">
                            <div class="seal-icon">
                                <a :href="getSealDetails(seal).singleUrl" class="link">
                                    <span class="sr-only">{{getSealDetails(seal).name}}</span>
                                    <div v-if="getSealDetails(seal).files?.avatar" class="image">
                                        <mc-avatar :entity="getSealDetails(seal)" size="small" square></mc-avatar>
                                    </div>
                                    <div v-if="!(getSealDetails(seal).files?.avatar)">
                                        <mc-icon name="seal"></mc-icon>
                                    </div>
                                </a>
                                <div v-if="editable" class="icon">
                                    <mc-confirm-button @confirm="removeSeal(proponentType, seal, 'proponent')">
                                        <template #button="modal">
                                            <button type="button" class="seals-certifier__remove" @click="modal.open()" aria-haspopup="dialog">
                                                <mc-icon name="delete" aria-hidden="true"></mc-icon>
                                                <span class="sr-only"><?= i::__('Remover selo') ?> {{getSealDetails(seal).name}}</span>
                                            </button>
                                        </template>
                                        <template #message="message">
                                            <?php i::_e('Remover selo?') ?>
                                        </template>
                                    </mc-confirm-button>
                                </div>
                            </div>
                            <span class="seal-label" v-if="showName">{{getSealDetails(seal).name}}</span>
                        </div>
                        <select-entity
                            :type="'seal'"
                            @select="addSeal(proponentType, $event, 'proponent')"
                            :query="getSealQuery(proponentType, 'proponent')"
                            openside="down-right"
                            >
                            <template #button="{ toggle }">
                                <button type="button" class="seals-certifier__proponent--addSeal" @click="toggle()" aria-haspopup="dialog">
                                    <mc-icon name="add" aria-hidden="true"></mc-icon>
                                    <span v-if="proponentType !== 'default'" class="sr-only"><?= i::__('Adicionar selo para') ?> {{proponentType}}</span>
                                    <span v-else class="sr-only"><?= i::__('Adicionar selo para todos proponentes') ?></span>
                                </button>
                            </template>
                        </select-entity>
                    </div>
                </div>
            </div>

            <h4 class="seals-certifier__title bold"> {{title}} <?php i::_e(' para categorias') ?> </h4>
            <div class="seals-certifier__categories">
                <div v-for="(seals, category) in categorySeals" :key="category" class="seals-certifier__category field">
                    <label v-if="category !== 'default'"><?php i::_e('Selecione o(s) selo(s) para {{ category }}:') ?></label>
                    <label v-else><?php i::_e('Selecione o(s) selo(s) para todas categorias:') ?></label>
                    <div class="seals-certifier__category--seals">
                        <div v-for="seal in seals" :key="seal.id" class="seals-certifier__category--seal">
                            <div class="seal-icon">
                                <a :href="getSealDetails(seal).singleUrl" class="link">
                                    <span class="sr-only">{{getSealDetails(seal).name}}</span>
                                    <div v-if="getSealDetails(seal).files?.avatar" class="image">
                                        <mc-avatar :entity="getSealDetails(seal)" size="small" square></mc-avatar>
                                    </div>
                                    <div v-if="!(getSealDetails(seal).files?.avatar)">
                                        <mc-icon name="seal"></mc-icon>
                                    </div>
                                </a>
                                <div v-if="editable" class="icon">
                                    <mc-confirm-button @confirm="removeSeal(category, seal, 'category')">
                                        <template #button="modal">
                                            <button type="button" class="seals-certifier__remove" @click="modal.open()" aria-haspopup="dialog">
                                                <mc-icon name="delete" aria-hidden="true"></mc-icon>
                                                <span class="sr-only"><?= i::__('Remover selo') ?> {{getSealDetails(seal).name}}</span>
                                            </button>
                                        </template>
                                        <template #message="message">
                                            <?php i::_e('Remover selo?') ?>
                                        </template>
                                    </mc-confirm-button>
                                </div>
                            </div>
                            <span class="seal-label" v-if="showName">{{getSealDetails(seal).name}}</span>
                        </div>
                        <select-entity
                            :type="'seal'"
                            @select="addSeal(category, $event, 'category')"
                            :query="getSealQuery(category, 'category')"
                            openside="down-right">
                            <template #button="{ toggle }">
                                <button type="button" class="seals-certifier__category--addSeal" @click="toggle()" aria-haspopup="dialog">
                                    <mc-icon name="add" aria-hidden="true"></mc-icon>
                                    <span v-if="category !== 'default'" class="sr-only"><?= i::__('Adicionar selo para') ?> {{category}}</span>
                                    <span v-else class="sr-only"><?= i::__('Adicionar selo para todas categorias') ?></span>
                                </button>
                            </template>
                        </select-entity>
                    </div>
                </div>
            </div>
        </template>
    </mc-accordion>
</div>
