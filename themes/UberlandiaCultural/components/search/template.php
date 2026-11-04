<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 *
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: abaixo do cabeçalho das listagens
 * (Turismo, Oportunidades, Agentes, Eventos, Espaços e Projetos) entra o texto
 * de apresentação da seção, definido em Theme::getSectionDescriptions(). Ao
 * atualizar o MapasCulturais, compare este arquivo com a nova versão oficial.
 */

$this->import('
    mc-breadcrumb
    mc-title
');

$sections = [
    'turismo' => 'turismo',
    'opportunities' => 'opportunity',
    'agents' => 'agent',
    'events' => 'event',
    'spaces' => 'space',
    'projects' => 'project',
];
$section = $sections[$this->controller->action ?? ''] ?? null;
$description = $section ? ($this->getSectionDescriptions()[$section] ?? null) : null;
?>
<div class="search">
    <mc-breadcrumb></mc-breadcrumb>

    <header class="search__header">
        <div class="search__header--content">
            <div class="search__header--content-left">
                <div :class="['search__header--content-left-icon', entityType+'__background']">
                    <mc-icon :name="entityType"></mc-icon>
                </div>

                <h1 class="mc-title mc-title--short bold"> {{pageTitle}} </h1>
            </div>

            <div class="search__header--content--right">
                <slot name="create-button"></slot>
            </div>
        </div>

        <?php if ($description): ?>
            <div class="search__description">
                <p class="search__description--text"><?= htmlspecialchars($description['text']) ?></p>
                <?php if (!empty($description['tip'])): ?>
                    <p class="search__description--tip">
                        <?= htmlspecialchars($description['tip']) ?>
                        <a href="<?= htmlspecialchars($description['link']['url']) ?>"><?= htmlspecialchars($description['link']['label']) ?></a>.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </header>

    <slot :pseudo-query="pseudoQuery" :changeTab="changeTab"></slot>
</div>
