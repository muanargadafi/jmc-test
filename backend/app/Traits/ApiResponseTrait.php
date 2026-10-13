<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ApiResponseTrait
 *
 * Provides standardized JSON response formatting helpers for API controllers.
 *
 * @package App\Traits
 */
trait ApiResponseTrait
{
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param mixed $meta
     * @string $message
     * @int $code
     * @return JsonResponse
     */
    protected function successResponse(
        mixed $data = null,
        mixed $meta = null,
        string $message = 'Data retrieved successfully.',
        int $code = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param mixed $errors
     * @int $code
     * @return JsonResponse
     */
    protected function error(
        string $message = 'An error occurred.',
        mixed $errors = null,
        int $code = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
