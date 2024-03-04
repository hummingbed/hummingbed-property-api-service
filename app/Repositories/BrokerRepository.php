<?php

namespace App\Repositories;

use App\Models\Broker;

class BrokerRepository extends BaseRepository
{
    public function getModel(): Broker
    {
        return new Broker();
    }
}