<?php

namespace App\Services;

use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;
use App\Repositories\BrokerRepository;

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

    public function saveBroker($request, int $userId)
    {
        return $this->repo->insert($request->validated() + ['user_id' => $userId]);
    }

    public function updateBrokerById($request, $id)
    {
        $broker = $this->getBrokerById($id);
        $broker->update($request->validated());

        return $broker->refresh();
    }

    public function deleteBrokerById($id)
    {
        return $this->getBrokerById($id)->delete();
    }
}
