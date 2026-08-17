<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attributes' => [
                'address' => $this->address,
                'listing_type' => $this->listing_type,
                'city' => $this->city,
                'zip_code' => $this->zip_code,
                'description' => $this->description,
                'build_year' => $this->build_year,
                'is_featured' => $this->is_featured,
                'published_at' => $this->published_at,
            ],
            'characteristics' => $this->whenLoaded('characteristic'),
            'broker' => $this->whenLoaded('broker', fn () => [
                'id' => $this->broker->id,
                'name' => $this->broker->name,
                'address' => $this->broker->address,
                'phone_number' => $this->broker->phone_number,
                'logo' => $this->broker->logo,
            ]),
            'images' => $this->whenLoaded('images'),
            'amenities' => $this->whenLoaded('amenities'),
            'is_favorite' => $this->when(auth('sanctum')->check(), fn () => $this->favorites()->where('user_id', auth('sanctum')->id())->exists()),
        ];
    }
}
