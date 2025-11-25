<?php

namespace Emeq\Moneybird\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Emeq\Moneybird\Http\Resources\SalesInvoiceResource;
use Emeq\Moneybird\Http\Requests\SendSalesInvoiceRequest;
use Emeq\Moneybird\Http\Resources\SalesInvoiceCollection;
use Emeq\Moneybird\Http\Controllers\Concerns\ApiResponser;
use Emeq\Moneybird\Http\Requests\StoreSalesInvoiceRequest;
use Emeq\Moneybird\Http\Requests\FilterSalesInvoiceRequest;
use Emeq\Moneybird\Http\Requests\UpdateSalesInvoiceRequest;
use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;

class SalesInvoiceController
{
    use ApiResponser;
    use GetsMoneybirdService;

    /**
     * List all sales invoices.
     */
    public function index(FilterSalesInvoiceRequest $request): JsonResponse
    {
        $service = $this->getService($request);
        $filters = array_filter($request->validated());
        $invoices = $service->salesInvoices()->list($filters);

        return $this->success(new SalesInvoiceCollection($invoices), 'Sales invoices listed');
    }

    /**
     * Get a specific sales invoice by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $invoice = $service->salesInvoices()->find($id);

        return $this->success(new SalesInvoiceResource($invoice), 'Sales invoice gevonden');
    }

    /**
     * Find a sales invoice by invoice ID.
     */
    public function findByInvoiceId(Request $request, string $invoiceId): JsonResponse
    {
        $service = $this->getService($request);
        $invoice = $service->salesInvoices()->findByInvoiceId($invoiceId);

        return $this->success(new SalesInvoiceResource($invoice), 'Sales invoice gevonden');
    }

    /**
     * Create a new sales invoice.
     */
    public function store(StoreSalesInvoiceRequest $request): JsonResponse
    {
        $service = $this->getService($request);
        $invoice = $service->salesInvoices()->create($request->validated());

        return $this->created(new SalesInvoiceResource($invoice), 'Sales invoice created');
    }

    /**
     * Update an existing sales invoice.
     */
    public function update(UpdateSalesInvoiceRequest $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $invoice = $service->salesInvoices()->update($id, $request->validated());

        return $this->success(new SalesInvoiceResource($invoice), 'Sales invoice updated');
    }

    /**
     * Delete a sales invoice.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $service->salesInvoices()->delete($id);

        return $this->noContent('Sales invoice deleted successfully');
    }

    /**
     * Send a sales invoice.
     */
    public function send(SendSalesInvoiceRequest $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $validated = $request->validated();
        $invoice = $service->salesInvoices()->send($id, $validated['delivery_method']);

        return $this->success(new SalesInvoiceResource($invoice), 'Sales invoice sent successfully');
    }

    /**
     * Download a sales invoice as PDF.
     */
    public function downloadPdf(Request $request, string $id): Response|JsonResponse
    {
        $service = $this->getService($request);
        $pdf = $service->salesInvoices()->downloadPdf($id);

        return $this->downloadFile($pdf, 'invoice-'.$id.'.pdf', 'application/pdf');
    }

    /**
     * Download a sales invoice as UBL.
     */
    public function downloadUbl(Request $request, string $id): Response|JsonResponse
    {
        $service = $this->getService($request);
        $ubl = $service->salesInvoices()->downloadUbl($id);

        return $this->downloadFile($ubl, 'invoice-'.$id.'.ubl', 'application/xml');
    }
}
