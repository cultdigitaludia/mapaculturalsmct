<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
?>
        <?php $this->bodyEnd() ?>
    <script src="<?= $app->baseUrl ?>assets/js/geolocalizacao.js?v=<?= time() ?>"></script>
    </body>
    <?php $this->applyTemplateHook('body','after'); ?>
    <?php $this->printJsObject(); ?>
</html>