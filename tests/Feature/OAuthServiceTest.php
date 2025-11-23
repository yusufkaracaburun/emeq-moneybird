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

    $oauthService = new OAuthService;
    $connection = $oauthService->exchangeCodeForTokens('auth_code', 1, 'tenant1');

    expect($connection)->toBeInstanceOf(MoneybirdConnection::class)
        ->and($connection->access_token)->toBe('new_access_token')
        ->and($connection->refresh_token)->toBe('new_refresh_token')
        ->and($connection->user_id)->toBe(1)
        ->and($connection->tenant_id)->toBe('tenant1');
});

it('throws exception when token exchange fails', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $oauthService = new OAuthService;

    expect(fn () => $oauthService->exchangeCodeForTokens('invalid_code'))
        ->toThrow(\RuntimeException::class);
});

it('can refresh tokens', function () {
    $connection = MoneybirdConnection::create([
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

    $oauthService = new OAuthService;
    $connection = $oauthService->exchangeCodeForTokens('auth_code');

    expect($connection->expires_at)->not->toBeNull();
});
