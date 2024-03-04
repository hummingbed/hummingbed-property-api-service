<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    protected function successHttpMessage($dataType, $data, $message, $code): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            $dataType => $data
        ], $code);
    }

    protected function errorHttpMessage($data, $code, $message = null,): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
