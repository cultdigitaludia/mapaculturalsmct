<?php

return [
    'plugins' => [
        'MultipleLocalAuth' => [ 'namespace' => 'MultipleLocalAuth' ],
        'SamplePlugin' => ['namespace' => 'SamplePlugin'],
        'SpamDetector',
        'AccountStatus' => [
            'namespace' => 'AccountStatus',
                "config" => [
                    'inactive_seal_id' => env('USR_STATUS_INACTIVE_SEAL_ID', 5),
                    'inactive_period' => env('USR_STATUS_INACTIVE_PERIOD', '-1 year'),
                    'updated_seal_id' => env('USR_STATUS_UPDATED_SEAL_ID', 4),
                    'update_expiration_period' => env('USR_STATUS_LAST_UPDATE', '+1 year'),
                    'update_fields' => [
                        'name',
                        'shortDescription',
                        ],
                ]
            ]
        ]
];