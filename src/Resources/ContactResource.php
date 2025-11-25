<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Moneybird;
use Picqer\Financials\Moneybird\Entities\Contact;

class ContactResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    /**
     * List all contacts.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, Contact>
     */
    public function list(array $filters = []): array
    {
        $contact = $this->client->contact();

        if (! empty($filters)) {
            return $contact->filter($filters);
        }

        return $contact->get();
    }

    /**
     * Find a contact by ID.
     */
    public function find(string $id): Contact
    {
        $contact     = $this->client->contact();
        $contact->id = $id;

        return $contact->find($id);
    }

    /**
     * Search contacts by query.
     *
     * @return array<int, Contact>
     */
    public function search(string $query): array
    {
        $contact = $this->client->contact();

        return $contact->search($query);
    }

    /**
     * Create a new contact.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Contact
    {
        $contact = $this->client->contact($attributes);
        $contact->save();

        return $contact;
    }

    /**
     * Update an existing contact.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(string $id, array $attributes): Contact
    {
        $contact     = $this->client->contact();
        $contact->id = $id;
        $contact     = $contact->find($id);

        foreach ($attributes as $key => $value) {
            $contact->$key = $value;
        }

        $contact->save();

        return $contact;
    }

    /**
     * Delete a contact.
     */
    public function delete(string $id): bool
    {
        $contact     = $this->client->contact();
        $contact->id = $id;
        $contact     = $contact->find($id);

        $contact->delete();
        
        return true;
    }
}
