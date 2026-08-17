<?php

namespace App\Http\Requests;

use App\Enums\ListingTypeEnum;
use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => ['required', 'string', 'max:255', 'unique:properties'],
            'listing_type' => ['required', Rule::enum(ListingTypeEnum::class)],
            'city' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string'],
            'build_year' => ['required', 'integer', 'digits:4', 'min:1800', 'max:'.now()->year],
            'price' => ['required', 'integer', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:100'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:100'],
            'square_feet' => ['required', 'integer', 'min:1'],
            'price_square_feet' => ['required', 'integer', 'min:0'],
            'property_type' => ['required', Rule::enum(PropertyTypeEnum::class)],
            'status' => ['required', Rule::enum(PropertyStatusEnum::class)],
            'broker_id' => ['required', 'integer', 'exists:brokers,id'],
        ];
    }
}
