<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\Estimate;
use Picqer\Financials\Moneybird\Moneybird;

class EstimateResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(array $filters = []): array
    {
        $estimate = $this->client->estimate();

        if (! empty($filters)) {
            return $estimate->filter($filters);
        }

        return $estimate->get();
    }

    public function find(string|int $id): Estimate
    {
        $estimate = $this->client->estimate();
        $estimate->id = (int) $id;

        return $estimate->find((int) $id);
    }

    public function create(array $attributes): Estimate
    {
        $estimate = $this->client->estimate($attributes);
        $estimate->save();

        return $estimate;
    }

    public function update(string|int $id, array $attributes): Estimate
    {
        $estimate = $this->client->estimate();
        $estimate->id = (int) $id;
        $estimate = $estimate->find((int) $id);

        foreach ($attributes as $key => $value) {
            $estimate->$key = $value;
        }

        $estimate->save();

        return $estimate;
    }

    public function delete(string|int $id): bool
    {
        $estimate = $this->client->estimate();
        $estimate->id = (int) $id;
        $estimate = $estimate->find((int) $id);

        return $estimate->delete();
    }

    public function downloadPdf(string|int $id): string
    {
        $estimate = $this->client->estimate();
        $estimate->id = (int) $id;
        $estimate = $estimate->find((int) $id);

        return $estimate->downloadPdf();
    }
}
