<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceCustomFieldResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'    => 'id',
            'name'  => 'name',
            'value' => 'value',
        ];
    }
}

