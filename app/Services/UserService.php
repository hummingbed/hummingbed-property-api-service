<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        $this->repo = $repository;
    }

    public function getUsers()
    {
        return $this->repo->findAll();
    }
}
