<?php

namespace Emeq\Moneybird\Http\Controllers;

use Emeq\Moneybird\Http\Controllers\Concerns\ApiResponser;
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
    use ApiResponser;
    use GetsMoneybirdService;

    /**
     * List all contacts.
     */
    public function index(FilterContactRequest $request): JsonResponse
    {
        $service  = $this->getService($request);
        $filters  = array_filter($request->validated());
        $contacts = $service->contacts()->list($filters);

        return $this->success(new ContactCollection($contacts), 'Contacts listed');
    }

    /**
     * Get a specific contact by ID.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $contact = $service->contacts()->find($id);

        return $this->success(new ContactResource($contact), 'Contact gevonden');
    }

    /**
     * Search contacts by query.
     */
    public function search(SearchContactRequest $request): JsonResponse
    {
        $service   = $this->getService($request);
        $validated = $request->validated();
        $contacts  = $service->contacts()->search($validated['q']);

        return $this->success(new ContactCollection($contacts), 'Contacten gezocht');
    }

    /**
     * Create a new contact.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $service = $this->getService($request);
        $contact = $service->contacts()->create($request->validated());

        return $this->created(new ContactResource($contact), 'Contact created');
    }

    /**
     * Update an existing contact.
     */
    public function update(UpdateContactRequest $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $contact = $service->contacts()->update($id, $request->validated());

        return $this->success(new ContactResource($contact), 'Contact updated');
    }

    /**
     * Delete a contact.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $service = $this->getService($request);
        $service->contacts()->delete($id);

        return $this->noContent('Contact deleted successfully');
    }
}
