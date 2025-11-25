<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceDetailResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'                                      => 'id',
            'administration_id'                       => 'administration_id',
            'tax_rate_id'                             => 'tax_rate_id',
            'ledger_account_id'                       => 'ledger_account_id',
            'project_id'                              => 'project_id',
            'product_id'                              => 'product_id',
            'amount'                                  => 'amount',
            'amount_decimal'                          => 'amount_decimal',
            'description'                             => 'description',
            'price'                                   => 'price',
            'period'                                  => 'period',
            'row_order'                               => 'row_order',
            'total_price_excl_tax_with_discount'      => 'total_price_excl_tax_with_discount',
            'total_price_excl_tax_with_discount_base' => 'total_price_excl_tax_with_discount_base',
            'tax_report_reference'                    => 'tax_report_reference',
            'mandatory_tax_text'                      => 'mandatory_tax_text',
            'created_at'                              => 'created_at',
            'updated_at'                              => 'updated_at',
            'is_optional'                             => 'is_optional',
            'is_selected'                             => 'is_selected',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'tax_report_reference' => [],
        ];
    }
}
