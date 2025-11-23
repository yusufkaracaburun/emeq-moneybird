<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Moneybird;

class WorkflowResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(): array
    {
        $workflow = $this->client->workflow();

        return $workflow->get();
    }

    public function find(string $id)
    {
        $workflow = $this->client->workflow();
        $workflow->id = $id;

        return $workflow->find($id);
    }
}
