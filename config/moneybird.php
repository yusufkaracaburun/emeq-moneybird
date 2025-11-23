<?php

return [
    'oauth' => [
        // @phpstan-ignore-next-line
        'client_id' => env('MONEYBIRD_CLIENT_ID'),
        // @phpstan-ignore-next-line
        'client_secret' => env('MONEYBIRD_CLIENT_SECRET'),
        // @phpstan-ignore-next-line
        'redirect_uri' => env('MONEYBIRD_REDIRECT_URI'),
        'scopes' => [
            'sales_invoices',
            'documents',
            'estimates',
            'bank',
            'time_entries',
            'settings',
        ],
    ],
    'api' => [
        'base_url' => 'https://moneybird.com/api/v2',
        'timeout' => 30,
    ],
    'webhook' => [
        // @phpstan-ignore-next-line
        'secret' => env('MONEYBIRD_WEBHOOK_SECRET'),
        'route' => '/moneybird/webhook',
    ],
];
