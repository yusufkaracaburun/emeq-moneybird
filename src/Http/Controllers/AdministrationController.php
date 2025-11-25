<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\ApiResponser;
use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Resources\AdministrationCollection;
use Emeq\Moneybird\Http\Resources\AdministrationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdministrationController
{
    use ApiResponser;
    use GetsMoneybirdService;

    /**
     * List all administrations.
     */
    public function index(Request $request): JsonResponse
    {
        $service         = $this->getService($request);
        $administrations = $service->administrations()->list();

        return $this->success(new AdministrationCollection($administrations), 'Administrations fetched successfully');
    }

    /**
     * Get a specific administration by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $service        = $this->getService($request);
        $administration = $service->administrations()->get($id);

        return $this->success(new AdministrationResource($administration), 'Administration fetched successfully');
    }
}
