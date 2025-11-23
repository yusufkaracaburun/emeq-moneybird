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
        $taxRate = $this->client->taxRate();

        if (! empty($filters)) {
            return $taxRate->filter($filters);
        }

        return $taxRate->get();
    }

    public function find(string $id)
    {
        $taxRate = $this->client->taxRate();
        $taxRate->id = $id;

        return $taxRate->find($id);
    }
}

