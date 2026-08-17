<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'message' => $this->message,
            'status' => $this->status,
            'property' => PropertyResource::make($this->whenLoaded('property')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
