<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use App\Helpers\ResponseMessages;
use App\Http\Requests\StorePropertyRequest;



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
            ResponseMessages::getSuccessMessage('Properties', 'Retrieved'),
            200
        );
    }
    public function storeProperty(StorePropertyRequest $request)
    {
        $this->propertyService->storePropertiesWithCharacteristics($request);
        return $this->successHttpMessage(
            'data',
            'null',
            ResponseMessages::getSuccessMessage('Property', 'Saved'),
            201
        );
    }
}
