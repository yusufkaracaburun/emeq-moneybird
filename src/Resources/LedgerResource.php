<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\LedgerAccount;
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

    public function find(string $id): LedgerAccount
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        $ledger->id = $id;

        /** @var LedgerAccount */
        return $ledger->find($id);
    }

    public function create(array $attributes, string $rgsCode): LedgerAccount
    {
        /** @var LedgerAccount */
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger($attributes);
        // @phpstan-ignore-next-line
        $ledger->save($rgsCode);

        return $ledger;
    }

    public function update(string $id, array $attributes, string $rgsCode): LedgerAccount
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        $ledger->id = $id;
        /** @var LedgerAccount */
        $ledger = $ledger->find($id);

        foreach ($attributes as $key => $value) {
            $ledger->$key = $value;
        }

        // @phpstan-ignore-next-line
        $ledger->save($rgsCode);

        return $ledger;
    }

    public function delete(string $id): bool
    {
        // @phpstan-ignore-next-line
        $ledger = $this->client->ledger();
        $ledger->id = $id;
        /** @var LedgerAccount */
        $ledger = $ledger->find($id);

        return $ledger->delete();
    }
}
