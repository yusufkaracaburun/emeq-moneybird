<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Requests\FilterSalesInvoiceRequest;
use Emeq\Moneybird\Http\Requests\SendSalesInvoiceRequest;
use Emeq\Moneybird\Http\Requests\StoreSalesInvoiceRequest;
use Emeq\Moneybird\Http\Requests\UpdateSalesInvoiceRequest;
use Emeq\Moneybird\Http\Resources\SalesInvoiceCollection;
use Emeq\Moneybird\Http\Resources\SalesInvoiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesInvoiceController
{
    use GetsMoneybirdService;

    /**
     * List all sales invoices.
     */
    public function index(FilterSalesInvoiceRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $filters = array_filter($request->validated());
            $invoices = $service->salesInvoices()->list($filters);

            return (new SalesInvoiceCollection($invoices))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific sales invoice by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->find($id);

            return (new SalesInvoiceResource($invoice))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find a sales invoice by invoice ID.
     */
    public function findByInvoiceId(Request $request, string $invoiceId): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->findByInvoiceId($invoiceId);

            return (new SalesInvoiceResource($invoice))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new sales invoice.
     */
    public function store(StoreSalesInvoiceRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->create($request->validated());

            return (new SalesInvoiceResource($invoice))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing sales invoice.
     */
    public function update(UpdateSalesInvoiceRequest $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->update($id, $request->validated());

            return (new SalesInvoiceResource($invoice))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a sales invoice.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $service->salesInvoices()->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Sales invoice deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a sales invoice.
     */
    public function send(SendSalesInvoiceRequest $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $validated = $request->validated();
            $invoice = $service->salesInvoices()->send($id, $validated['delivery_method']);

            return (new SalesInvoiceResource($invoice))
                ->additional(['message' => 'Sales invoice sent successfully'])
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download a sales invoice as PDF.
     */
    public function downloadPdf(Request $request, string $id): Response
    {
        try {
            $service = $this->getService($request);
            $pdf = $service->salesInvoices()->downloadPdf($id);

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice-'.$id.'.pdf"',
            ]);
        } catch (\Exception $e) {
            return response(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]), 500, [
                'Content-Type' => 'application/json',
            ]);
        }
    }

    /**
     * Download a sales invoice as UBL.
     */
    public function downloadUbl(Request $request, string $id): Response
    {
        try {
            $service = $this->getService($request);
            $ubl = $service->salesInvoices()->downloadUbl($id);

            return response($ubl, 200, [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="invoice-'.$id.'.ubl"',
            ]);
        } catch (\Exception $e) {
            return response(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]), 500, [
                'Content-Type' => 'application/json',
            ]);
        }
    }
}
