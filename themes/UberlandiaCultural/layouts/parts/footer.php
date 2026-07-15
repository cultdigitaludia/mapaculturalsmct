<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
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