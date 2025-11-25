<?php

namespace Emeq\Moneybird\Http\Resources;

use Emeq\Moneybird\Http\Resources\SalesInvoiceAttachmentResource as AttachmentResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceCustomFieldResource as CustomFieldResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceDetailResource as DetailResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceEventResource as EventResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceNoteResource as NoteResource;
use Emeq\Moneybird\Http\Resources\SalesInvoicePaymentResource as PaymentResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceTaxTotalResource as TaxTotalResource;
use Emeq\Moneybird\Http\Resources\SalesInvoiceTimeEntryResource as TimeEntryResource;
use Illuminate\Http\Request;

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
            'id'                          => 'id',
            'administration_id'           => 'administration_id',
            'contact_id'                  => 'contact_id',
            'contact'                     => 'contact',
            'contact_person_id'           => 'contact_person_id',
            'contact_person'              => 'contact_person',
            'invoice_id'                  => 'invoice_id',
            'recurring_sales_invoice_id'  => 'recurring_sales_invoice_id',
            'subscription_id'             => 'subscription_id',
            'workflow_id'                 => 'workflow_id',
            'document_style_id'           => 'document_style_id',
            'identity_id'                 => 'identity_id',
            'draft_id'                    => 'draft_id',
            'state'                       => 'state',
            'invoice_date'                => 'invoice_date',
            'due_date'                    => 'due_date',
            'payment_conditions'          => 'payment_conditions',
            'payment_reference'           => 'payment_reference',
            'short_payment_reference'     => 'short_payment_reference',
            'reference'                   => 'reference',
            'language'                    => 'language',
            'currency'                    => 'currency',
            'discount'                    => 'discount',
            'original_sales_invoice_id'   => 'original_sales_invoice_id',
            'paused'                      => 'paused',
            'paid_at'                     => 'paid_at',
            'sent_at'                     => 'sent_at',
            'created_at'                  => 'created_at',
            'updated_at'                  => 'updated_at',
            'public_view_code'            => 'public_view_code',
            'public_view_code_expires_at' => 'public_view_code_expires_at',
            'version'                     => 'version',
            'details'                     => 'details',
            'payments'                    => 'payments',
            'total_paid'                  => 'total_paid',
            'total_unpaid'                => 'total_unpaid',
            'total_unpaid_base'           => 'total_unpaid_base',
            'prices_are_incl_tax'         => 'prices_are_incl_tax',
            'total_price_excl_tax'        => 'total_price_excl_tax',
            'total_price_excl_tax_base'   => 'total_price_excl_tax_base',
            'total_price_incl_tax'        => 'total_price_incl_tax',
            'total_price_incl_tax_base'   => 'total_price_incl_tax_base',
            'total_discount'              => 'total_discount',
            'marked_dubious_on'           => 'marked_dubious_on',
            'marked_uncollectible_on'     => 'marked_uncollectible_on',
            'reminder_count'              => 'reminder_count',
            'next_reminder'               => 'next_reminder',
            'original_estimate_id'        => 'original_estimate_id',
            'url'                         => 'url',
            'payment_url'                 => 'payment_url',
            'custom_fields'               => 'custom_fields',
            'notes'                       => 'notes',
            'attachments'                 => 'attachments',
            'events'                      => 'events',
            'tax_totals'                  => 'tax_totals',
            'time_entries'                => 'time_entries',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'details'       => [],
            'payments'      => [],
            'custom_fields' => [],
            'notes'         => [],
            'attachments'   => [],
            'events'        => [],
            'tax_totals'    => [],
            'time_entries'  => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['contact'] = $this->transformResource(
            $this->getRawAttribute('contact') ?? $data['contact'] ?? null,
            ContactResource::class,
            $request
        );

        $data['contact_person'] = $this->transformResource(
            $this->getRawAttribute('contact_person') ?? $data['contact_person'] ?? null,
            ContactPersonResource::class,
            $request
        );

        $data['details'] = $this->transformCollection(
            $this->getRawAttribute('details') ?? $data['details'] ?? [],
            DetailResource::class,
            $request
        );

        $data['payments'] = $this->transformCollection(
            $this->getRawAttribute('payments') ?? $data['payments'] ?? [],
            PaymentResource::class,
            $request
        );

        $data['custom_fields'] = $this->transformCollection(
            $this->getRawAttribute('custom_fields') ?? $data['custom_fields'] ?? [],
            CustomFieldResource::class,
            $request
        );

        $data['notes'] = $this->transformCollection(
            $this->getRawAttribute('notes') ?? $data['notes'] ?? [],
            NoteResource::class,
            $request
        );

        $data['attachments'] = $this->transformCollection(
            $this->getRawAttribute('attachments') ?? $data['attachments'] ?? [],
            AttachmentResource::class,
            $request
        );

        $data['events'] = $this->transformCollection(
            $this->getRawAttribute('events') ?? $data['events'] ?? [],
            EventResource::class,
            $request
        );

        $data['tax_totals'] = $this->transformCollection(
            $this->getRawAttribute('tax_totals') ?? $data['tax_totals'] ?? [],
            TaxTotalResource::class,
            $request
        );

        $data['time_entries'] = $this->transformCollection(
            $this->getRawAttribute('time_entries') ?? $data['time_entries'] ?? [],
            TimeEntryResource::class,
            $request
        );

        return $data;
    }

    private function transformResource(mixed $value, string $resourceClass, Request $request): mixed
    {
        if ($value === null) {
            return null;
        }

        return (new $resourceClass($value))->toArray($request);
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
