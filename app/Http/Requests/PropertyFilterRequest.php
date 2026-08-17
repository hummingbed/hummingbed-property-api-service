<?php

namespace App\Http\Requests;

class PropertyFilterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'city' => ['sometimes', 'string', 'max:100'],
            'listing_type' => ['sometimes', 'string'],
            'property_type' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'min_price' => ['sometimes', 'integer', 'min:0'],
            'max_price' => ['sometimes', 'integer', 'gte:min_price'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'amenity' => ['sometimes', 'string', 'max:100'],
            'featured' => ['sometimes', 'boolean'],
        ];
    }
}
