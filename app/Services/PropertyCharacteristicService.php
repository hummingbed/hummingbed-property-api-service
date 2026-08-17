<?php

namespace App\Services;

use App\Repositories\PropertyCharacteristicRepository;
use Illuminate\Support\Arr;

class PropertyCharacteristicService extends BaseService
{
    public function __construct(PropertyCharacteristicRepository $repository)
    {
        $this->repo = $repository;
    }

    public function createPropertyCharacteristics(array $attributes, $property)
    {
        return $this->repo->insert(Arr::only($attributes, [
            'price', 'bedrooms', 'bathrooms', 'square_feet', 'price_square_feet', 'property_type', 'status',
        ]) + ['property_id' => $property->id]);
    }

    public function updatePropertyCharacteristics(array $attributes, $property)
    {
        return $property->characteristic()->update($attributes);
    }
}
