<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\Webhook;
use Picqer\Financials\Moneybird\Moneybird;

class WebhookResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(): array
    {
        $webhook = $this->client->webhook();

        return $webhook->get();
    }

    public function create(array $attributes): Webhook
    {
        $webhook = $this->client->webhook($attributes);
        $webhook->save();

        return $webhook;
    }

    public function delete(string $id): bool
    {
        $webhook = $this->client->webhook();
        // @phpstan-ignore-next-line
        $webhook->id = $id;

        return $webhook->delete();
    }
}
