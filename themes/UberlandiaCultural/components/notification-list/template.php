<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-card
    mc-entities
    mc-icon
');
?>
<mc-entities type="notification" name="notification-list" :query="query" #default="{entities, refresh}">
    <mc-card :class="['notification-card', styleCss]" v-for="entity in entities" :key="entity.__objectId">
        <div class="avatar">
            <img v-if="hasAvatar(entity)" :src="avatarUrl(entity)">
            <mc-icon v-if="!hasAvatar(entity)" name="agent-1"></mc-icon>
        </div>
        <div class="content">
            <div class="content__header">
                <span class="title" v-html="messageWithoutEdit(entity.message)"></span>
                <span class="subtitle">{{ entity.createTimestamp?.date('numeric year') }} - {{ entity.createTimestamp?.time() }}</span>
            </div>

            <div class="content__groupButtons content__groupButtons--notification" v-if="!entity.request">
                <a v-if="editUrl(entity.message)" :href="editUrl(entity.message)" class="button button--primary-outline notification-card__edit-action">
                    <?= i::__('Editar') ?>
                </a>
                <button class="button button--primary notification-card__ok-action" @click="ok(entity)">
                    <?= i::__('Ok') ?>
                </button>
            </div>

            <div class="content__groupButtons" v-else-if="entity.request?.requesterUser?.id === currentUserId">
                <div class="col-2">
                    <button class="button button--primary-outline" @click="cancel(entity); refresh();"><?= i::__('Cancelar') ?></button>
                </div>
                <div class="col-2">
                    <button class="button button--primary" @click="ok(entity)"><?= i::__('Ok') ?></button>
                </div>
            </div>

            <div class="content__groupButtons" v-else-if="entity.request?.requesterUser?.id !== currentUserId">
                <div class="col-2">
                    <button class="button button--primary-outline" @click="reject(entity); refresh();"><?= i::__('Rejeitar') ?></button>
                </div>
                <div class="col-2">
                    <button class="button button--primary" @click="approve(entity); refresh();"><?= i::__('Aceitar') ?></button>
                </div>
            </div>
        </div>
    </mc-card>
</mc-entities>
