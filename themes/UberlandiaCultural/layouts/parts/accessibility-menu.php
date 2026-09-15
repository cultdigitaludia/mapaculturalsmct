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
            <h2><?= i::__('Recursos de acessibilidade') ?></h2>
            <button class="accessibility-menu__close" type="button" aria-label="<?= i::esc_attr_e('Fechar menu de acessibilidade') ?>">&times;</button>
        </div>

        <div class="accessibility-menu__controls">
            <button type="button" data-a11y-action="font-increase"><?= i::__('Aumentar fonte') ?></button>
            <button type="button" data-a11y-action="font-decrease"><?= i::__('Diminuir fonte') ?></button>
            <button type="button" data-a11y-action="contrast" aria-pressed="false"><?= i::__('Alto contraste') ?></button>
            <button type="button" data-a11y-action="links" aria-pressed="false"><?= i::__('Destacar links') ?></button>
            <button type="button" data-a11y-action="reset"><?= i::__('Restaurar padrão') ?></button>
        </div>

        <output class="accessibility-menu__status" aria-live="polite" aria-atomic="true"></output>
    </section>
</div>
