<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
?>
<!DOCTYPE html>
<html lang="<?= $app->currentLCode ?>" dir="ltr">
    <head>
        <?php $this->applyTemplateHook('head','begin'); ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $this->printDocumentMeta(); ?>
        <title><?= $this->getTitle($entity ?? null) ?></title>
        <link rel="profile" href="//gmpg.org/xfn/11" />
        <link rel="icon" href="<?= $this->asset('img/culturese.svg', false) ?>" type="image/svg+xml">
        <link rel="apple-touch-icon" href="<?= $this->asset('img/favicon-180x180.png', false) ?>">

        <?php $this->printStyles('vendor-v2'); ?>
        <?php $this->printStyles('app-v2'); ?>

        <?php $this->printScripts('vendor-v2'); ?>
        <?php $this->printScripts('app-v2'); ?>

        <?php $this->applyTemplateHook('head','end'); ?>
    </head>

    <body <?php $this->bodyProperties() ?> style="opacity:0" >
        <?php $this->bodyBegin() ?>
<style>
.home-header {
    background: transparent !important;
}
.home-header__content::before {
    background: none !important;
}
.home-header__background .img > img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.home-header__content {
    min-height: 600px;
}
</style>
