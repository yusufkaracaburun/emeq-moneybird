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

    public function list(array $filters = []): array
    {
        $invoice = $this->client->salesInvoice();

        if (! empty($filters)) {
            return $invoice->filter($filters);
        }

        return $invoice->get();
    }

    public function find(string $id): SalesInvoice
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;

        return $invoice->find($id);
    }

    public function findByInvoiceId(string $invoiceId): SalesInvoice
    {
        $invoices = $this->client->salesInvoice()->filter(['invoice_id' => $invoiceId]);

        if (empty($invoices)) {
            throw new MoneybirdException("Sales invoice with invoice_id '{$invoiceId}' not found");
        }

        return $invoices[0];
    }

    public function create(array $attributes): SalesInvoice
    {
        $invoice = $this->client->salesInvoice($attributes);
        $invoice->save();

        return $invoice;
    }

    public function update(string $id, array $attributes): SalesInvoice
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice = $invoice->find($id);

        foreach ($attributes as $key => $value) {
            $invoice->$key = $value;
        }

        $invoice->save();

        return $invoice;
    }

    public function delete(string $id): bool
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice = $invoice->find($id);

        return $invoice->delete();
    }

    public function send(string $id, string|SendInvoiceOptions $deliveryMethodOrOptions = SendInvoiceOptions::METHOD_EMAIL): SalesInvoice
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice = $invoice->find($id);
        $invoice->sendInvoice($deliveryMethodOrOptions);

        return $invoice;
    }

    public function downloadPdf(string $id): string
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice = $invoice->find($id);

        return $invoice->downloadPdf();
    }

    public function downloadUbl(string $id): string
    {
        $invoice = $this->client->salesInvoice();
        $invoice->id = $id;
        $invoice = $invoice->find($id);

        return $invoice->downloadUbl();
    }
}
