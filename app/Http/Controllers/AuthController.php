<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->successResponse(new AuthResource($this->authService->register($request->validated())), status: 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->successResponse(new AuthResource($this->authService->login($request->validated())));
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($this->authService->currentUser($request->user())));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(message: 'Logged out successfully');
    }
}
