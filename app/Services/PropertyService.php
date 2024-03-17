<?php

namespace App\Services;

use App\Repositories\PropertyRepository;
use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseMessages;

class PropertyService extends BaseService
{
    protected PropertyCharacteristicService $propertyCharacteristicService;
    protected BrokerService $brokerService;

    public function __construct(PropertyRepository $repository, BrokerService $brokerService, PropertyCharacteristicService $propertyCharacteristicService)
    {
        $this->repo = $repository;
        $this->propertyCharacteristicService = $propertyCharacteristicService;
        $this->brokerService = $brokerService;
    }

    public function allProperties()
    {
        return $this->repo->findAll([], 'characteristic');
    }

    private function checkBrokerExist($request)
    {
        return $this->brokerService->getBrokerById($request->broker_id);
    }

    public function storePropertiesWithCharacteristics($request)
    {
        $this->checkBrokerExist($request);
       
        $property = $this->repo->insert([
            'broker_id'=> $request->broker_id,
            'address'=> $request->address,
            'listing_type'=> $request->listing_type,
            'city'=> $request->city,
            'zip_code'=> $request->zip_code,
            'description'=> $request->description,
            'build_year'=> $request->build_year
        ]);

        $this->propertyCharacteristicService->createPropertyCharacteristics($request, $property);
    }

    public function updateProperties($request)
    {

    }

    public function getPropertyById($id)
    {
        return $this->repo->findById($id);
    }
}