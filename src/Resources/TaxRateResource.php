<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Moneybird;

class TaxRateResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(array $filters = []): array
    {
        // @phpstan-ignore-next-line
        $taxRate = $this->client->taxRate();

        if (! empty($filters)) {
            // @phpstan-ignore-next-line
            return $taxRate->filter($filters);
        }

        return $taxRate->get();
    }

    public function find(string $id)
    {
        // @phpstan-ignore-next-line
        $taxRate = $this->client->taxRate();
        // @phpstan-ignore-next-line
        $taxRate->id = $id;

        // @phpstan-ignore-next-line
        return $taxRate->find($id);
    }
}

