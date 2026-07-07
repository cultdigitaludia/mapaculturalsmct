<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
use MapasCulturais\i;

$this->import('
    theme-logo
');
$config = $app->config['social-media'];
?>
<?php $this->applyTemplateHook("main-footer", "before")?>
<div v-if="globalState.visibleFooter" class="main-footer">
    <?php $this->applyTemplateHook("main-footer", "begin")?>
    <div class="main-footer__content">
        <?php $this->applyTemplateHook("main-footer-logo", "before")?>
        <div class="main-footer__support">
            <?php $this->part('footer-support-message') ?>
        </div>
        <div class="main-footer__content--logo">
            <div class="main-footer__content--logo-img">
                <?php if ($app->config['logo.footer.image']): ?>
                    <a href="<?= $app->createUrl('site', 'index') ?>" title="<?= $app->siteName ?>">
                        <img 
                            src="<?= $this->asset($app->config['logo.footer.image'], false) ?>" 
                            alt="<?= $app->siteName ?>"
                            style="max-height: 80px; width: auto; max-width: 100%;"
                        />
                    </a>
                <?php else: ?>
                    <theme-logo href="<?= $app->createUrl('site', 'index') ?>"></theme-logo>
                <?php endif; ?>
            </div>
            <div class="main-footer__content--logo-share">
                <?php foreach ($config as $meta => $conf) : ?>
                    <a target="_blank" href="<?= $conf['link'] ?>">
                        <mc-icon name='<?= $conf['icon'] ?>'></mc-icon>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php $this->applyTemplateHook("main-footer-logo", "after")?>

        <?php $this->applyTemplateHook("main-footer-links", "before")?>
        <div class="main-footer__content--links">
            <?php $this->applyTemplateHook("main-footer-links", "begin")?>

            <ul class="main-footer__content--links-group">
                <li>
                    <a><?php i::_e("Acesse"); ?></a>
                </li>
                <li>
                    <a href="<?= $app->createUrl('search', 'Turismo') ?>">
                        <mc-icon name="space"></mc-icon> <?php i::_e('Turismo'); ?>
                    </a>
                </li>
                <li v-if="global.enabledEntities.opportunities">
                    <a href="<?= $app->createUrl('search', 'opportunities') ?>">
                        <mc-icon name="opportunity"></mc-icon> <?= $this->text('main-footer', i::__('Editais e Oportunidades')); ?>
                    </a>
                </li>
                <li v-if="global.enabledEntities.events">
                    <a href="<?= $app->createUrl('search', 'events') ?>">
                        <mc-icon name="event"></mc-icon> <?php i::_e('Eventos'); ?>
                    </a>
                </li>
                <li v-if="global.enabledEntities.agents">
                    <a href="<?= $app->createUrl('search', 'agents') ?>">
                        <mc-icon name="agent"></mc-icon> <?php i::_e('Agentes'); ?>
                    </a>
                </li>
                <li v-if="global.enabledEntities.spaces">
                    <a href="<?= $app->createUrl('search', 'spaces') ?>">
                        <mc-icon name="space"></mc-icon> <?php i::_e('Espaços'); ?>
                    </a>
                </li>
                <li v-if="global.enabledEntities.projects">
                    <a href="<?= $app->createUrl('search', 'projects') ?>">
                        <mc-icon name="project"></mc-icon> <?php i::_e('Projetos'); ?>
                    </a>
                </li>
            </ul>

            <ul class="main-footer__content--links-group">
                <li>
                    <a href="<?= $app->createUrl('panel', 'index') ?>"><?php i::_e('Painel'); ?></a>
                </li>
                <li v-if="global.enabledEntities.opportunities">
                    <a href="<?= $app->createUrl('panel', 'opportunities') ?>"><?php i::_e('Editais e Oportunidades'); ?></a>
                </li>
                <li v-if="global.enabledEntities.events">
                    <a href="<?= $app->createUrl('panel', 'events') ?>"><?php i::_e('Meus Eventos'); ?></a>
                </li>
                <li v-if="global.enabledEntities.agents">
                    <a href="<?= $app->createUrl('panel', 'agents') ?>"><?php i::_e('Meus Agentes'); ?></a>
                </li>
                <li v-if="global.enabledEntities.spaces">
                    <a href="<?= $app->createUrl('panel', 'spaces') ?>"><?php i::_e('Meus Espaços'); ?></a>
                </li>
                <?php if (!($app->user->is('guest'))) : ?>
                    <li>
                        <a href="<?= $app->createUrl('auth', 'logout') ?>"><?php i::_e('Sair')?></a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="main-footer__content--links-group">
                <li>
                    <a><?php i::_e('Ajuda e Privacidade'); ?></a>
                </li>
                <li>
                    <a href="<?= $app->createUrl('faq') ?>"><?php i::_e('Dúvidas Frequentes'); ?></a>
                </li>
                <li>
                    <p style="margin: 4px 0 12px 0; font-size: 0.85em; line-height: 1.4;">
                        <?php i::_e('Dúvidas ou problemas com o sistema entrar em contato pelo e-mail'); ?>
                        <a href="mailto:suporte.mapasculturais@uberlandia.mg.gov.br" style="display: block; margin-top: 4px;">
                            suporte.mapasculturais@uberlandia.mg.gov.br
                        </a>
                    </p>
                </li>
                <li>
                    <a href="<?= $app->createUrl('page', 'site', ['termos-de-uso']) ?>"><?php i::_e('Termos e Condições de Uso'); ?></a>
                </li>
                <li>
                    <a href="<?= $app->createUrl('page', 'site', ['politica-de-privacidade']) ?>"><?php i::_e('Política de Privacidade'); ?></a>
                </li>
                <li>
                    <a href="<?= $app->createUrl('page', 'site', ['autorizacao-de-uso-de-imagem']) ?>"><?php i::_e('Autorização de Uso de Imagem'); ?></a>
                </li>
            </ul>

            <?php $this->applyTemplateHook("main-footer-links", "end")?>
        </div>
        <?php $this->applyTemplateHook("main-footer-links", "after")?>
    </div>

    <?php $this->applyTemplateHook("main-footer-reg", "before")?>
    <div class="main-footer__reg">
        <?php $this->applyTemplateHook("main-footer-reg", "begin")?>
        <div class="main-footer__reg-content">
            <?php $this->part('main-footer/developed-by') ?>

            <a class="link" href="https://github.com/cultdigitaludia/mapaculturalsmct">
                <?php i::_e("Conheça o repositório") ?>
                <mc-icon name="github"></mc-icon>
            </a>
        </div>
        <?php $this->applyTemplateHook("main-footer-reg", "end")?>
    </div>
    <?php $this->applyTemplateHook("main-footer-reg", "after")?>
    <?php $this->applyTemplateHook("main-footer", "end")?>
</div>
<?php $this->applyTemplateHook("main-footer", "after")?>