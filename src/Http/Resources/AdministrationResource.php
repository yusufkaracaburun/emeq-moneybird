<?php

namespace Emeq\Moneybird\Http\Resources;

class AdministrationResource extends MoneybirdResource
{
    /**
     * Get the field mappings for this resource.
     *
     * @return array<string, string|array<string>>
     */
    protected function getFields(): array
    {
        return [
            'id'                  => 'id',
            'name'                => 'name',
            'language'            => 'language',
            'currency'            => 'currency',
            'country'             => 'country',
            'time_zone'           => 'time_zone',
            'access'              => 'access',
            'suspended'           => 'suspended',
            'period_locked_until' => 'period_locked_until',
        ];
    }
}
