<?php

use Emeq\Moneybird\Models\MoneybirdConnection;

it('can create a moneybird connection', function () {
    $connection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => now()->addHour(),
        'is_active'         => true,
    ]);

    expect($connection)->toBeInstanceOf(MoneybirdConnection::class)
        ->and($connection->access_token)->toBe('test_token')
        ->and($connection->is_active)->toBeTrue();
});

it('can check if connection is expired', function () {
    $expiredConnection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'expires_at'        => now()->subHour(),
        'is_active'         => true,
    ]);

    $activeConnection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'expires_at'        => now()->addHour(),
        'is_active'         => true,
    ]);

    expect($expiredConnection->isExpired())->toBeTrue()
        ->and($activeConnection->isExpired())->toBeFalse();
});

it('can check if connection needs refresh', function () {
    $connectionNeedingRefresh = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => now()->subMinutes(10),
        'is_active'         => true,
    ]);

    $connectionNotNeedingRefresh = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => now()->addHour(),
        'is_active'         => true,
    ]);

    expect($connectionNeedingRefresh->needsRefresh())->toBeTrue()
        ->and($connectionNotNeedingRefresh->needsRefresh())->toBeFalse();
});

it('returns false when refresh token is missing', function () {
    $connection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => null,
        'expires_at'        => now()->subHour(),
        'is_active'         => true,
    ]);

    expect($connection->needsRefresh())->toBeFalse();
});

it('returns false when expires_at is missing', function () {
    // Since expires_at is required, we test the method behavior with a connection that has expires_at
    // but the method checks for null internally
    $connection = new MoneybirdConnection([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => null,
        'is_active'         => true,
    ]);

    expect($connection->needsRefresh())->toBeFalse();
});

it('returns false when expires_at is not past', function () {
    $connection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => now()->addHour(),
        'is_active'         => true,
    ]);

    expect($connection->needsRefresh())->toBeFalse();
});

it('returns true when expires_at is within 5 minutes', function () {
    $connection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => now()->addMinutes(3),
        'is_active'         => true,
    ]);

    expect($connection->needsRefresh())->toBeTrue();
});

it('returns false when expires_at is null', function () {
    // Since expires_at is required, we test the method behavior with a connection that has expires_at
    // but the method checks for null internally
    $connection = new MoneybirdConnection([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'refresh_token'     => 'test_refresh_token',
        'expires_at'        => null,
        'is_active'         => true,
    ]);

    expect($connection->isExpired())->toBeFalse();
});

it('can access user relationship', function () {
    $connection = MoneybirdConnection::create([
        'user_id'           => 1,
        'name'              => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token'      => 'test_token',
        'expires_at'        => now()->addHour(),
        'is_active'         => true,
    ]);

    $relation = $connection->user();

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
