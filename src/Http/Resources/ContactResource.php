<?php

namespace Emeq\Moneybird\Http\Resources;

use Emeq\Moneybird\Http\Resources\SalesInvoiceCustomFieldResource as CustomFieldResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceEventResource as EventResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceNoteResource as NoteResource;
use Illuminate\Http\Request;

class ContactResource extends MoneybirdResource
{
    /**
     * Get the field mappings for this resource.
     *
     * @return array<string, string|array<string>>
     */
    protected function getFields(): array
    {
        return [
            'id'                          => 'id',
            'administration_id'           => 'administration_id',
            'company_name'                => 'company_name',
            'firstname'                   => 'firstname',
            'lastname'                    => 'lastname',
            'address1'                    => 'address1',
            'address2'                    => 'address2',
            'zipcode'                     => 'zipcode',
            'city'                        => 'city',
            'country'                     => 'country',
            'phone'                       => 'phone',
            'delivery_method'             => 'delivery_method',
            'customer_id'                 => 'customer_id',
            'tax_number'                  => 'tax_number',
            'chamber_of_commerce'         => 'chamber_of_commerce',
            'bank_account'                => 'bank_account',
            'is_trusted'                  => 'is_trusted',
            'max_transfer_amount'         => 'max_transfer_amount',
            'attention'                   => 'attention',
            'email'                       => 'email',
            'email_ubl'                   => 'email_ubl',
            'send_invoices_to_attention'  => 'send_invoices_to_attention',
            'send_invoices_to_email'      => 'send_invoices_to_email',
            'send_estimates_to_attention' => 'send_estimates_to_attention',
            'send_estimates_to_email'     => 'send_estimates_to_email',
            'sepa_active'                 => 'sepa_active',
            'sepa_iban'                   => 'sepa_iban',
            'sepa_iban_account_name'      => 'sepa_iban_account_name',
            'sepa_bic'                    => 'sepa_bic',
            'sepa_mandate_id'             => 'sepa_mandate_id',
            'sepa_mandate_date'           => 'sepa_mandate_date',
            'sepa_sequence_type'          => 'sepa_sequence_type',
            'credit_card_number'          => 'credit_card_number',
            'credit_card_reference'       => 'credit_card_reference',
            'credit_card_type'            => 'credit_card_type',
            'tax_number_validated_at'     => 'tax_number_validated_at',
            'tax_number_valid'            => 'tax_number_valid',
            'invoice_workflow_id'         => 'invoice_workflow_id',
            'estimate_workflow_id'        => 'estimate_workflow_id',
            'si_identifier'               => 'si_identifier',
            'si_identifier_type'          => 'si_identifier_type',
            'moneybird_payments_mandate'  => 'moneybird_payments_mandate',
            'created_at'                  => 'created_at',
            'updated_at'                  => 'updated_at',
            'version'                     => 'version',
            'sales_invoices_url'          => 'sales_invoices_url',
            'notes'                       => 'notes',
            'custom_fields'               => 'custom_fields',
            'contact_people'              => 'contact_people',
            'archived'                    => 'archived',
            'events'                      => 'events',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'notes'          => [],
            'custom_fields'  => [],
            'contact_people' => [],
            'events'         => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['notes'] = $this->transformCollection(
            $this->getRawAttribute('notes') ?? $data['notes'] ?? [],
            NoteResource::class,
            $request
        );

        $data['custom_fields'] = $this->transformCollection(
            $this->getRawAttribute('custom_fields') ?? $data['custom_fields'] ?? [],
            CustomFieldResource::class,
            $request
        );

        $data['contact_people'] = $this->transformCollection(
            $this->getRawAttribute('contact_people') ?? $data['contact_people'] ?? [],
            ContactPersonResource::class,
            $request
        );

        $data['events'] = $this->transformCollection(
            $this->getRawAttribute('events') ?? $data['events'] ?? [],
            EventResource::class,
            $request
        );

        return $data;
    }

    /**
     * @param  array<int|string, mixed>|null  $items
     * @param  class-string<MoneybirdResource>  $resourceClass
     * @return array<int, array<string, mixed>>
     */
    private function transformCollection(mixed $items, string $resourceClass, Request $request): array
    {
        /** @var array<int|string, mixed> $itemsArray */
        $itemsArray = $items ?? [];

        return collect($itemsArray)
            ->filter(static fn ($item) => $item !== null)
            ->map(static fn ($item) => (new $resourceClass($item))->toArray($request))
            ->values()
            ->all();
    }

    private function getRawAttribute(string $key): mixed
    {
        if (is_array($this->resource) && array_key_exists($key, $this->resource)) {
            return $this->resource[$key];
        }

        if (is_object($this->resource) && isset($this->resource->$key)) {
            return $this->resource->$key;
        }

        return null;
    }
}
