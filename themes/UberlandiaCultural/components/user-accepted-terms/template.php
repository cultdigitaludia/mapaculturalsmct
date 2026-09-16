<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;
?>
<?php $this->applyTemplateHook('user-accepted-terms', 'before'); ?>

<div class="user-accepted-terms__privacy">
    <?php $this->applyTemplateHook('user-accepted-terms', 'begin'); ?>
    <div class="user-accepted-terms__privacy--accept">
        <div class="user-accepted-terms__privacy--accept-title"><?= i::__('Aceite de termos') ?></div>
        <div v-if="user" class="user-accepted-terms__privacy--accept-title-box">
            <div class="boxterm">
                <div v-for="(term, slug) in terms" class="boxterm__list">
                    <div v-if="user['lgpd_'+ slug]?.[term.md5]" class="boxterm__list-entry">
                        <div class="boxterm__list-subterm">
                            <div class="boxterm__list-subterm-title"><?= i::__('{{term.title}}') ?></div>
                            <div class="boxterm__list-subterm-content">
                                <span><?= i::__('aceito em {{formatDate(user["lgpd_"+slug][term.md5].timestamp)}}') ?></span>
                                <span><?= i::__('pelo ip {{user["lgpd_"+slug][term.md5].ip}}') ?></span>
                            </div>
                        </div>

                        <a v-if="slug === 'termsOfUsage'"
                            class="boxterm__list-action"
                            href="<?= $app->createUrl('lgpd', 'view', ['termsOfUsage']) ?>"
                            target="_blank"
                            rel="noopener noreferrer">
                            <?= i::__('Consultar termo') ?>
                        </a>
                        <a v-else-if="slug === 'privacyPolicy'"
                            class="boxterm__list-action"
                            href="<?= $app->createUrl('lgpd', 'view', ['privacyPolicy']) ?>"
                            target="_blank"
                            rel="noopener noreferrer">
                            <?= i::__('Consultar termo') ?>
                        </a>
                        <a v-else-if="slug === 'imageUsageAuthorization'"
                            class="boxterm__list-action"
                            href="<?= $app->createUrl('lgpd', 'view', ['imageUsageAuthorization']) ?>"
                            target="_blank"
                            rel="noopener noreferrer">
                            <?= i::__('Consultar termo') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->applyTemplateHook('user-accepted-terms', 'end'); ?>
</div>

<?php $this->applyTemplateHook('user-accepted-terms', 'after'); ?>
