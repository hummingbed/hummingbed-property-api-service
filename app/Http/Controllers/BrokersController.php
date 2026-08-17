<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessages;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;
use App\Http\Resources\BrokerResource;
use App\Services\BrokerService;
use Illuminate\Http\JsonResponse;

class BrokersController extends BaseController
{
    protected BrokerService $brokerService;

    public function __construct(BrokerService $brokerService)
    {
        $this->brokerService = $brokerService;
    }

    public function getAllBrokers(): JsonResponse
    {
        $broker = $this->brokerService->getBrokers();

        return $this->successResponse(
            BrokerResource::collection($broker)->response()->getData(true),
            ResponseMessages::getSuccessMessage('Brokers', 'retrieved')
        );
    }

    public function addBroker(StoreBrokerRequest $request): JsonResponse
    {
        $broker = $this->brokerService->createBroker($request->validated(), $request->user());

        return $this->successResponse(new BrokerResource($broker), ResponseMessages::getSuccessMessage('Brokers', 'saved'), 201);
    }

    public function getBrokerUsingBrokerId($id): JsonResponse
    {
        $broker = $this->brokerService->getBrokerById($id);

        return $this->successResponse(new BrokerResource($broker), ResponseMessages::getSuccessMessage('Brokers', 'retrieved'));
    }

    public function updateBroker(UpdateBrokerRequest $request, $id): JsonResponse
    {
        $broker = $this->brokerService->updateBroker($request->validated(), $id, $request->user());

        return $this->successResponse(new BrokerResource($broker), ResponseMessages::getSuccessMessage('Brokers', 'Updated'));
    }

    public function deleteBroker($id): JsonResponse
    {
        $this->brokerService->deleteBroker($id, request()->user());

        return $this->successResponse(message: ResponseMessages::getSuccessMessage('Brokers', 'Deleted'));
    }
}
