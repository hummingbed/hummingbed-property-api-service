<?php

namespace App\Services;

use App\Repositories\PropertyRepository;
use App\Http\Resources\PropertyResource;

class PropertyService extends BaseService
{
    public function __construct(PropertyRepository $repository)
    {
        $this->repo = $repository;
    }

    public function allProperties()
    {
        return $this->repo->findAll([], 'characteristic');
    }
}