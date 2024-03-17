<?php

namespace App\Http\Requests;


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
            "address"=> ["required","string","max:255", "unique:properties"],
            "listing_type"=> ["required"],
            "city"=> ["required"],
            "zip_code"=> ["required", "numeric"],
            "description"=> ["required"],
            "build_year"=> ["required"],
            "price"=> ["required"],
            "bedrooms"=> ["required"],
            "bathrooms"=> ["required"],
            "square_feet"=> ["required"],
            "price_square_feet"=> ["required"],
            "property_type"=> ["required"],
            "status"=> ["required"],
            "broker_id"=> ["required"],
        ];
    }
}
