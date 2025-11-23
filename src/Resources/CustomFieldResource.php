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
        $customField = $this->client->customField();

        return $customField->get();
    }

    public function find(string $id)
    {
        $customField = $this->client->customField();
        $customField->id = $id;

        return $customField->find($id);
    }
}

