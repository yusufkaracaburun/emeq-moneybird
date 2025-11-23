<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookApiController
{
    use GetsMoneybirdService;

    /**
     * List all webhooks.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $webhooks = $service->webhooks()->list();

            return response()->json([
                'success' => true,
                'data' => $webhooks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new webhook.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $webhook = $service->webhooks()->create($request->all());

            return response()->json([
                'success' => true,
                'data' => $webhook,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a webhook.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $service->webhooks()->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Webhook deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
