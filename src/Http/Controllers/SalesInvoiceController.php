<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Picqer\Financials\Moneybird\Entities\SalesInvoice\SendInvoiceOptions;

class SalesInvoiceController
{
    use GetsMoneybirdService;

    /**
     * List all sales invoices.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $filters = $request->only(['contact_id', 'state', 'invoice_id']);
            $invoices = $service->salesInvoices()->list(array_filter($filters));

            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
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

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
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

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
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
    public function store(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->create($request->all());

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ], 201);
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
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $invoice = $service->salesInvoices()->update($id, $request->all());

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
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
    public function send(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $deliveryMethod = $request->input('delivery_method', SendInvoiceOptions::METHOD_EMAIL);
            $invoice = $service->salesInvoices()->send($id, $deliveryMethod);

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Sales invoice sent successfully',
            ]);
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
