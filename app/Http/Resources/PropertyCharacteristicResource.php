<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCharacteristicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'square_feet' => $this->square_feet,
            'price_square_feet' => $this->price_square_feet,
            'property_type' => $this->property_type,
            'status' => $this->status,
        ];
    }
}
