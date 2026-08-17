<?php

namespace App\Services;

use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;
use App\Models\Broker;
use App\Models\User;
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

    public function createBroker(array $attributes, User $user): Broker
    {
        abort_unless(in_array($user->role, ['broker', 'admin'], true), 403);
        abort_if(Broker::where('user_id', $user->id)->exists(), 422, 'This account already has a broker profile.');

        return $this->repo->insert($attributes + ['user_id' => $user->id]);
    }

    public function updateBroker(array $attributes, int $id, User $user): Broker
    {
        $broker = $this->getBrokerById($id);
        $this->authorizeOwner($broker, $user);
        $broker->update($attributes);

        return $broker->refresh();
    }

    public function deleteBroker(int $id, User $user): void
    {
        $broker = $this->getBrokerById($id);
        $this->authorizeOwner($broker, $user);
        $broker->delete();
    }

    private function authorizeOwner(Broker $broker, User $user): void
    {
        abort_unless($user->role === 'admin' || $broker->user_id === $user->id, 403);
    }
}
