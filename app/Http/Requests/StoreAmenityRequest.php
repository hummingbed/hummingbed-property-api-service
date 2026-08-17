<?php

namespace App\Http\Requests;

class StoreAmenityRequest extends BaseRequest
{
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:100', 'unique:amenities,name']];
    }
}
