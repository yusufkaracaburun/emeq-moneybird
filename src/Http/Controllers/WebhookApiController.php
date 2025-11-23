<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Requests\StoreWebhookRequest;
use Emeq\Moneybird\Http\Resources\WebhookCollection;
use Emeq\Moneybird\Http\Resources\WebhookResource;
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

            return (new WebhookCollection($webhooks))
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
     * Create a new webhook.
     */
    public function store(StoreWebhookRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $webhook = $service->webhooks()->create($request->validated());

            return (new WebhookResource($webhook))
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
