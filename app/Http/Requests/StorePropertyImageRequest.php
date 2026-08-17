<?php

namespace App\Http\Requests;

class StorePropertyImageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }
}
