<?php

use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);
});

it('can connect with authorization code', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $this->artisan('moneybird:connect')
        ->expectsOutput('Starting Moneybird OAuth connection...')
        ->expectsQuestion('Enter the authorization code from the callback URL', 'test_code')
        ->expectsOutput('Successfully connected to Moneybird!')
        ->assertSuccessful();
});

it('handles missing authorization code', function () {
    $this->artisan('moneybird:connect')
        ->expectsOutput('Starting Moneybird OAuth connection...')
        ->expectsQuestion('Enter the authorization code from the callback URL', '')
        ->expectsOutput('Authorization code is required')
        ->assertFailed();
});

it('handles connection failure', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $this->artisan('moneybird:connect')
        ->expectsOutput('Starting Moneybird OAuth connection...')
        ->expectsQuestion('Enter the authorization code from the callback URL', 'invalid_code')
        ->assertFailed();
});

it('can connect with user id option', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $this->artisan('moneybird:connect', ['--user-id' => '1'])
        ->expectsQuestion('Enter the authorization code from the callback URL', 'test_code')
        ->assertSuccessful();
});

it('can connect with tenant id option', function () {
    Http::fake([
        'moneybird.com/oauth/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $this->artisan('moneybird:connect', ['--tenant-id' => 'tenant1'])
        ->expectsQuestion('Enter the authorization code from the callback URL', 'test_code')
        ->assertSuccessful();
});
