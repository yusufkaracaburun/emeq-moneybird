<?php

namespace Emeq\Moneybird\Resources;

use Emeq\Moneybird\Exceptions\MoneybirdException;
use Picqer\Financials\Moneybird\Entities\SalesInvoice;
use Picqer\Financials\Moneybird\Entities\SalesInvoice\SendInvoiceOptions;
use Picqer\Financials\Moneybird\Moneybird;

class SalesInvoiceResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    /**
     * List all sales invoices.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, SalesInvoice>
     */
    public function list(array $filters = []): array
    {
        $invoice = $this->client->salesInvoice();

        if (! empty($filters)) {
            return $invoice->filter($filters);
        }

        return $invoice->get();
    }

    /**
     * Find a sales invoice by ID.
     */
    public function find(string $id): SalesInvoice
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;

        return $invoice->find($id);
    }

    /**
     * Find a sales invoice by invoice ID.
     */
    public function findByInvoiceId(string $invoiceId): SalesInvoice
    {
        $invoices = $this->client->salesInvoice()->filter(['invoice_id' => $invoiceId]);

        if (empty($invoices)) {
            throw new MoneybirdException("Sales invoice with invoice_id '{$invoiceId}' not found");
        }

        return $invoices[0];
    }

    /**
     * Create a new sales invoice.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): SalesInvoice
    {
        $invoice = $this->client->salesInvoice($attributes);
        $invoice->save();

        return $invoice;
    }

    /**
     * Update an existing sales invoice.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(string $id, array $attributes): SalesInvoice
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice     = $invoice->find($id);

        foreach ($attributes as $key => $value) {
            $invoice->$key = $value;
        }

        $invoice->save();

        return $invoice;
    }

    /**
     * Delete a sales invoice.
     */
    public function delete(string $id): bool
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice     = $invoice->find($id);

        return $invoice->delete();
    }

    /**
     * Send a sales invoice.
     */
    public function send(string $id, string|SendInvoiceOptions $deliveryMethodOrOptions = SendInvoiceOptions::METHOD_EMAIL): SalesInvoice
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice     = $invoice->find($id);
        $invoice->sendInvoice($deliveryMethodOrOptions);

        return $invoice;
    }

    /**
     * Download a sales invoice as PDF.
     */
    public function downloadPdf(string $id): string
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice     = $invoice->find($id);

        return $invoice->downloadPdf();
    }

    /**
     * Download a sales invoice as UBL.
     */
    public function downloadUbl(string $id): string
    {
        $invoice     = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice     = $invoice->find($id);

        return $invoice->downloadUbl();
    }
}
