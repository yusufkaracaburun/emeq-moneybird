<?php

use Emeq\Moneybird\Models\MoneybirdConnection;

beforeEach(function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);
});

it('can test connection command with connection id', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([
        (object) ['id' => 'admin1', 'name' => 'Test Admin'],
    ]);

    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $this->app->bind(\Emeq\Moneybird\Services\MoneybirdService::class, function () use ($mockMoneybird) {
        $service = \Mockery::mock(\Emeq\Moneybird\Services\MoneybirdService::class)->makePartial();
        $service->shouldReceive('connection')->andReturnSelf();
        $service->shouldReceive('administrations')->andReturn(
            new \Emeq\Moneybird\Resources\AdministrationResource($mockMoneybird)
        );

        return $service;
    });

    $this->artisan('moneybird:test-connection', ['--connection-id' => $connection->id])
        ->expectsOutput('Connection successful!')
        ->assertSuccessful();
});

it('handles refresh tokens command error when no connection id or all option', function () {
    $this->artisan('moneybird:refresh-tokens')
        ->expectsOutput('Either --connection-id or --all option is required')
        ->assertFailed();
});

it('handles refresh tokens command when no connections found', function () {
    $this->artisan('moneybird:refresh-tokens', ['--all' => true])
        ->expectsOutput('No active connections with refresh tokens found')
        ->assertSuccessful();
});

it('handles refresh tokens command with failure', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'moneybird.com/oauth/token' => \Illuminate\Support\Facades\Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $this->artisan('moneybird:refresh-tokens', ['--connection-id' => $connection->id])
        ->expectsOutputToContain('Failed to refresh tokens')
        ->assertFailed();
});

it('handles refresh all tokens command with some failures', function () {
    $connection1 = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token1',
        'refresh_token' => 'refresh_token1',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    $connection2 = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token2',
        'refresh_token' => 'refresh_token2',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'moneybird.com/oauth/token' => \Illuminate\Support\Facades\Http::sequence()
            ->push(['access_token' => 'new_access_token', 'refresh_token' => 'new_refresh_token', 'expires_in' => 3600], 200)
            ->push(['error' => 'invalid_grant'], 400),
    ]);

    $this->artisan('moneybird:refresh-tokens', ['--all' => true])
        ->expectsOutput("✓ Connection {$connection1->id} refreshed successfully")
        ->expectsOutputToContain("✗ Connection {$connection2->id} failed:")
        ->assertFailed();
});

it('handles test connection command failure', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $this->artisan('moneybird:test-connection', ['--connection-id' => $connection->id])
        ->assertFailed();
});

it('can test connection command without options', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([
        (object) ['id' => 'admin1', 'name' => 'Test Admin'],
    ]);

    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $this->app->bind(\Emeq\Moneybird\Services\MoneybirdService::class, function () use ($mockMoneybird) {
        $service = \Mockery::mock(\Emeq\Moneybird\Services\MoneybirdService::class)->makePartial();
        $service->shouldReceive('connection')->andReturnSelf();
        $service->shouldReceive('administrations')->andReturn(
            new \Emeq\Moneybird\Resources\AdministrationResource($mockMoneybird)
        );

        return $service;
    });

    $this->artisan('moneybird:test-connection')
        ->expectsOutput('Connection successful!')
        ->assertSuccessful();
});

it('can refresh tokens command for specific connection', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'moneybird.com/oauth/token' => \Illuminate\Support\Facades\Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $this->artisan('moneybird:refresh-tokens', ['--connection-id' => $connection->id])
        ->expectsOutput("Successfully refreshed tokens for connection {$connection->id}")
        ->assertSuccessful();
});

it('can refresh all tokens command', function () {
    $connection1 = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token1',
        'refresh_token' => 'refresh_token1',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    $connection2 = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token2',
        'refresh_token' => 'refresh_token2',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'moneybird.com/oauth/token' => \Illuminate\Support\Facades\Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $this->artisan('moneybird:refresh-tokens', ['--all' => true])
        ->assertSuccessful();
});
