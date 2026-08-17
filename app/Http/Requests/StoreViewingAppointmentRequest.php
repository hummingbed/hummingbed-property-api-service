<?php

namespace App\Http\Requests;

class StoreViewingAppointmentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
