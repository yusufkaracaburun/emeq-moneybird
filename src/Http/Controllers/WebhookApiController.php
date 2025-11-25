<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\ApiResponser;
use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Requests\StoreWebhookRequest;
use Emeq\Moneybird\Http\Resources\WebhookCollection;
use Emeq\Moneybird\Http\Resources\WebhookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookApiController
{
    use ApiResponser;
    use GetsMoneybirdService;

    /**
     * List all webhooks.
     */
    public function index(Request $request): JsonResponse
    {
        $service = $this->getService($request);
        $webhooks = $service->webhooks()->list();

        return $this->success(new WebhookCollection($webhooks), 'Webhooks listed');
    }

    /**
     * Create a new webhook.
     */
    public function store(StoreWebhookRequest $request): JsonResponse
    {
        $service = $this->getService($request);
        $webhook = $service->webhooks()->create($request->validated());

        return $this->created(new WebhookResource($webhook), 'Webhook created');
    }

    /**
     * Delete a webhook.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $service->webhooks()->delete($id);

        return $this->noContent('Webhook deleted successfully');
    }
}
