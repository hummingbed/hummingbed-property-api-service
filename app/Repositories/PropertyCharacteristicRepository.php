<?php

namespace App\Repositories;

use App\Models\PropertyCharacteristic;

class PropertyCharacteristicRepository extends BaseRepository
{
    public function getModel(): PropertyCharacteristic
    {
        return new PropertyCharacteristic();
    }
}





