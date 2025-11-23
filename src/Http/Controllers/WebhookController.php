<?php

namespace Emeq\Moneybird\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController
{
    public function handle(Request $request): Response
    {
        $payload = $request->all();
        $eventType = $payload['event'] ?? null;

        if (! $eventType) {
            return response('Missing event type', 400);
        }

        $this->validateWebhook($request);

        $this->dispatchWebhookEvent($eventType, $payload);

        return response('OK', 200);
    }

    protected function validateWebhook(Request $request): void
    {
        $secret = config('moneybird.webhook.secret');

        if (! $secret) {
            Log::warning('Moneybird webhook secret not configured, skipping validation');

            return;
        }

        $signature = $request->header('X-Moneybird-Signature');

        if (! $signature) {
            throw new \RuntimeException('Missing webhook signature');
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new \RuntimeException('Invalid webhook signature');
        }
    }

    protected function dispatchWebhookEvent(string $eventType, array $payload): void
    {
        $eventClass = $this->getEventClass($eventType);

        if ($eventClass && class_exists($eventClass)) {
            event(new $eventClass($payload));
        } else {
            event(new \Emeq\Moneybird\Events\MoneybirdWebhookReceived($eventType, $payload));
        }
    }

    protected function getEventClass(string $eventType): ?string
    {
        $eventMap = [
            'sales_invoice.created' => \Emeq\Moneybird\Events\SalesInvoiceCreated::class,
            'sales_invoice.updated' => \Emeq\Moneybird\Events\SalesInvoiceUpdated::class,
            'sales_invoice.deleted' => \Emeq\Moneybird\Events\SalesInvoiceDeleted::class,
            'contact.created' => \Emeq\Moneybird\Events\ContactCreated::class,
            'contact.updated' => \Emeq\Moneybird\Events\ContactUpdated::class,
            'contact.deleted' => \Emeq\Moneybird\Events\ContactDeleted::class,
            'estimate.created' => \Emeq\Moneybird\Events\EstimateCreated::class,
            'estimate.updated' => \Emeq\Moneybird\Events\EstimateUpdated::class,
            'estimate.deleted' => \Emeq\Moneybird\Events\EstimateDeleted::class,
        ];

        return $eventMap[$eventType] ?? null;
    }
}
