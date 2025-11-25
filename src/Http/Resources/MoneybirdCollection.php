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
     * @return array<int, array<string, mixed>>
     */
    public function toArray(Request $request): array
    {
        $resourceClass = $this->resourceClass();

        /** @var \Illuminate\Support\Collection<int, mixed> $collection */
        $collection = collect($this->collection);

        /** @var array<int, array<string, mixed>> $result */
        $result = $collection
            ->map(function (mixed $resource) use ($resourceClass, $request): array {
                $resourceInstance = $resource instanceof JsonResource
                    ? $resource
                    : new $resourceClass($resource);

                return $resourceInstance->toArray($request);
            })
            ->all();

        return $result;
    }
}
