<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\Contact;
use Picqer\Financials\Moneybird\Moneybird;

class ContactResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function list(array $filters = []): array
    {
        $contact = $this->client->contact();

        if (! empty($filters)) {
            return $contact->filter($filters);
        }

        return $contact->get();
    }

    public function find(string $id): Contact
    {
        $contact = $this->client->contact();
        $contact->id = $id;

        return $contact->find($id);
    }

    public function search(string $query): array
    {
        $contact = $this->client->contact();

        return $contact->search($query);
    }

    public function create(array $attributes): Contact
    {
        $contact = $this->client->contact($attributes);
        $contact->save();

        return $contact;
    }

    public function update(string $id, array $attributes): Contact
    {
        $contact = $this->client->contact();
        $contact->id = $id;
        $contact = $contact->find($id);

        foreach ($attributes as $key => $value) {
            $contact->$key = $value;
        }

        $contact->save();

        return $contact;
    }

    public function delete(string $id): bool
    {
        $contact = $this->client->contact();
        $contact->id = $id;
        $contact = $contact->find($id);

        return $contact->delete();
    }
}
