<?php

namespace App\Models;


class Broker extends BaseModel
{
    protected $fillable = [
        "name",
        "phone_number",
        "address",
        "city",
        "zip_code",
        "logo_path",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
