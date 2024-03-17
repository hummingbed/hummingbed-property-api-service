<?php

namespace App\Services;

use App\Repositories\PropertyCharacteristicRepository;

class PropertyCharacteristicService extends BaseService
{
    public function __construct(PropertyCharacteristicRepository $repository)
    {
        $this->repo = $repository;
    }

    public function createPropertyCharacteristics($request, $property)
    {
        return $this->repo->insert([
            "property_id"=> $property->id,
            "price"=> $request->price,
            "bedrooms"=> $request->bedrooms,
            "square_feet"=> $request->square_feet,
            "price_square_feet"=> $request->price_square_feet,
            "property_type"=> $request->property_type,
            "status"=> $request->status,
        ]);
    }
}



