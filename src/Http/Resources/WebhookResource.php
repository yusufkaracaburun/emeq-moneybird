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
            'id'                => 'id',
            'administration_id' => 'administration_id',
            'url'               => 'url',
            'enabled_events'    => 'enabled_events',
            'last_http_status'  => 'last_http_status',
            'last_http_body'    => 'last_http_body',
            'token'             => 'token',
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
            'enabled_events'   => [],
            'last_http_status' => null,
            'last_http_body'   => null,
        ];
    }
}
