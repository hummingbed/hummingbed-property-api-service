<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessages;
use App\Http\Requests\PropertyFilterRequest;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;

class PropertyController extends BaseController
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function getAllProperties(PropertyFilterRequest $request): JsonResponse
    {
        $properties = $this->propertyService->allProperties($request->validated());
        $transformer = PropertyResource::collection($properties)->response()->getData(true);

        return $this->successResponse($transformer, ResponseMessages::getSuccessMessage('Properties', 'Retrieved'));
    }

    public function storeProperty(StorePropertyRequest $request): JsonResponse
    {
        $property = $this->propertyService->createProperty($request->validated(), $request->user());

        return $this->successResponse(new PropertyResource($property), ResponseMessages::getSuccessMessage('Property', 'Saved'), 201);
    }

    public function getSingleProperty($id): JsonResponse
    {
        $property = $this->propertyService->getPropertyById($id);
        $propertyTransformer = new PropertyResource($property);

        return $this->successResponse($propertyTransformer, ResponseMessages::getSuccessMessage('Property', 'retrieved'));
    }

    public function updateProperty(UpdatePropertyRequest $request, $id): JsonResponse
    {
        $property = $this->propertyService->updateProperty($request->validated(), $id, $request->user());

        return $this->successResponse(new PropertyResource($property), ResponseMessages::getSuccessMessage('Property', 'updated'));
    }

    public function deleteProperty($id): JsonResponse
    {
        $this->propertyService->deleteProperty($id, request()->user());

        return $this->successResponse(message: ResponseMessages::getSuccessMessage('Property', 'deleted'));
    }
}
