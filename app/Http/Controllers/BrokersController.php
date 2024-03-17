<?php

namespace App\Http\Controllers;

use App\Services\BrokerService;
use App\Helpers\ResponseMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;

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

    public function addBroker(StoreBrokerRequest $request)
    {
        $this->brokerService->saveBroker($request);
        return $this->successHttpMessage(
            'data',
            true,
            ResponseMessages::getSuccessMessage('Brokers', 'saved'),
            201
        );
    }

    public function getBrokerUsingBrokerId($id)
    {
        $broker = $this->brokerService->getBrokerById($id);
        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'retrieved'),
            200
        );
    }

    public function updateBroker(UpdateBrokerRequest $request, $id)
    {
        $broker = $this->brokerService->updateBrokerById($request, $id);
        return $this->successHttpMessage(
            'data',
            $broker,
            ResponseMessages::getSuccessMessage('Brokers', 'Updated'),
            200
        );
    }

    public function deleteBroker($id)
    {
        $this->brokerService->deleteBrokerById($id);
        return $this->successHttpMessage(
            'data',
            null,
            ResponseMessages::getSuccessMessage('Brokers', 'Deleted'),
            200
        );
    }
} 