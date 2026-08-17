<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBrokerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'phone_number' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('brokers', 'phone_number')->ignore($this->route('broker') ?? $this->route('id')),
            ],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'zip_code' => ['sometimes', 'required', 'string', 'max:20'],
            'logo' => ['sometimes', 'nullable', 'url', 'max:2048'],
        ];
    }
}
