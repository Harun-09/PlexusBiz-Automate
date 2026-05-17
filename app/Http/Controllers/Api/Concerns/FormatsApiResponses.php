<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait FormatsApiResponses
{
    /**
     * @param array<string, mixed> $meta
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = 200,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    /**
     * @param array<string, mixed> $meta
     */
    protected function errorResponse(
        string $message,
        int $status = 422,
        mixed $data = null,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    /**
     * @param array<string, mixed> $meta
     */
    protected function resourceResponse(
        JsonResource $resource,
        string $message = 'Request completed successfully.',
        int $status = 200,
        array $meta = [],
    ): JsonResponse {
        return $this->successResponse(
            data: $resource->resolve(),
            message: $message,
            status: $status,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    protected function paginatedResourceResponse(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        string $message = 'Request completed successfully.',
        array $meta = [],
    ): JsonResponse {
        /** @var class-string<JsonResource> $resourceClass */
        $data = $resourceClass::collection($paginator->getCollection())->resolve();
        $pagination = $this->paginationMeta($paginator);

        return $this->successResponse(
            data: $data,
            message: $message,
            meta: array_merge($pagination, $meta, [
                'pagination' => $pagination,
            ]),
        );
    }

    /**
     * @return array<string, int|string|null>
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'path' => $paginator->path(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }
}
