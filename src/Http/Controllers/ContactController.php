<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\GetsMoneybirdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController
{
    use GetsMoneybirdService;

    /**
     * List all contacts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $filters = $request->only(['firstname', 'lastname', 'company_name', 'email']);
            $contacts = $service->contacts()->list(array_filter($filters));

            return response()->json([
                'success' => true,
                'data' => $contacts,
            ]);
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

            return response()->json([
                'success' => true,
                'data' => $contact,
            ]);
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
    public function search(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $query = $request->input('q');

            if (! $query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                ], 400);
            }

            $contacts = $service->contacts()->search($query);

            return response()->json([
                'success' => true,
                'data' => $contacts,
            ]);
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
    public function store(Request $request): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $contact = $service->contacts()->create($request->all());

            return response()->json([
                'success' => true,
                'data' => $contact,
            ], 201);
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
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $service = $this->getService($request);
            $contact = $service->contacts()->update($id, $request->all());

            return response()->json([
                'success' => true,
                'data' => $contact,
            ]);
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
