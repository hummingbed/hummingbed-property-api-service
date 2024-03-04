<?php

namespace App\Models;



class PropertyCharacteristic extends BaseModel
{
    protected $fillable = [
        "property_id",
        "price",
        "bedrooms",
        "bathrooms",
        "square_feet",
        "price_square_feet",
        "property_type",
    ];
}
