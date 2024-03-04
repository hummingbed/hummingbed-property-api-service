<?php

namespace App\Helpers;

class ResponseMessages
{
    const UNPROCESSABLE_CONTENT = 'Unprocessable content';
    const AUTH_SUCCESS = 'Authentication successful';
    public static function getSuccessMessage($entity, $action = 'created'): string
    {
        return sprintf('%s %s successfully', $entity, $action);
    }

    public static function notFoundErrorMessage($entity): string
    {
        return sprintf('%s Not Found', $entity);
    }
}