<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacteristicsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            "price"=> $this->price,
            "bedrooms"=> $this->bedrooms,
            "bathrooms"=> $this->bathrooms,
            "sq_ft"=> $this->square_feet,
            "price_per_square_feet"=> $this->price_square_feet,
            "property_type"=> $this->property_type,
            "status"=> $this->status,
        ];
    }
}
