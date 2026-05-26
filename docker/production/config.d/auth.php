<?php
return [
    'auth.provider' => '\MultipleLocalAuth\Provider',
    'auth.config' => [
        'salt' => env('AUTH_SALT', null),
        'timeout' => '24 hours',
        'strategies' => [
            'Google' => [
                'visible' => true,
                'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
                'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
                'redirect_uri' => '/autenticacao/google/oauth2callback',
                'scope' => 'email profile',
            ],
        ]
    ]
];
