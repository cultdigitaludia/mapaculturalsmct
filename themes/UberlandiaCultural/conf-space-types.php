<?php
return [
    'items' => [
        \MapasCulturais\i::__('Turismo — Tipos de Espaços') => [
            'range' => [1, 1099],
            'items' => [
                // ── Categorias exclusivas de Turismo ──────────────────────────
                1000 => ['name' => \MapasCulturais\i::__('Pontos-Espaços')],
                1001 => ['name' => \MapasCulturais\i::__('Gastronomia')],
                1002 => ['name' => \MapasCulturais\i::__('Estrutura Esportiva')],
                1003 => ['name' => \MapasCulturais\i::__('Religiões de Matriz Africana')],
                1006 => ['name' => \MapasCulturais\i::__('Shoppings')],
                1007 => ['name' => \MapasCulturais\i::__('Camelodromos')],
                1008 => ['name' => \MapasCulturais\i::__('Centros Comerciais')],        // removido "e Galerias"
                1010 => ['name' => \MapasCulturais\i::__('Atrativos Naturais')],
                1011 => ['name' => \MapasCulturais\i::__('Clubes Esportivos')],         // removido "Sociais e"
                1016 => ['name' => \MapasCulturais\i::__('Equipamentos Culturais')],
                1017 => ['name' => \MapasCulturais\i::__('Segurança Pública')],
                1018 => ['name' => \MapasCulturais\i::__('Meios de Hospedagem')],
                1019 => ['name' => \MapasCulturais\i::__('Agências')],

                // ── Espaços de Exibição de Filmes ─────────────────────────────
                13  => ['name' => \MapasCulturais\i::__('Espaço Público Para Projeção de Filmes')],
                14  => ['name' => \MapasCulturais\i::__('Sala de cinema')],

                // ── Bibliotecas ───────────────────────────────────────────────
                20  => ['name' => \MapasCulturais\i::__('Biblioteca Pública')],
                21  => ['name' => \MapasCulturais\i::__('Biblioteca Privada')],
                22  => ['name' => \MapasCulturais\i::__('Biblioteca Comunitária (incluídos os pontos de leitura)')],
                23  => ['name' => \MapasCulturais\i::__('Biblioteca Escolar')],
                24  => ['name' => \MapasCulturais\i::__('Biblioteca Nacional')],
                25  => ['name' => \MapasCulturais\i::__('Biblioteca Universitária')],
                26  => ['name' => \MapasCulturais\i::__('Biblioteca Especializada')],

                // ── Teatros ───────────────────────────────────────────────────
                30  => ['name' => \MapasCulturais\i::__('Teatro Público')],
                31  => ['name' => \MapasCulturais\i::__('Teatro Privado')],

                // ── Centros Culturais ─────────────────────────────────────────
                40  => ['name' => \MapasCulturais\i::__('Centro Cultural Público')],
                41  => ['name' => \MapasCulturais\i::__('Centro Cultural Privado')],

                // ── Museus ────────────────────────────────────────────────────
                60  => ['name' => \MapasCulturais\i::__('Museu Público')],
                61  => ['name' => \MapasCulturais\i::__('Museu Privado')],

                // ── Espaços Religiosos ────────────────────────────────────────
                80  => ['name' => \MapasCulturais\i::__('Templo')],
                81  => ['name' => \MapasCulturais\i::__('Terreiro')],
                82  => ['name' => \MapasCulturais\i::__('Mesquitas')],
                84  => ['name' => \MapasCulturais\i::__('Igreja')],
                85  => ['name' => \MapasCulturais\i::__('Centro Espírita')],

                // ── Demais Equipamentos Culturais ─────────────────────────────
                100 => ['name' => \MapasCulturais\i::__('Galeria de arte')],
                101 => ['name' => \MapasCulturais\i::__('Livraria')],
                109 => ['name' => \MapasCulturais\i::__('Danceteria')],
                113 => ['name' => \MapasCulturais\i::__('Espaço para Eventos')],
                120 => ['name' => \MapasCulturais\i::__('Espaço Mais Cultura')],
                121 => ['name' => \MapasCulturais\i::__('Sala de dança')],
                123 => ['name' => \MapasCulturais\i::__('Espaço para apresentação de dança')],
                128 => ['name' => \MapasCulturais\i::__('Clube social')],
                130 => ['name' => \MapasCulturais\i::__('Sala Multiuso')],
                135 => ['name' => \MapasCulturais\i::__('Sala de Leitura')],

                // ── Instituições Públicas de Ensino Regular ───────────────────
                300 => ['name' => \MapasCulturais\i::__('Instituição Pública de Ensino Regular Federal')],
                301 => ['name' => \MapasCulturais\i::__('Instituição Pública de Ensino Regular Estadual')],
                302 => ['name' => \MapasCulturais\i::__('Instituição Pública de Ensino Regular Municipal')],
                303 => ['name' => \MapasCulturais\i::__('Instituição Pública de Ensino Regular Distrital')],

                // ── Instituições Privadas de Ensino Regular ───────────────────
                400 => ['name' => \MapasCulturais\i::__('Instituição Privada Particular')],
                401 => ['name' => \MapasCulturais\i::__('Instituição Privada Comunitária')],
                402 => ['name' => \MapasCulturais\i::__('Instituição Privada Confessional')],
                403 => ['name' => \MapasCulturais\i::__('Instituição Privada Filantrópica')],

                // ── Instituições Públicas exclusivamente para formação artística
                601 => ['name' => \MapasCulturais\i::__('Instituição Pública Federal exclusivamente voltada para formação artistica e cultural')],
                602 => ['name' => \MapasCulturais\i::__('Instituição Pública Estadual exclusivamente voltada para formação artistica e cultural')],
                603 => ['name' => \MapasCulturais\i::__('Instituição Pública Municipal exclusivamente voltada para formação artistica e cultural')],
                604 => ['name' => \MapasCulturais\i::__('Instituição Pública Distrital exclusivamente voltada para formação artistica e cultural')],

                // ── Instituições Privadas exclusivamente para formação artística
                700 => ['name' => \MapasCulturais\i::__('Instituição Privada Particular exclusivamente voltada para formação artistica e cultural')],
                701 => ['name' => \MapasCulturais\i::__('Instituição Privada Comunitária exclusivamente voltada para formação artistica e cultural')],
                702 => ['name' => \MapasCulturais\i::__('Instituição Privada Confessional exclusivamente voltada para formação artistica e cultural')],
                703 => ['name' => \MapasCulturais\i::__('Instituição Privada Filantrópica exclusivamente voltada para formação artistica e cultural')],

                // ── Escolas Livres ────────────────────────────────────────────
                800 => ['name' => \MapasCulturais\i::__('Escola livre de Artes Cênicas')],
                801 => ['name' => \MapasCulturais\i::__('Escola livre de Artes Visuais')],
                802 => ['name' => \MapasCulturais\i::__('Escola livre de Audiovisual')],
                803 => ['name' => \MapasCulturais\i::__('Escola livre de Hip Hop')],
                804 => ['name' => \MapasCulturais\i::__('Escola livre de Cultura Digital')],
                805 => ['name' => \MapasCulturais\i::__('Escola livre de Música')],
                806 => ['name' => \MapasCulturais\i::__('Escola livre de Cultura Popular')],
                807 => ['name' => \MapasCulturais\i::__('Escola livre de Gestão Cultural')],
                808 => ['name' => \MapasCulturais\i::__('Escola livre de Pontinhos de cultura')],
                809 => ['name' => \MapasCulturais\i::__('Escola livre de Patrimônio')],
                810 => ['name' => \MapasCulturais\i::__('Escola livre de Design')],
            ],
        ],
    ],
];