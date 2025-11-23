<?php

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Services\MoneybirdService;
use Picqer\Financials\Moneybird\Moneybird;

beforeEach(function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);

    $connection = MoneybirdConnection::create([
        'access_token' => 'test_token',
        'expires_at' => now()->addHour(),
        'is_active' => true,
    ]);

    $this->service = app(MoneybirdService::class);
    $this->service->setConnection($connection);
});

it('can get administrations resource', function () {
    $resource = $this->service->administrations();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\AdministrationResource::class);
});

it('can get contacts resource', function () {
    $resource = $this->service->contacts();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\ContactResource::class);
});

it('can get sales invoices resource', function () {
    $resource = $this->service->salesInvoices();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\SalesInvoiceResource::class);
});

it('can get estimates resource', function () {
    $resource = $this->service->estimates();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\EstimateResource::class);
});

it('can get documents resource', function () {
    $resource = $this->service->documents();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\DocumentResource::class);
});

it('can get webhooks resource', function () {
    $resource = $this->service->webhooks();

    expect($resource)->toBeInstanceOf(\Emeq\Moneybird\Resources\WebhookResource::class);
});

