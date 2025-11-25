<?php

namespace Emeq\Moneybird\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Emeq\Moneybird\Events\ContactCreated;
use Emeq\Moneybird\Events\ContactDeleted;
use Emeq\Moneybird\Events\ContactUpdated;
use Emeq\Moneybird\Events\SalesInvoiceCreated;
use Emeq\Moneybird\Events\SalesInvoiceDeleted;
use Emeq\Moneybird\Events\SalesInvoiceUpdated;
use Emeq\Moneybird\Events\MoneybirdWebhookReceived;

class WebhookController
{
    /**
     * Handle incoming Moneybird webhook.
     */
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

    /**
     * Validate webhook signature.
     */
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

    /**
     * Dispatch webhook event.
     */
    protected function dispatchWebhookEvent(string $eventType, array $payload): void
    {
        $eventClass = $this->getEventClass($eventType);

        if ($eventClass && class_exists($eventClass)) {
            event(new $eventClass($payload));
        } else {
            event(new MoneybirdWebhookReceived($eventType, $payload));
        }
    }

    /**
     * Get event class for event type.
     */
    protected function getEventClass(string $eventType): ?string
    {
        $eventMap = [
            'sales_invoice.created' => SalesInvoiceCreated::class,
            'sales_invoice.updated' => SalesInvoiceUpdated::class,
            'sales_invoice.deleted' => SalesInvoiceDeleted::class,
            'contact.created' => ContactCreated::class,
            'contact.updated' => ContactUpdated::class,
            'contact.deleted' => ContactDeleted::class,
        ];

        return $eventMap[$eventType] ?? null;
    }
}
