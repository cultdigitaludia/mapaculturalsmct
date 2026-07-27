<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\i;

$this->import('
    mc-modal
');
?>
<div class="change-password">
    <label class="change-password__title"> <?= i::__('Senha:') ?> </label>

    <div class="change-password__password">
        <div class="change-password__password--fakePassword">
            <div v-for="n in 12" class="dot"></div>
        </div>

        <mc-modal title="<?= i::esc_attr__('Alteração de senha') ?>" classes="change-password__modal">
            <template #default>
                <form class="grid-12" @submit.prevent="changePassword(modal);">

                    <div v-if="myAccount" class="field col-12 password change-password__current-password">
                        <label for="currentPassword"> <?= i::__('Senha atual'); ?> </label>
                        <input autocomplete="off" id="currentPassword" type="password" name="currentPassword" v-model="currentPassword" />
                        <div class="seePassword" @click="togglePassword('currentPassword', $event)"></div>
                    </div>

                    <div class="change-password__new-password col-12" role="group" aria-labelledby="new-password-title">
                        <div class="change-password__new-password-divider">
                            <h3 id="new-password-title"><?= i::__('Nova senha'); ?></h3>
                        </div>

                        <div class="field password">
                            <label for="newPassword"> <?= i::__('Nova senha'); ?> </label>
                            <input autocomplete="off" id="newPassword" type="password" name="newPassword" v-model="newPassword" />
                            <div class="seePassword" @click="togglePassword('newPassword', $event)"></div>
                        </div>

                        <div class="field password">
                            <label for="confirmNewPassword"> <?= i::__('Confirme a nova senha'); ?> </label>
                            <input autocomplete="off" id="confirmNewPassword" type="password" name="confirmNewPassword" v-model="confirmNewPassword" />
                            <div class="seePassword" @click="togglePassword('confirmNewPassword', $event)"></div>
                            <p v-if="passwordRulesLoaded && !allPasswordCriteriaMet" class="password-requirements" aria-live="polite">
                                <strong><?= i::__('A senha deve conter no mínimo:'); ?></strong>
                                <span>{{pendingPasswordRequirementsText}}.</span>
                            </p>
                        </div>
                    </div>
                </form>                

            </template>

            <template #button="modal">
                <a class="change-password__password--edit" @click="modal.open()">
                    <mc-icon name="edit"></mc-icon> 
                    <label class="label"> <?= i::__('Alterar senha') ?> </label>
                </a>
            </template>     

            <template #actions="modal">
                <button class="button button--primary" @click="changePassword(modal)"> <?= i::__('Alterar senha') ?> </button>
                <button class="button button--text button--text-del" @click="cancel(modal)"><?= i::__("Cancelar") ?></button>
            </template>
        </mc-modal>
    </div>
</div>
