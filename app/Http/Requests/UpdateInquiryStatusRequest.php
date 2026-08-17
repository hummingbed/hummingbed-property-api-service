<?php

namespace App\Http\Requests;

class UpdateInquiryStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return ['status' => ['required', 'in:new,contacted,closed']];
    }
}
