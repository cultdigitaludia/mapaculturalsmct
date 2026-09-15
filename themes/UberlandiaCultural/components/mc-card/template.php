<?php
/**
 * Mantém a estrutura visual do componente oficial sem criar landmarks main
 * aninhados dentro do conteúdo principal da página.
 */
?>
<component :is="tag" class="mc-card" :class="classes">
    <header v-if="hasSlot('title')" class="mc-card__title">
        <slot name="title"></slot>
    </header>
    <div class="mc-card__content">
        <slot></slot>
        <slot name="content"></slot>
    </div>
</component>
