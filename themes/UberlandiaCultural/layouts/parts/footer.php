<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

// As ferramentas embutidas (BaseV1EmbedTools) renderizam com o tema BaseV1, mas
// resolvem as partes pelo caminho do tema ativo. Este rodapé é do BaseV2: ele
// declararia novamente o objeto MapasCulturais e omitiria os templates da V1.
if (!$this instanceof \MapasCulturais\Themes\BaseV2\Theme) {
    include \MapasCulturais\Themes\BaseV1\Theme::getThemeFolder() . '/layouts/parts/footer.php';
    return;
}
?>
        <?php $this->bodyEnd() ?>
        <div vw class="enabled">
            <div vw-access-button class="active"></div>
            <div vw-plugin-wrapper>
                <div class="vw-plugin-top-wrapper"></div>
            </div>
        </div>
    </body>
    <?php $this->applyTemplateHook('body','after'); ?>
    <?php $this->printJsObject(); ?>
</html>