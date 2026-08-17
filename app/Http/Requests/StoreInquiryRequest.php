<?php

namespace App\Http\Requests;

class StoreInquiryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
