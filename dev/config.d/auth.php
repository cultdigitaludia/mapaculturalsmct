<?php
// Define a URL base do seu site. Isto irá construir a URL completa dinamicamente.
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);

return [
    'auth.provider' => '\MultipleLocalAuth\Provider',

    'auth.config' => [
        'salt' => env('AUTH_SALT', 'mude-este-valor-para-algo-aleatorio-e-muito-longo'),

        // NOVO: Diz explicitamente quais logins sociais devem aparecer.
        'enabled_strategies' => [
            'Google' => true,
            // 'Facebook' => true, // Pode descomentar isto mais tarde se configurar o Facebook
        ],
        
        // Estratégias de autenticação social com as suas chaves
        'strategies' => [
            'Google' => [
                'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
                'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
                'redirect_uri' => 'http://localhost/autenticacao/google/oauth2callback',
                'scope' => 'email profile'
            ],
            
            // 'Facebook' => [
            //     'app_id' => env('AUTH_FACEBOOK_APP_ID', null),
            //     'app_secret' => env('AUTH_FACEBOOK_APP_SECRET', null), 
            //     'scope' => 'email'
            // ],
        ],

        // Todas as outras configurações que já tinha (regras de senha, etc.)
        'timeout' => '24 hours',
        'enableLoginByCPF' => true,
        'passwordMustHaveCapitalLetters' => true,
        'passwordMustHaveLowercaseLetters' => true,
        'passwordMustHaveSpecialCharacters' => true,
        'passwordMustHaveNumbers' => true,
        'minimumPasswordLength' => 6,
        'sessionTime' => 7200,
        'numberloginAttemp' => '5', 
        'timeBlockedloginAttemp' => '900',
    ]
];