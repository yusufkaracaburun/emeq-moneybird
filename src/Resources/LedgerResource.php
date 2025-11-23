<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\Ledger;
use Picqer\Financials\Moneybird\Moneybird;

class LedgerResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(): array
    {
        $ledger = $this->client->ledger();

        return $ledger->get();
    }

    public function find(string $id): Ledger
    {
        $ledger = $this->client->ledger();
        $ledger->id = $id;

        return $ledger->find($id);
    }

    public function create(array $attributes, string $rgsCode): Ledger
    {
        $ledger = $this->client->ledger($attributes);
        $ledger->save($rgsCode);

        return $ledger;
    }

    public function update(string $id, array $attributes, string $rgsCode): Ledger
    {
        $ledger = $this->client->ledger();
        $ledger->id = $id;
        $ledger = $ledger->find($id);

        foreach ($attributes as $key => $value) {
            $ledger->$key = $value;
        }

        $ledger->save($rgsCode);

        return $ledger;
    }

    public function delete(string $id): bool
    {
        $ledger = $this->client->ledger();
        $ledger->id = $id;
        $ledger = $ledger->find($id);

        return $ledger->delete();
    }
}
