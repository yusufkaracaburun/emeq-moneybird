<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
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

            // Ensure administrations is an array
            if (! is_array($administrations)) {
                $administrations = [];
            }

            // Transform administration objects to arrays
            $data = array_map(function ($admin) {
                if (is_object($admin)) {
                    return [
                        'id' => $admin->id ?? null,
                        'name' => $admin->name ?? null,
                        'language' => $admin->language ?? null,
                        'currency' => $admin->currency ?? null,
                        'time_zone' => $admin->time_zone ?? ($admin->timezone ?? null),
                        'created_at' => isset($admin->created_at) && method_exists($admin->created_at, 'toDateTimeString')
                            ? $admin->created_at->toDateTimeString()
                            : ($admin->created_at ?? null),
                        'updated_at' => isset($admin->updated_at) && method_exists($admin->updated_at, 'toDateTimeString')
                            ? $admin->updated_at->toDateTimeString()
                            : ($admin->updated_at ?? null),
                    ];
                }

                return $admin;
            }, $administrations);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
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

            return response()->json([
                'success' => true,
                'data' => $administration,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
