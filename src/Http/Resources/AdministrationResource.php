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
            'id' => 'id',
            'name' => 'name',
            'language' => 'language',
            'currency' => 'currency',
            'time_zone' => ['time_zone', 'timezone'],
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }
}
