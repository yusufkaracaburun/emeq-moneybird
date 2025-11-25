<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceTaxTotalResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'tax_rate_id'         => 'tax_rate_id',
            'taxable_amount'      => 'taxable_amount',
            'taxable_amount_base' => 'taxable_amount_base',
            'tax_amount'          => 'tax_amount',
            'tax_amount_base'     => 'tax_amount_base',
        ];
    }
}
