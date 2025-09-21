<?php

namespace App\Core\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Respond with a collection of resources.
     *
     * @param mixed $collection
     * @param string $message
     * @param string $modelResource
     * @return AnonymousResourceCollection
     */
    public function respondWithCollection($collection, $modelResource, $message = 'data retrieved successfully.', array $additional = []): AnonymousResourceCollection
    {
        return forward_static_call([$modelResource, 'collection'], $collection)->additional(array_merge([
            'status' => true,
            'message' => $message
        ], $additional));
    }

    /**
     * Respond with a single resource.
     *
     * @param mixed $resource
     * @param string $message
     * @param string $modelResource
     * @return JsonResource
     */
    public function respondWithResource($resource, $modelResource, $message = 'data retrieved successfully.', array $additional = []): JsonResource
    {
        return forward_static_call([$modelResource, 'make'], $resource)->additional(array_merge([
            'status' => true,
            'message' => $message
        ], $additional));
    }

    /**
     * Respond with a success message and optional data.
     *
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    public function respondWithSuccess(string $message = 'Success', array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Respond with an error message.
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    public function respondWithError(string $message = 'An error occurred', array $errors = [], int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    /**
     * Respond with no content (204).
     *
     * @return JsonResponse
     */
    public function respondWithNoContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
