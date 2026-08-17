<?php

namespace App\Http\Requests;

use App\Enums\ListingTypeEnum;
use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends BaseRequest
{
    public function rules(): array
    {
        $propertyId = $this->route('property') ?? $this->route('id');

        return [
            'address' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('properties')->ignore($propertyId)],
            'listing_type' => ['sometimes', 'required', Rule::enum(ListingTypeEnum::class)],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'zip_code' => ['sometimes', 'required', 'string', 'max:20'],
            'description' => ['sometimes', 'required', 'string'],
            'build_year' => ['sometimes', 'required', 'integer', 'digits:4', 'min:1800', 'max:'.now()->year],
            'broker_id' => ['sometimes', 'required', 'integer', 'exists:brokers,id'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'bedrooms' => ['sometimes', 'required', 'integer', 'min:0', 'max:100'],
            'bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'square_feet' => ['sometimes', 'required', 'integer', 'min:1'],
            'price_square_feet' => ['sometimes', 'required', 'integer', 'min:0'],
            'property_type' => ['sometimes', 'required', Rule::enum(PropertyTypeEnum::class)],
            'status' => ['sometimes', 'required', Rule::enum(PropertyStatusEnum::class)],
        ];
    }
}
