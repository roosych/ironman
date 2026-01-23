<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json(array_merge(['success' => true], $data), $status);
    }

    protected function errorResponse(array $errors = [], int $status = 400, ?string $errorCode = null): JsonResponse
    {
        $response = [
            'success' => false,
            'errors' => $errors,
        ];

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $status);
    }
}
