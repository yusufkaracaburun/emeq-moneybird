<?php

return [
    'oauth' => [
        // @phpstan-ignore-next-line
        'client_id' => env('MONEYBIRD_CLIENT_ID', null),
        // @phpstan-ignore-next-line
        'client_secret' => env('MONEYBIRD_CLIENT_SECRET', null),
        // @phpstan-ignore-next-line
        'redirect_uri' => env('MONEYBIRD_REDIRECT_URI', null),
        'scopes' => [
            'sales_invoices',
            'bank',
            'time_entries',
            'settings',
        ],
    ],
    'api' => [
        'base_url' => 'https://moneybird.com/api/v2',
        // @phpstan-ignore-next-line
        'timeout' => env('MONEYBIRD_API_TIMEOUT', 30),
        // @phpstan-ignore-next-line
        'retry_attempts' => env('MONEYBIRD_API_RETRY_ATTEMPTS', 3),
        // @phpstan-ignore-next-line
        'retry_delay' => env('MONEYBIRD_API_RETRY_DELAY', 1), // seconds
    ],
    'webhook' => [
        // @phpstan-ignore-next-line
        'secret' => env('MONEYBIRD_WEBHOOK_SECRET', null),
        'route' => '/moneybird/webhook',
    ],
];
