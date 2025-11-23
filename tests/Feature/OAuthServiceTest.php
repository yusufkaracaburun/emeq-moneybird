<?php

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Services\OAuthService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);
});

it('can exchange authorization code for tokens', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    // Mock the Moneybird client to avoid real API calls
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([
        (object) ['id' => 'admin123', 'name' => 'Test Administration'],
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $oauthService = \Mockery::mock(\Emeq\Moneybird\Services\OAuthService::class)->makePartial();
    $oauthService->shouldAllowMockingProtectedMethods();
    $oauthService->shouldReceive('createMoneybirdClient')
        ->once()
        ->andReturn($mockMoneybird);

    $connection = $oauthService->exchangeCodeForTokens('auth_code', 1, 'tenant1');

    expect($connection)->toBeInstanceOf(MoneybirdConnection::class)
        ->and($connection->access_token)->toBe('new_access_token')
        ->and($connection->refresh_token)->toBe('new_refresh_token')
        ->and($connection->user_id)->toBe(1)
        ->and($connection->tenant_id)->toBe('tenant1');
});

it('can create moneybird client', function () {
    $oauthService = new OAuthService;
    
    $connection = new \Picqer\Financials\Moneybird\Connection;
    $connection->setClientId('test_client_id');
    $connection->setClientSecret('test_secret');
    $connection->setRedirectUrl('https://example.com/callback');
    $connection->setAccessToken('test_token');

    $reflection = new \ReflectionClass($oauthService);
    $method = $reflection->getMethod('createMoneybirdClient');
    $method->setAccessible(true);

    $client = $method->invoke($oauthService, $connection);

    expect($client)->toBeInstanceOf(\Picqer\Financials\Moneybird\Moneybird::class);
});

it('throws exception when token exchange fails', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->exchangeCodeForTokens('invalid_code', 1))
        ->toThrow(\Emeq\Moneybird\Exceptions\MoneybirdException::class);
});

it('throws exception when user id is missing', function () {
    $oauthService = new OAuthService;

    expect(fn () => $oauthService->exchangeCodeForTokens('code', 0))
        ->toThrow(\Emeq\Moneybird\Exceptions\MoneybirdException::class, 'User ID is required');
});

it('throws connection error exception when connection fails during token exchange', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
    });

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->exchangeCodeForTokens('code', 1))
        ->toThrow(\Emeq\Moneybird\Exceptions\ConnectionErrorException::class);
});

it('throws exception when no administrations found', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([]);

    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $oauthService = \Mockery::mock(\Emeq\Moneybird\Services\OAuthService::class)->makePartial();
    $oauthService->shouldAllowMockingProtectedMethods();
    $oauthService->shouldReceive('createMoneybirdClient')
        ->once()
        ->andReturn($mockMoneybird);

    expect(fn () => $oauthService->exchangeCodeForTokens('code', 1))
        ->toThrow(\Emeq\Moneybird\Exceptions\MoneybirdException::class, 'No administrations found');
});

it('throws exception when specified administration not found', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([
        (object) ['id' => 'admin123', 'name' => 'Test Administration'],
    ]);

    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $oauthService = \Mockery::mock(\Emeq\Moneybird\Services\OAuthService::class)->makePartial();
    $oauthService->shouldAllowMockingProtectedMethods();
    $oauthService->shouldReceive('createMoneybirdClient')
        ->once()
        ->andReturn($mockMoneybird);

    expect(fn () => $oauthService->exchangeCodeForTokens('code', 1, null, 'nonexistent'))
        ->toThrow(\Emeq\Moneybird\Exceptions\MoneybirdException::class, 'Administration with ID nonexistent not found');
});

it('throws connection error exception when connection fails during token refresh', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
    });

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->refreshTokens($connection))
        ->toThrow(\Emeq\Moneybird\Exceptions\ConnectionErrorException::class);
});

it('can refresh tokens', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $oauthService = new OAuthService;
    $refreshed = $oauthService->refreshTokens($connection);

    expect($refreshed->access_token)->toBe('new_access_token')
        ->and($refreshed->refresh_token)->toBe('new_refresh_token');
});

it('throws exception when refresh token is missing', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => null,
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->refreshTokens($connection))
        ->toThrow(\RuntimeException::class, 'No refresh token available');
});

it('throws exception when token refresh fails', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'invalid_refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    Http::fake([
        'moneybird.com/oauth/token' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->refreshTokens($connection))
        ->toThrow(\RuntimeException::class, 'Failed to refresh tokens');
});

it('handles missing refresh token in response', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'name' => 'Test Connection',
        'administration_id' => 'admin123',
        'access_token' => 'old_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $oauthService = new OAuthService;
    $refreshed = $oauthService->refreshTokens($connection);

    expect($refreshed->access_token)->toBe('new_access_token')
        ->and($refreshed->refresh_token)->toBe('refresh_token');
});

it('handles missing expires_in in token response', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
        ], 200),
    ]);

    // Mock the Moneybird client to avoid real API calls
    $mockAdministration = \Mockery::mock();
    $mockAdministration->shouldReceive('get')->andReturn([
        (object) ['id' => 'admin123', 'name' => 'Test Administration'],
    ]);

    $mockMoneybird = \Mockery::mock(\Picqer\Financials\Moneybird\Moneybird::class);
    $mockMoneybird->shouldReceive('administration')->andReturn($mockAdministration);

    $oauthService = \Mockery::mock(\Emeq\Moneybird\Services\OAuthService::class)->makePartial();
    $oauthService->shouldAllowMockingProtectedMethods();
    $oauthService->shouldReceive('createMoneybirdClient')
        ->once()
        ->andReturn($mockMoneybird);

    $connection = $oauthService->exchangeCodeForTokens('auth_code', 1);

    expect($connection->expires_at)->not->toBeNull();
});
