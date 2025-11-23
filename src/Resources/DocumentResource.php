<?php

namespace Emeq\Moneybird\Resources;

use Picqer\Financials\Moneybird\Entities\GeneralDocument;
use Picqer\Financials\Moneybird\Entities\TypelessDocument;
use Picqer\Financials\Moneybird\Moneybird;

class DocumentResource
{
    public function __construct(
        protected Moneybird $client
    ) {}

    public function listGeneralDocuments(array $filters = []): array
    {
        $document = $this->client->generalDocument();

        return $document->get();
    }

    public function findGeneralDocument(string $id): GeneralDocument
    {
        $document = $this->client->generalDocument();
        // @phpstan-ignore-next-line
        $document->id = $id;

        return $document->find($id);
    }

    public function createGeneralDocument(array $attributes): GeneralDocument
    {
        $document = $this->client->generalDocument($attributes);
        $document->save();

        return $document;
    }

    public function updateGeneralDocument(string $id, array $attributes): GeneralDocument
    {
        $document = $this->client->generalDocument();
        // @phpstan-ignore-next-line
        $document->id = $id;
        $document = $document->find($id);

        foreach ($attributes as $key => $value) {
            $document->$key = $value;
        }

        $document->save();

        return $document;
    }

    public function deleteGeneralDocument(string $id): bool
    {
        $document = $this->client->generalDocument();
        // @phpstan-ignore-next-line
        $document->id = $id;
        $document = $document->find($id);

        return $document->delete();
    }

    public function listTypelessDocuments(array $filters = []): array
    {
        $document = $this->client->typelessDocument();

        return $document->get();
    }

    public function findTypelessDocument(string $id): TypelessDocument
    {
        $document = $this->client->typelessDocument();
        $document->id = $id;

        return $document->find($id);
    }

    public function createTypelessDocument(array $attributes): TypelessDocument
    {
        $document = $this->client->typelessDocument($attributes);
        $document->save();

        return $document;
    }

    public function updateTypelessDocument(string $id, array $attributes): TypelessDocument
    {
        $document = $this->client->typelessDocument();
        $document->id = $id;
        $document = $document->find($id);

        foreach ($attributes as $key => $value) {
            $document->$key = $value;
        }

        $document->save();

        return $document;
    }

    public function deleteTypelessDocument(string $id): bool
    {
        $document = $this->client->typelessDocument();
        $document->id = $id;
        $document = $document->find($id);

        return $document->delete();
    }
}
