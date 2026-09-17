<?php
use MapasCulturais\i;
?>
<div class="accessibility-menu" data-accessibility-menu>
    <button
        class="accessibility-menu__trigger mc-header-menu--item accessibility"
        type="button"
        aria-expanded="false"
        title="<?= i::esc_attr_e('Abrir menu de acessibilidade') ?>">
        <span class="accessibility-menu__symbol icon" aria-hidden="true">Aa</span>
        <span class="accessibility-menu__trigger-label label"><?= i::__('Acessibilidade') ?></span>
    </button>

    <section
        class="accessibility-menu__panel"
        hidden>
        <div class="accessibility-menu__header">
            <h2><?= i::__('Acessibilidade') ?></h2>
            <button class="accessibility-menu__close" type="button" aria-label="<?= i::esc_attr_e('Fechar menu de acessibilidade') ?>">&times;</button>
        </div>

        <div class="accessibility-menu__controls">
            <button type="button" data-a11y-action="font-increase" aria-label="<?= i::esc_attr_e('Aumentar fonte') ?>" title="<?= i::esc_attr_e('Aumentar fonte') ?>"><span aria-hidden="true">A+</span></button>
            <button type="button" data-a11y-action="font-decrease" aria-label="<?= i::esc_attr_e('Diminuir fonte') ?>" title="<?= i::esc_attr_e('Diminuir fonte') ?>"><span aria-hidden="true">A−</span></button>
            <button type="button" data-a11y-action="contrast" aria-label="<?= i::esc_attr_e('Alto contraste') ?>" title="<?= i::esc_attr_e('Alto contraste') ?>" aria-pressed="false"><span class="accessibility-menu__contrast-icon" aria-hidden="true"></span></button>
            <button type="button" data-a11y-action="links" aria-label="<?= i::esc_attr_e('Destacar links') ?>" title="<?= i::esc_attr_e('Destacar links') ?>" aria-pressed="false"><span class="accessibility-menu__links-icon" aria-hidden="true">A</span></button>
            <button type="button" data-a11y-action="reset" aria-label="<?= i::esc_attr_e('Restaurar padrão') ?>" title="<?= i::esc_attr_e('Restaurar padrão') ?>"><span aria-hidden="true">↺</span></button>
        </div>

        <output class="accessibility-menu__status" aria-live="polite" aria-atomic="true"></output>
    </section>
</div>
