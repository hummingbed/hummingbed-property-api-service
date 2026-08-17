<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessages;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\SinglePropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends BaseController
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function getAllProperties(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'city' => ['sometimes', 'string', 'max:100'],
            'listing_type' => ['sometimes', 'string'],
            'property_type' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'min_price' => ['sometimes', 'integer', 'min:0'],
            'max_price' => ['sometimes', 'integer', 'gte:min_price'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'amenity' => ['sometimes', 'string', 'max:100'],
            'featured' => ['sometimes', 'boolean'],
        ]);
        $properties = $this->propertyService->allProperties($filters);
        $transformer = PropertyResource::collection($properties)->response()->getData(true);

        return $this->successHttpMessage(
            'data',
            $transformer,
            ResponseMessages::getSuccessMessage('Properties', 'Retrieved'),
            200
        );
    }

    public function storeProperty(StorePropertyRequest $request): JsonResponse
    {
        $this->authorizeBrokerOwnership($request, (int) $request->broker_id);
        $property = $this->propertyService->storePropertiesWithCharacteristics($request);

        return $this->successHttpMessage(
            'data',
            new SinglePropertyResource($property),
            ResponseMessages::getSuccessMessage('Property', 'Saved'),
            201
        );
    }

    public function getSingleProperty($id): JsonResponse
    {
        $property = $this->propertyService->getPropertyById($id);
        $propertyTransformer = new SinglePropertyResource($property);

        return $this->successHttpMessage(
            'data',
            $propertyTransformer,
            ResponseMessages::getSuccessMessage('Property', 'retrieved'),
            200
        );
    }

    public function updateProperty(UpdatePropertyRequest $request, $id): JsonResponse
    {
        $this->authorizePropertyOwnership($request, $id);
        $property = $this->propertyService->updateProperty($request, $id);

        return $this->successHttpMessage(
            'data',
            new SinglePropertyResource($property),
            ResponseMessages::getSuccessMessage('Property', 'updated'),
            200
        );
    }

    public function deleteProperty($id): JsonResponse
    {
        $this->authorizePropertyOwnership(request(), $id);
        $this->propertyService->deleteProperty($id);

        return $this->successHttpMessage(
            'data',
            null,
            ResponseMessages::getSuccessMessage('Property', 'deleted'),
            200
        );
    }

    private function authorizePropertyOwnership(Request $request, $id): void
    {
        $property = $this->propertyService->getPropertyById($id);
        $this->authorizeBrokerOwnership($request, $property->broker_id);
    }

    private function authorizeBrokerOwnership(Request $request, int $brokerId): void
    {
        $broker = \App\Models\Broker::findOrFail($brokerId);
        abort_unless($request->user()->role === 'admin' || $broker->user_id === $request->user()->id, 403);
    }
}
