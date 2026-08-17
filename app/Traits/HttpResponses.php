<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    protected function successResponse(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $payload = [
            'status' => 'success',
            'data' => $data,
        ];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        return response()->json($payload, $status);
    }

    protected function errorResponse(
        ?string $message = null,
        int $status = 400,
        mixed $data = null,
        ?array $errors = null,
    ): JsonResponse {
        $payload = [
            'status' => 'failed',
            'message' => $message,
            'data' => $data,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
