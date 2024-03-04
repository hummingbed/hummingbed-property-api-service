<?php

namespace App\Services;

use App\Repositories\BrokerRepository;
use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;

class BrokerService extends BaseService
{
    public function __construct(BrokerRepository $repository)
    {
        $this->repo = $repository;
    }

    public function getBrokers()
    {
        return $this->repo->findAll();
    }

    public function getBrokerById($id)
    {
        $broker = $this->repo->findById($id);
        throw_unless($broker, new EntityNotFoundException(ResponseMessages::notFoundErrorMessage("Broker id $id")));
        return $broker;
    }

    public function saveBroker($request)
    {
        return $this->repo->insert([
            "name" => $request->name,
            "address" => $request->address,
            "phone_number" => $request->phone_number,
            "city" => $request->city,
            "zip_code" => $request->zip_code,
            "logo_path" => $request->logo_path,
        ]);
    }
}