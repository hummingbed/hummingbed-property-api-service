<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrokerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'logo' => $this->logo,
        ];
    }
}
