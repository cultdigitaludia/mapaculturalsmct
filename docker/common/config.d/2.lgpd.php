<?php

use MapasCulturais\i;

return [
    'module.LGPD' => [
        'termsOfUsage' => [
            'title' => 'Termos e Condições de Uso do Mapa Cultural e Turístico de Uberlândia',
            'text' => file_get_contents(__DIR__ . '/lgpd-terms/terms-of-usage.html'),
            'buttonText' => i::__('Li e aceito os Termos de Uso'),
        ],
        'privacyPolicy' => [
            'title' => 'Política de Privacidade do Mapa Cultural e Turístico de Uberlândia',
            'text' => file_get_contents(__DIR__ . '/lgpd-terms/privacy-policy.html'),
            'buttonText' => i::__('Li e aceito a Política de Privacidade'),
        ],
        'imageUsageAuthorization' => [
            'title' => 'Autorização de Uso de Imagem do Mapa Cultural e Turístico de Uberlândia',
            'text' => file_get_contents(__DIR__ . '/lgpd-terms/image-usage-authorization.html'),
            'buttonText' => i::__('Li e autorizo o uso de imagem'),
        ],
    ],
];
