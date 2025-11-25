<?php

namespace Emeq\Moneybird\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class MoneybirdCollection extends ResourceCollection
{
    abstract protected function resourceClass(): string;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resourceClass = $this->resourceClass();

        return collect($this->collection)
            ->map(function ($resource) use ($resourceClass, $request) {
                $resourceInstance = $resource instanceof JsonResource
                    ? $resource
                    : new $resourceClass($resource);

                return $resourceInstance->toArray($request);
            })
            ->all();
    }
}
