<?php

namespace App\Models;

class Property extends BaseModel
{
    protected $fillable = [
        "broker_id",
        "address",
        "listing_type",
        "city",
        "zip_code",
        "description",
        "build_year",
    ];
}
