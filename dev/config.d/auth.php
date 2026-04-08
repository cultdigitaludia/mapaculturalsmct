<?php

return [
<<<<<<< HEAD
    // 'auth.provider' => 'Fake',
    'auth.provider' => '\MultipleLocalAuth\Provider',
    'auth.config' => [
        'salt' => env('AUTH_SALT', null),
        'timeout' => '24 hours',
        'strategies' => [
            // 'Facebook' => [
            //     'app_id' => env('AUTH_FACEBOOK_APP_ID', null),
            //     'app_secret' => env('AUTH_FACEBOOK_APP_SECRET', null),
            //     'scope' => env('AUTH_FACEBOOK_SCOPE', 'email'),
            // ],
            // 'Google' => [
            //     'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
            //     'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
            //     'redirect_uri' => '/autenticacao/google/oauth2callback',
            //     'scope' => env('AUTH_GOOGLE_SCOPE', 'email'),
            // ],
        ]
    ]
];
=======
    'auth.provider' => '\MultipleLocalAuth\Provider',

    'auth.config' => [
        'salt' => env('AUTH_SALT', 'Q8Gvys50EhkoOY3TRRbNcvO153ziS6Kk'),
        'timeout' => '24 hours',

        'strategies' => [
            // --- ATIVOS AGORA (Login Local) ---
            'email' => [
                'provider' => 'Email',
                'label' => 'Email',
            ],
            'cpf' => [
                'provider' => 'CPF',
                'label' => 'CPF',
            ],

            // --- FUTURO (Redes Sociais) ---
            // Mantenha comentado até ter o domínio final e as chaves de API.
            
            // 'Facebook' => [
            //     'app_id'     => env('AUTH_FACEBOOK_APP_ID'),
            //     'app_secret' => env('AUTH_FACEBOOK_APP_SECRET'),
            //     'scope'      => env('AUTH_FACEBOOK_SCOPE', 'email'),
            // ],

            // 'Google' => [
            //     'client_id'     => env('AUTH_GOOGLE_CLIENT_ID'),
            //     'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET'),
            //     'redirect_uri'  => env('AUTH_GOOGLE_REDIRECT_URI', '/autenticacao/google/oauth2callback'),
            //     'scope'         => env('AUTH_GOOGLE_SCOPE', 'email'),
            // ],

            // 'LinkedIn' => [
            //     'api_key'      => env('AUTH_LINKEDIN_API_KEY'),
            //     'secret_key'   => env('AUTH_LINKEDIN_SECRET_KEY'),
            //     'redirect_uri' => env('AUTH_LINKEDIN_REDIRECT_URI', '/autenticacao/linkedin/oauth2callback'),
            //     'scope'        => env('AUTH_LINKEDIN_SCOPE', 'r_emailaddress'),
            // ],
        ]
    ]
];
>>>>>>> 3d441c89c04294375654a3c7b387e8d980527d30
