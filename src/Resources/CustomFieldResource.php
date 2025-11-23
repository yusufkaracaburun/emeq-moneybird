<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Moneybird;

class CustomFieldResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(): array
    {
        // @phpstan-ignore-next-line
        $customField = $this->client->customField();

        return $customField->get();
    }

    public function find(string $id)
    {
        // @phpstan-ignore-next-line
        $customField = $this->client->customField();
        // @phpstan-ignore-next-line
        $customField->id = $id;

        // @phpstan-ignore-next-line
        return $customField->find($id);
    }
}
