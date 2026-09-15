<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */
$document_language = str_replace('_', '-', $app->currentLCode ?: 'pt-BR');
$document_title = trim((string) $this->getTitle($entity ?? null));
$request_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$route_titles = [
    '/autenticacao/register/' => 'Novo cadastro',
    '/search/turismo/' => 'Turismo',
    '/conta-e-privacidade/' => 'Conta e privacidade',
];
if (isset($route_titles[$request_path])) {
    $document_title = $route_titles[$request_path] . ' — ' . $app->siteName;
} elseif ($document_title === '') {
    $document_title = $app->siteName;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($document_language, ENT_QUOTES, 'UTF-8') ?>" dir="ltr">
    <head>
        <?php $this->applyTemplateHook('head','begin'); ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $this->printDocumentMeta(); ?>
        <title><?= htmlspecialchars($document_title, ENT_QUOTES, 'UTF-8') ?></title>
        <link rel="profile" href="//gmpg.org/xfn/11" />
        <link rel="icon" href="<?= $this->asset('img/culturese.svg', false) ?>" type="image/svg+xml">
        <link rel="apple-touch-icon" href="<?= $this->asset('img/favicon-180x180.png', false) ?>">

        <?php $this->printStyles('vendor-v2'); ?>
        <?php $this->printStyles('app-v2'); ?>

        <?php $this->printScripts('vendor-v2'); ?>
        <?php $this->printScripts('app-v2'); ?>

        <?php $this->applyTemplateHook('head','end'); ?>
    </head>

    <body <?php $this->bodyProperties() ?>>
        <?php $this->bodyBegin() ?>
