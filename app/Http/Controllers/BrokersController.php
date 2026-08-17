<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseMessages;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;
use App\Models\Broker;
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

        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'retrieved'),
            200
        );
    }

    public function addBroker(StoreBrokerRequest $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['broker', 'admin'], true), 403);
        abort_if(Broker::where('user_id', $request->user()->id)->exists(), 422, 'This account already has a broker profile.');
        $broker = $this->brokerService->saveBroker($request, $request->user()->id);

        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'saved'),
            201
        );
    }

    public function getBrokerUsingBrokerId($id): JsonResponse
    {
        $broker = $this->brokerService->getBrokerById($id);

        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'retrieved'),
            200
        );
    }

    public function updateBroker(UpdateBrokerRequest $request, $id): JsonResponse
    {
        $this->authorizeBroker($request, $id);
        $broker = $this->brokerService->updateBrokerById($request, $id);

        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'Updated'),
            200
        );
    }

    public function deleteBroker($id): JsonResponse
    {
        $this->authorizeBroker(request(), $id);
        $this->brokerService->deleteBrokerById($id);

        return $this->successHttpMessage(
            'data',
            null,
            ResponseMessages::getSuccessMessage('Brokers', 'Deleted'),
            200
        );
    }

    private function authorizeBroker($request, $id): void
    {
        $broker = $this->brokerService->getBrokerById($id);
        abort_unless($request->user()->role === 'admin' || $broker->user_id === $request->user()->id, 403);
    }
}
