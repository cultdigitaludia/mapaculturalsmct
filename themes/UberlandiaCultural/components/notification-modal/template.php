<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-link
    mc-popover
    notification-list
');
?>
<mc-popover v-if="notificationsCount>0" classes="notification-modal" title="<?= i::esc_attr_e('Notificações') ?>" openside="down-right">
    <template #default>
        <div style="max-width: 500px;">
            <div class="notification-modal__header">
                <div v-if="notificationsCount>0">
                    <span class="count">
                        <?= i::__('Você tem') ?>
                        <span class="count-counter">{{ notificationsCount }}</span>
                        <span v-if="Number(notificationsCount) === 1"><?= i::__('notificação') ?></span>
                        <span v-else><?= i::__('notificações') ?></span>
                    </span>
                </div>
            </div>
            <div class="grid-12 notification-modal__content" style="max-height: 700px; overflow-y:auto">
                <div class="col-12 notifications">
                    <notification-list></notification-list>
                </div>
            </div>
            <div class="notification-modal__action">
                <div class="link" v-if="notificationsCount">
                    <mc-link route="panel/index" hash="notifications"><?= i::__('Ver todas as notificações') ?></mc-link>
                </div>
            </div>
        </div>
    </template>

    <template #button="popover">
        <button v-if="viewport=='desktop'" type="button" class="notification-modal__menu-desk" @click="popover.toggle()" title="<?= i::esc_attr_e('Notificações') ?>" aria-label="<?= i::esc_attr_e('Abrir notificações') ?>">
            <span class="sr-only"><?= i::__('Notificações') ?></span>
            <div class="icon">
                <mc-icon name="notification"></mc-icon>
                <span v-if="notificationsCount>0" class="count">{{notificationsCount}}</span>
            </div>
        </button>

        <a v-if="viewport=='mobile'" class="notification-modal__menu-mobile" @click="popover.toggle()">
            <div class="icon">
                <mc-icon name="notification"></mc-icon>
                <span v-if="notificationsCount>0" class="count">{{notificationsCount}}</span>
            </div>
            <label class="label"><?= i::__('Notificações') ?></label>
        </a>
    </template>
</mc-popover>
