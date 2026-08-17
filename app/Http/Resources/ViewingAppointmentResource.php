<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewingAppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'property_id' => $this->property_id,
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'notes' => $this->notes,
            'property' => PropertyResource::make($this->whenLoaded('property')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
