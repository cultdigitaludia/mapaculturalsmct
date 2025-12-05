<?php
namespace TemaSMCT;

class Theme extends \BaseV1\Theme {

    static function getThemeFolder() {
        return __DIR__;
    }

    protected function _init() {
        parent::_init();
        // Configurações adicionais virão aqui no futuro
    }

}
