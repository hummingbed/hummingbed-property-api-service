<?php

namespace App\Exceptions;

use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;

class EntityNotFoundException extends Exception
{
    use HttpResponses;

    public function render(): JsonResponse
    {
        return $this->errorResponse($this->getMessage(), 404);
    }
}
