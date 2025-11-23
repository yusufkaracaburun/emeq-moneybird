<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Emeq\Moneybird\Http\Requests\FilterContactRequest;
use Emeq\Moneybird\Http\Requests\SearchContactRequest;
use Emeq\Moneybird\Http\Requests\StoreContactRequest;
use Emeq\Moneybird\Http\Requests\UpdateContactRequest;
use Emeq\Moneybird\Http\Resources\ContactCollection;
use Emeq\Moneybird\Http\Resources\ContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController
{
    use GetsMoneybirdService;

    /**
     * List all contacts.
     */
    public function index(FilterContactRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $filters = array_filter($request->validated());
            $contacts = $service->contacts()->list($filters);

            return (new ContactCollection($contacts))
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
     * Get a specific contact by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $contact = $service->contacts()->find($id);

            return (new ContactResource($contact))
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
     * Search contacts by query.
     */
    public function search(SearchContactRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $validated = $request->validated();
            $contacts = $service->contacts()->search($validated['q']);

            return (new ContactCollection($contacts))
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
     * Create a new contact.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $contact = $service->contacts()->create($request->validated());

            return (new ContactResource($contact))
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
     * Update an existing contact.
     */
    public function update(UpdateContactRequest $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $contact = $service->contacts()->update($id, $request->validated());

            return (new ContactResource($contact))
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
     * Delete a contact.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $service->contacts()->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
