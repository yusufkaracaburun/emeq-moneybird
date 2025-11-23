<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceResource extends MoneybirdResource
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
            'invoice_id' => 'invoice_id',
            'contact_id' => 'contact_id',
            'state' => 'state',
            'invoice_date' => 'invoice_date',
            'due_date' => 'due_date',
            'total_price_excl_tax' => 'total_price_excl_tax',
            'total_price_incl_tax' => 'total_price_incl_tax',
            'currency' => 'currency',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }
}
