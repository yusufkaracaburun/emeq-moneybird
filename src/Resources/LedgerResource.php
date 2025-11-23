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
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();

        return $ledger->get();
    }

    public function find(string $id): Ledger
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        // @phpstan-ignore-next-line
        $ledger->id = $id;

        // @phpstan-ignore-next-line
        return $ledger->find($id);
    }

    public function create(array $attributes, string $rgsCode): Ledger
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger($attributes);
        $ledger->save($rgsCode);

        return $ledger;
    }

    public function update(string $id, array $attributes, string $rgsCode): Ledger
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        // @phpstan-ignore-next-line
        $ledger->id = $id;
        // @phpstan-ignore-next-line
        $ledger = $ledger->find($id);

        foreach ($attributes as $key => $value) {
            $ledger->$key = $value;
        }

        $ledger->save($rgsCode);

        return $ledger;
    }

    public function delete(string $id): bool
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        // @phpstan-ignore-next-line
        $ledger->id = $id;
        // @phpstan-ignore-next-line
        $ledger = $ledger->find($id);

        return $ledger->delete();
    }
}

