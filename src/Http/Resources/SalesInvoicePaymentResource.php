<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoicePaymentResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'                     => 'id',
            'administration_id'      => 'administration_id',
            'invoice_type'           => 'invoice_type',
            'invoice_id'             => 'invoice_id',
            'financial_account_id'   => 'financial_account_id',
            'user_id'                => 'user_id',
            'payment_transaction_id' => 'payment_transaction_id',
            'transaction_identifier' => 'transaction_identifier',
            'price'                  => 'price',
            'price_base'             => 'price_base',
            'payment_date'           => 'payment_date',
            'credit_invoice_id'      => 'credit_invoice_id',
            'financial_mutation_id'  => 'financial_mutation_id',
            'ledger_account_id'      => 'ledger_account_id',
            'linked_payment_id'      => 'linked_payment_id',
            'manual_payment_action'  => 'manual_payment_action',
            'created_at'             => 'created_at',
            'updated_at'             => 'updated_at',
        ];
    }
}
