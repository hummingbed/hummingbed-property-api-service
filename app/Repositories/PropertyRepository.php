<?php

namespace App\Repositories;

use App\Models\Property;

class PropertyRepository extends BaseRepository
{
    public function getModel(): Property
    {
        return new Property();
    }
}