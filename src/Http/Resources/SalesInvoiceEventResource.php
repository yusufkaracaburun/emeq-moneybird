<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceEventResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'administration_id' => 'administration_id',
            'user_id'           => 'user_id',
            'action'            => 'action',
            'link_entity_id'    => 'link_entity_id',
            'link_entity_type'  => 'link_entity_type',
            'data'              => 'data',
            'created_at'        => 'created_at',
            'updated_at'        => 'updated_at',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'data' => [],
        ];
    }
}

