<?php

namespace App\Services;

use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;
use App\Models\Broker;
use App\Models\Property;
use App\Models\User;
use App\Repositories\PropertyRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PropertyService extends BaseService
{
    protected PropertyCharacteristicService $propertyCharacteristicService;

    public function __construct(PropertyRepository $repository, PropertyCharacteristicService $propertyCharacteristicService)
    {
        $this->repo = $repository;
        $this->propertyCharacteristicService = $propertyCharacteristicService;
    }

    public function allProperties(array $filters = [])
    {
        return $this->repo->search($filters);
    }

    public function createProperty(array $attributes, User $user): Property
    {
        return DB::transaction(function () use ($attributes, $user) {
            $this->authorizeBroker($attributes['broker_id'], $user);

            $property = $this->repo->insert(Arr::only($attributes, [
                'broker_id', 'address', 'listing_type', 'city', 'zip_code', 'description', 'build_year',
            ]));

            $this->propertyCharacteristicService->createPropertyCharacteristics($attributes, $property);

            return $property->load(['broker', 'characteristic', 'images', 'amenities']);
        });
    }

    public function updateProperty(array $attributes, int $id, User $user): Property
    {
        return DB::transaction(function () use ($attributes, $id, $user) {
            $property = $this->getPropertyById($id);
            $this->authorizeBroker($property->broker_id, $user);

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

    public function deleteProperty(int $id, User $user): void
    {
        $property = $this->getPropertyById($id);
        $this->authorizeBroker($property->broker_id, $user);
        $property->delete();
    }

    private function authorizeBroker(int $brokerId, User $user): void
    {
        $broker = Broker::findOrFail($brokerId);
        abort_unless($user->role === 'admin' || $broker->user_id === $user->id, 403);
    }
}
