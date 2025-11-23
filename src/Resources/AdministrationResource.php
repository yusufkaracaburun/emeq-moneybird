<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\Administration;
use Picqer\Financials\Moneybird\Moneybird;

class AdministrationResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    /**
     * List all administrations.
     */
    public function list(): array
    {
        $administration = $this->client->administration();
        $administrations = $administration->get();

        return $administrations;
    }

    /**
     * Get a specific administration by ID.
     */
    public function get(string $id): ?Administration
    {
        $administrations = $this->list();

        foreach ($administrations as $administration) {
            if ($administration->id === $id) {
                return $administration;
            }
        }

        return null;
    }
}
