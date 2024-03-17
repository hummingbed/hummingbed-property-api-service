<?php

namespace App\Http\Resources;

use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "attributes" => [
                "address" => $this->address,
                "listing_type" => $this->listing_type,
                "city" => $this->city,
                "zip_code"=> $this->zip_code,
                "description"=> $this->description,
                "build_year"=> $this->build_year,
            ],
            "characteristics"=> $this->characteristic,
            "broker"=> $this->broker($this->broker_id)
        ];
    }

    private function broker($id)
    {
        $broker = Broker::find($id);
        return [
            "name" => $broker->name,
            "address" => $broker->address,
            "phone_number" => $broker->phone_number,
        ];
    }
}
