<?php

use Emeq\Moneybird\Events\ContactCreated;
use Emeq\Moneybird\Events\ContactUpdated;
use Emeq\Moneybird\Events\EstimateCreated;
use Emeq\Moneybird\Events\SalesInvoiceCreated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('moneybird.webhook.secret', 'test_secret');
    Event::fake();
});

it('can handle webhook request', function () {
    $payload = [
        'event' => 'sales_invoice.created',
        'data' => ['id' => '123'],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test_secret');

    $response = $this->postJson('/moneybird/webhook', $payload, [
        'X-Moneybird-Signature' => $signature,
    ]);

    $response->assertStatus(200)
        ->assertSeeText('OK');

    Event::assertDispatched(SalesInvoiceCreated::class);
});

it('validates webhook signature', function () {
    $payload = [
        'event' => 'sales_invoice.created',
        'data' => ['id' => '123'],
    ];

    $response = $this->postJson('/moneybird/webhook', $payload, [
        'X-Moneybird-Signature' => 'invalid_signature',
    ]);

    $response->assertStatus(500);
});

it('handles missing event type', function () {
    $payload = ['data' => ['id' => '123']];

    $response = $this->postJson('/moneybird/webhook', $payload);

    $response->assertStatus(400);
});

it('handles webhook without secret configured', function () {
    config()->set('moneybird.webhook.secret', null);

    $payload = [
        'event' => 'unknown.event',
        'data' => ['id' => '123'],
    ];

    $response = $this->postJson('/moneybird/webhook', $payload);

    $response->assertStatus(200);
    Event::assertDispatched(\Emeq\Moneybird\Events\MoneybirdWebhookReceived::class);
});

it('dispatches specific event for contact.created', function () {
    $payload = [
        'event' => 'contact.created',
        'data' => ['id' => '123'],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test_secret');

    $response = $this->postJson('/moneybird/webhook', $payload, [
        'X-Moneybird-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    Event::assertDispatched(ContactCreated::class);
});

it('dispatches specific event for contact.updated', function () {
    $payload = [
        'event' => 'contact.updated',
        'data' => ['id' => '123'],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test_secret');

    $response = $this->postJson('/moneybird/webhook', $payload, [
        'X-Moneybird-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    Event::assertDispatched(ContactUpdated::class);
});

it('dispatches generic event for unknown event type', function () {
    $payload = [
        'event' => 'unknown.event',
        'data' => ['id' => '123'],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test_secret');

    $response = $this->postJson('/moneybird/webhook', $payload, [
        'X-Moneybird-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    Event::assertDispatched(\Emeq\Moneybird\Events\MoneybirdWebhookReceived::class);
});

it('handles missing signature header when secret is configured', function () {
    $payload = [
        'event' => 'sales_invoice.created',
        'data' => ['id' => '123'],
    ];

    $response = $this->postJson('/moneybird/webhook', $payload);

    $response->assertStatus(500);
});

