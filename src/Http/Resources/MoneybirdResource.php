<?php

namespace Emeq\Moneybird\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class MoneybirdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        if (is_object($resource) && method_exists($resource, 'attributes')) {
            return $this->transformFromAttributes($resource->attributes(), $resource);
        }

        if (is_array($resource)) {
            return $this->transformFromArray($resource);
        }

        return $this->transformFromObject($resource);
    }

    /**
     * Get the field mappings for this resource.
     * Key is the output key, value is the source field name or array of alternatives.
     *
     * @return array<string, string|array<string>>
     */
    abstract protected function getFields(): array;

    /**
     * Get default values for fields.
     *
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [];
    }

    /**
     * Transform resource from attributes array.
     *
     * @param  array<string, mixed>  $attrs
     * @return array<string, mixed>
     */
    protected function transformFromAttributes(array $attrs, object $resource): array
    {
        $result   = [];
        $defaults = $this->getDefaults();

        foreach ($this->getFields() as $key => $field) {
            $result[$key] = $this->extractValue($attrs, $resource, $field, $defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * Transform resource from array.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function transformFromArray(array $data): array
    {
        $result   = [];
        $defaults = $this->getDefaults();

        foreach ($this->getFields() as $key => $field) {
            $fields = is_array($field) ? $field : [$field];
            $value  = null;

            foreach ($fields as $f) {
                if (isset($data[$f])) {
                    $value = $data[$f];

                    break;
                }
            }

            $result[$key] = $value ?? $defaults[$key] ?? null;
        }

        return $result;
    }

    /**
     * Transform resource from object with direct property access.
     *
     * @return array<string, mixed>
     */
    protected function transformFromObject(object $resource): array
    {
        $result   = [];
        $defaults = $this->getDefaults();

        foreach ($this->getFields() as $key => $field) {
            $result[$key] = $this->extractValue([], $resource, $field, $defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * Extract value from attributes, object, or use default.
     *
     * @param  array<string, mixed>  $attrs
     * @param  string|array<string>  $field
     */
    protected function extractValue(array $attrs, object $resource, string|array $field, mixed $default = null): mixed
    {
        $fields       = is_array($field) ? $field : [$field];
        $value        = null;
        $primaryField = $fields[0];

        foreach ($fields as $f) {
            if (! empty($attrs) && isset($attrs[$f])) {
                $value = $attrs[$f];

                break;
            }
        }

        if ($value === null) {
            foreach ($fields as $f) {
                if (isset($resource->$f)) {
                    $value = $resource->$f;

                    break;
                }
            }
        }

        if ($value === null) {
            return $default;
        }

        return $this->formatValue($value, $primaryField);
    }

    /**
     * Format the value (e.g., convert dates to strings).
     */
    protected function formatValue(mixed $value, string $field): mixed
    {
        if ($value === null) {
            return null;
        }

        $dateFields = ['created_at', 'updated_at', 'invoice_date', 'due_date'];

        if (in_array($field, $dateFields)
            && is_object($value)
            && method_exists($value, 'toDateTimeString')) {
            return $value->toDateTimeString();
        }

        return $value;
    }
}
