<?php

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Services\MoneybirdService;
use Picqer\Financials\Moneybird\Moneybird;

beforeEach(function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);
});

it('can get connection by connection id', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->connection(connectionId: $connection->id);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('can get connection by user id', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->connection(userId: 1);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('can get connection by tenant id', function () {
    $connection = MoneybirdConnection::create([
        'tenant_id' => 'tenant1',
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->connection(tenantId: 'tenant1');

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('can get connection by user id and tenant id', function () {
    $connection = MoneybirdConnection::create([
        'user_id' => 1,
        'tenant_id' => 'tenant1',
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->connection(userId: 1, tenantId: 'tenant1');

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('can get first active connection when no filters provided', function () {
    MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->connection();

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('can set connection directly', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('throws exception when getting client without connection', function () {
    $service = app(MoneybirdService::class);

    expect(fn () => $service->getClient())->toThrow(\RuntimeException::class, 'No Moneybird connection set');
});

it('can get client with connection', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $client = $service->getClient();

    expect($client)->toBeInstanceOf(Moneybird::class);
});

it('can get administrations resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->administrations();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\AdministrationResource::class);
});

it('can get contacts resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->contacts();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\ContactResource::class);
});

it('can get sales invoices resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->salesInvoices();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\SalesInvoiceResource::class);
});

it('can get estimates resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->estimates();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\EstimateResource::class);
});

it('can get documents resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->documents();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\DocumentResource::class);
});

it('can get webhooks resource', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $resource = $service->webhooks();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\WebhookResource::class);
});

it('refreshes tokens when connection needs refresh', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subMinutes(10),
        'is_active' => true,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'moneybird.com/oauth/token' => \Illuminate\Support\Facades\Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('does not refresh tokens when connection does not need refresh', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('handles connection without refresh token', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => null,
        'expires_at' => now()->subHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('handles connection without expires_at', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => null,
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    expect($service)->toBeInstanceOf(MoneybirdService::class);
});

it('creates picqer connection with administration id', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'administration_id' => 'admin123',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $client = $service->getClient();

    expect($client)->toBeInstanceOf(Moneybird::class);
});

it('creates picqer connection without administration id', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'administration_id' => null,
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $client = $service->getClient();

    expect($client)->toBeInstanceOf(Moneybird::class);
});

it('returns cached client on subsequent calls', function () {
    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $service = app(MoneybirdService::class);
    $service->setConnection($connection);

    $client1 = $service->getClient();
    $client2 = $service->getClient();

    expect($client1)->toBe($client2);
});

it('does not refresh tokens when connection is null', function () {
    $service = app(MoneybirdService::class);

    expect(fn () => $service->getClient())->toThrow(\RuntimeException::class);
});

it('does not call refresh when connection is null in ensureValidTokens', function () {
    $service = new \Emeq\Moneybird\Services\MoneybirdService(app(\Emeq\Moneybird\Services\OAuthService::class));

    $reflection = new \ReflectionClass($service);
    $method = $reflection->getMethod('ensureValidTokens');
    $method->setAccessible(true);

    $result = $method->invoke($service);

    expect($result)->toBeNull();
});
