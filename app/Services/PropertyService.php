<?php

namespace App\Services;

use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;
use App\Repositories\PropertyRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PropertyService extends BaseService
{
    protected PropertyCharacteristicService $propertyCharacteristicService;

    protected BrokerService $brokerService;

    public function __construct(PropertyRepository $repository, BrokerService $brokerService, PropertyCharacteristicService $propertyCharacteristicService)
    {
        $this->repo = $repository;
        $this->propertyCharacteristicService = $propertyCharacteristicService;
        $this->brokerService = $brokerService;
    }

    public function allProperties(array $filters = [])
    {
        return $this->repo->search($filters);
    }

    private function checkBrokerExist($request)
    {
        return $this->brokerService->getBrokerById($request->broker_id);
    }

    public function storePropertiesWithCharacteristics($request)
    {
        return DB::transaction(function () use ($request) {
            $this->checkBrokerExist($request);

            $property = $this->repo->insert($request->safe()->only([
                'broker_id', 'address', 'listing_type', 'city', 'zip_code', 'description', 'build_year',
            ]));

            $this->propertyCharacteristicService->createPropertyCharacteristics($request, $property);

            return $property->load(['broker', 'characteristic', 'images', 'amenities']);
        });
    }

    public function updateProperty($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $property = $this->getPropertyById($id);
            $attributes = $request->validated();

            $property->update(Arr::only($attributes, [
                'broker_id', 'address', 'listing_type', 'city', 'zip_code', 'description', 'build_year',
            ]));

            $characteristics = Arr::only($attributes, [
                'price', 'bedrooms', 'bathrooms', 'square_feet', 'price_square_feet', 'property_type', 'status',
            ]);

            if ($characteristics !== []) {
                $this->propertyCharacteristicService->updatePropertyCharacteristics($characteristics, $property);
            }

            return $property->refresh()->load(['broker', 'characteristic', 'images', 'amenities']);
        });
    }

    public function getPropertyById($id)
    {
        $property = $this->repo->findByIdWith($id, ['broker', 'characteristic', 'images', 'amenities']);
        throw_unless($property, new EntityNotFoundException(ResponseMessages::notFoundErrorMessage("Property id $id")));

        return $property;
    }

    public function deleteProperty($id): void
    {
        $this->getPropertyById($id)->delete();
    }
}
