<?php

namespace App\Models;

class Favorite extends BaseModel
{
    protected $fillable = ['user_id', 'property_id'];
}
