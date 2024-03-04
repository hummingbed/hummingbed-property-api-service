<?php

namespace App\Exceptions;

use App\Traits\HttpResponses;

use Exception;

class EntityNotFoundException extends Exception
{
    use HttpResponses;

    public function render()
    {
        return $this->errorHttpMessage(null, 404, $this->getMessage());
    }
}