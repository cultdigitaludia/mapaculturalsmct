<?php
/**
 * Sobrescrita do tema UberlandiaCultural baseada no componente oficial do
 * MapasCulturais v7.8.14. Diferença: o rótulo "Inicio", definido sem acento
 * nas views do núcleo, é exibido como "Início". Ao atualizar o MapasCulturais,
 * compare este arquivo com a nova versão oficial.
 */

use MapasCulturais\i;

$breadcrumb = $this->breadcrumb ?? [];
if ($breadcrumb) {
    $semAcento = [i::__('Inicio'), 'Inicio'];
    foreach ($breadcrumb as &$item) {
        if (isset($item['label']) && in_array($item['label'], $semAcento, true)) {
            $item['label'] = i::__('Início');
        }
    }
    unset($item);
    $this->jsObject['breadcrumb'] = $breadcrumb;
}
