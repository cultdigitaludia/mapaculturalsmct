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
            'type' => [1000,1001,1002,1003,1006,1007,1008,1010,1011,1016,1017,1018,1019,13,14,20,21,22,23,24,25,26,30,31,40,41,60,61,80,81,82,84,85,100,101,109,113,120,121,123,128,130,135,300,301,302,303,400,401,402,403,601,602,603,604,700,701,702,703,800,801,802,803,804,805,806,807,808,809,810]
        ];
        $app->applyHookBoundTo($this, 'search-turismo-initial-pseudo-query', [&$initial_pseudo_query]);
        $this->render('turismo', ['initial_pseudo_query' => $initial_pseudo_query]);
    }
}