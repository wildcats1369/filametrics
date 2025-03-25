<?php
// config/filametrics.php
return [
    'default_provider' => 'google',
    'providers' => [
        'select' => ['google' => 'Google Analytics'],
        "vendors" => [
            'google' => [
                'view_id' => env('GOOGLE_VIEW_ID', ''),
                'service_account_credentials_json' => env('GOOGLE_SERVICE_ACCOUNT_JSON', ''),
            ],
        ],
    ],
    'subform' => [
        'google' => [
            'view_id' => "text",
            'service_account_credentials_json' => "upload",
        ],
    ],

    'cache_lifetime_in_minutes' => 60 * 24,

    'cache' => [
        'store' => 'redis',
    ],

];
