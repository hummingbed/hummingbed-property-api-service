<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use App\Helpers\ResponseMessages;



class PropertyController extends BaseController
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }
    public function getAllProperties()
    {
        $properties = $this->propertyService->allProperties();
        $transformer = PropertyResource::collection($properties);
        return $this->successHttpMessage(
            'data',
            $transformer,
            ResponseMessages::getSuccessMessage('Brokers', 'retrieved'),
            200
        );
    }
}
