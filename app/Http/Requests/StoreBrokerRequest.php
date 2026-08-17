<?php

namespace App\Http\Requests;

class StoreBrokerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'address' => ['required', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30', 'unique:brokers,phone_number'],
            'city' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
