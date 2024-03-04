<?php

namespace App\Models;

class User extends BaseModel
{
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
    ];   
}