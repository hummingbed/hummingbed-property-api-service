<?php

namespace App\Http\Requests;

class SyncAmenitiesRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'amenity_ids' => ['present', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
    }
}
