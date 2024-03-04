<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessages;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

/**
 * @group User Endpoints
 *
 * APIs for managing users
 */
class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function getUsers()
    {
        $users = $this->userService->getUsers();
        $data = UserResource::collection($users);
        return
        $this->successHttpMessage(
            'data',
            $data,
            ResponseMessages::getSuccessMessage('User', 'retrieved'),
            200
        );
    }
}