<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-popover
    panel--nav
    theme-logo
    user-profile-avatar
');

$profile_name = trim((string) ($app->user?->profile?->name ?: i::__('Minha conta')));
$user_email = trim((string) ($app->user?->email ?? ''));

// iniciais das duas primeiras palavras com mais de duas letras (ex.: "Secretaria Municipal..." -> "SM")
$name_words = array_values(array_filter(preg_split('/\s+/u', $profile_name), fn($word) => mb_strlen($word) > 2));
$initials = implode('', array_map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)), array_slice($name_words, 0, 2)));
$initials = $initials ?: mb_strtoupper(mb_substr($profile_name, 0, 1));

$account_label = sprintf(i::__('Abrir menu da conta de %s'), $profile_name);
$escape = fn($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<?php $this->applyTemplateHook('header-menu-user', 'before') ?>
<div class="mc-header-menu-user">
    <?php $this->applyTemplateHook('header-menu-user', 'begin') ?>
    <!-- Menu desktop -->
    <?php $this->applyTemplateHook('header-menu-user--desktop', 'before'); ?>
    <mc-popover openside="down-left" classes="user-menu-popover">
        <template #button="{ toggle }">
        <div class="mc-header-menu-user__desktop">
            <button type="button" class="user" @mousedown.stop @click="toggle()" aria-label="<?= $escape($account_label) ?>" title="<?= $escape($profile_name) ?>">
                <span class="user__avatar">
                    <user-profile-avatar v-if="global.auth.user?.profile?.files?.avatar"></user-profile-avatar>
                    <span v-else class="user__initials" aria-hidden="true"><span v-pre><?= $escape($initials) ?></span></span>
                </span>
            </button>
        </div>
        </template>
        <template #default="popover">
            <?php $this->applyTemplateHook('header-menu-user--desktop', 'before') ?>
            <panel--nav classes="user-menu">
                <template #begin>
                    <?php $this->applyTemplateHook('header-menu-user--desktop', 'begin') ?>
                    <div class="user-menu__account">
                        <strong class="user-menu__account-name"><span v-pre><?= $escape($profile_name) ?></span></strong>
                        <?php if ($user_email): ?>
                            <span class="user-menu__account-email"><span v-pre><?= $escape($user_email) ?></span></span>
                        <?php endif; ?>
                    </div>
                    <ul>
                        <?php $this->applyTemplateHook('header-menu-user--itens', 'begin') ?>


                        <?php $this->applyTemplateHook('header-menu-user--itens', 'end') ?>
                    </ul>
                </template>

                <template #end>
                    <div class="user-menu__line"></div>
                    <li><mc-link :entity='profile' icon><?= i::__('Meu Perfil') ?></mc-link></li>
                    <li><mc-link route='auth/logout' icon="logout"><?= i::__('Sair') ?></mc-link></li>
                    <?php $this->applyTemplateHook('header-menu-user--desktop', 'end') ?>
                </template>
            </panel--nav>
        </template>
    </mc-popover>
    <?php $this->applyTemplateHook('header-menu-user--desktop', 'after'); ?>

    <!-- Menu mobile -->
    <?php $this->applyTemplateHook('header-menu-user--mobile', 'before'); ?>
    <div class="mc-header-menu-user__mobile">
        <?php $this->applyTemplateHook('header-menu-user--mobile', 'begin'); ?>
        <div class="mc-header-menu-user__mobile--button">
            <a href="#main-app" class="user" @click="toggleMobile()" aria-label="<?= $escape($account_label) ?>">
                <div class="user__name">
                    <?= i::_e('Minha conta') ?>
                </div>
                <div class="user__avatar">
                    <user-profile-avatar></user-profile-avatar>
                </div>
            </a>
        </div>
        <div v-if="open" class="mc-header-menu-user__mobile--list">
            <div class="close">
                <theme-logo href="<?= $app->createUrl('site', 'index') ?>"></theme-logo>
                <a class="close__btn" href="#main-app" @click="toggleMobile()">
                    <mc-icon name="close"></mc-icon>
                </a>
            </div>
            <?php $this->applyTemplateHook('header-menu-user--mobile', 'before') ?>
            <panel--nav>
                <template #begin>

                    <?php $this->applyTemplateHook('header-menu-user--mobile', 'begin') ?>
                </template>

                <template #end>
                    <mc-link :entity='profile' icon><label><?= i::__('Meu Perfil') ?></label></mc-link>
                    <mc-link route='auth/logout' icon="logout"><?= i::__('Sair') ?></mc-link>
                    <?php $this->applyTemplateHook('header-menu-user--mobile', 'end') ?>
                </template>
            </panel--nav>
            <?php $this->applyTemplateHook('header-menu-user--mobile', 'after') ?>
        </div>
        <?php $this->applyTemplateHook('header-menu-user--mobile', 'end'); ?>
    </div>
    <?php $this->applyTemplateHook('header-menu-user', 'end') ?>
</div>
<?php $this->applyTemplateHook('header-menu-user', 'after') ?>
