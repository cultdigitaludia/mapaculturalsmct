<?php
namespace Search;
use MapasCulturais\App;
class Controller extends \Search\Controller
{
    function GET_index() {
        header('Location: /search/turismo');
        exit;
    }

    function GET_turismo() {
        $app = App::i();
        $initial_pseudo_query = [
            'type' => [1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019]
        ];
        $app->applyHookBoundTo($this, 'search-turismo-initial-pseudo-query', [&$initial_pseudo_query]);
        $this->render('turismo', ['initial_pseudo_query' => $initial_pseudo_query]);
    }
}
