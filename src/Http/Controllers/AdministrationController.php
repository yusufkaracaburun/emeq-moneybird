<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Resources\AdministrationCollection;
use Emeq\Moneybird\Http\Resources\AdministrationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdministrationController
{
    use GetsMoneybirdService;

    /**
     * List all administrations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $administrations = $service->administrations()->list();

            return (new AdministrationCollection($administrations))
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
     * Get a specific administration by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $administration = $service->administrations()->get($id);

            if (! $administration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Administration not found',
                ], 404);
            }

            return (new AdministrationResource($administration))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
