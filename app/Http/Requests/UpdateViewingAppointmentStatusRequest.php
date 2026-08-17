<?php

namespace App\Http\Requests;

class UpdateViewingAppointmentStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return ['status' => ['required', 'in:confirmed,completed,cancelled']];
    }
}
