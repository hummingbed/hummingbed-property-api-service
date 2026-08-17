<?php

namespace App\Repositories;

use App\Models\Property;

class PropertyRepository extends BaseRepository
{
    public function getModel(): Property
    {
        return new Property();
    }

    public function search(array $filters)
    {
        return $this->getModel()
            ->with(['broker', 'characteristic', 'images', 'amenities'])
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city', 'like', "%{$city}%"))
            ->when($filters['listing_type'] ?? null, fn ($query, $type) => $query->where('listing_type', $type))
            ->when($filters['property_type'] ?? null, fn ($query, $type) => $query->whereHas('characteristic', fn ($q) => $q->where('property_type', $type)))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->whereHas('characteristic', fn ($q) => $q->where('status', $status)))
            ->when($filters['min_price'] ?? null, fn ($query, $price) => $query->whereHas('characteristic', fn ($q) => $q->where('price', '>=', $price)))
            ->when($filters['max_price'] ?? null, fn ($query, $price) => $query->whereHas('characteristic', fn ($q) => $q->where('price', '<=', $price)))
            ->when($filters['amenity'] ?? null, fn ($query, $amenity) => $query->whereHas('amenities', fn ($q) => $q->where('slug', $amenity)))
            ->when(array_key_exists('featured', $filters), fn ($query) => $query->where('is_featured', $filters['featured']))
            ->latest('id')
            ->paginate(min((int) ($filters['per_page'] ?? self::PAGINATION), 100));
    }
}
