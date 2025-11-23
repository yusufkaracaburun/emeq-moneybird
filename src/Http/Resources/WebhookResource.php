<?php

namespace Emeq\Moneybird\Http\Resources;

class WebhookResource extends MoneybirdResource
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
            'administration_id' => 'administration_id',
            'url' => 'url',
            'events' => 'events',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }

    /**
     * Get default values for fields.
     *
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'events' => [],
        ];
    }
}
