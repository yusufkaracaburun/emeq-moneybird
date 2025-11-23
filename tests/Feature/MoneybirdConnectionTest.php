<?php

use Emeq\Moneybird\Models\MoneybirdConnection;

it('can create a moneybird connection', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    expect($connection)->toBeInstanceOf(MoneybirdConnection::class)
        ->and($connection->access_token)->toBe('test_token')
        ->and($connection->is_active)->toBeTrue();
});

it('can check if connection is expired', function () {
    $expiredConnection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    $activeConnection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    expect($expiredConnection->isExpired())->toBeTrue()
        ->and($activeConnection->isExpired())->toBeFalse();
});

it('can check if connection needs refresh', function () {
    $connectionNeedingRefresh = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subMinutes(10),
        'is_active' => true,
    ]);

    $connectionNotNeedingRefresh = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    expect($connectionNeedingRefresh->needsRefresh())->toBeTrue()
        ->and($connectionNotNeedingRefresh->needsRefresh())->toBeFalse();
});
