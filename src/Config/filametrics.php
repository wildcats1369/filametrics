<?php

return [
    'default_provider' => 'google',
    'providers' => [
        'google' => [
            'view_id' => env('GOOGLE_VIEW_ID', ''),
            'service_account_credentials_json' => env('GOOGLE_SERVICE_ACCOUNT_JSON', ''),
        ],
    ],
];
