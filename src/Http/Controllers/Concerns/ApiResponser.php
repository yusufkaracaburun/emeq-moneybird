<?php

namespace Emeq\Moneybird\Http\Controllers\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

trait ApiResponser
{
    /**
     * Used to return success response
     *
     * @param  Arrayable<string, mixed>|JsonResource|array<string, mixed>|string|object|null  $items
     */
    public function ok(mixed $items = null): JsonResponse
    {
        //        return response()->json($items)->setEncodingOptions(JSON_NUMERIC_CHECK);
        return response()->json($items);
    }

    /**
     * Return a standardized success JSON response.
     *
     * @param  Arrayable<string, mixed>|JsonResource|array<string, mixed>|string|object|null  $data
     */
    protected function success(mixed $data, ?string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'is_success' => true,
            'status'     => $code,
            'statusText' => 'Success',
            'message'    => $message,
            'data'       => $this->formatPayload($data, request()),
        ], $code);
    }

    /**
     * Return a standardized created JSON response.
     *
     * @param  Arrayable<string, mixed>|JsonResource|array<string, mixed>|string|object|null  $data
     */
    protected function created(mixed $data, ?string $message = null): JsonResponse
    {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param  Arrayable<string, mixed>|JsonResource|array<string, mixed>|string|null  $data
     */
    protected function error(?string $message = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY, mixed $data = null): JsonResponse
    {
        return response()->json([
            'is_success' => false,
            'status'     => $code,
            'statusText' => 'Error',
            'message'    => $message,
            'data'       => $this->formatPayload($data, request()),
        ], $code);
    }

    /**
     * Return a standardized no-content JSON response with optional message.
     */
    protected function noContent(?string $message = null, int $code = Response::HTTP_NO_CONTENT): JsonResponse
    {
        return response()->json([
            'is_success' => true,
            'status'     => $code,
            'statusText' => 'Success',
            'message'    => $message,
            'data'       => null,
        ], $code);
    }

    /**
     * Normalize payloads so they can be embedded in the API response structure.
     *
     * @param  Arrayable<string, mixed>|JsonResource|array<string, mixed>|string|object|null  $payload
     * @return array<string, mixed>|string|object|null
     */
    protected function formatPayload(mixed $payload, ?Request $request = null): array|string|object|null
    {
        if ($payload instanceof JsonResource) {
            return $payload->resolve($request ?? request());
        }

        if ($payload instanceof Arrayable) {
            return $payload->toArray();
        }

        return $payload;
    }

    /**
     * Return a binary download response.
     */
    protected function downloadFile(string $contents, string $filename, string $mimeType): Response
    {
        return response($contents, Response::HTTP_OK, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
