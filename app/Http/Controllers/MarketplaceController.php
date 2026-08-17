<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\StorePropertyImageRequest;
use App\Http\Requests\StoreViewingAppointmentRequest;
use App\Http\Requests\SyncAmenitiesRequest;
use App\Http\Requests\UpdateInquiryStatusRequest;
use App\Http\Requests\UpdateViewingAppointmentStatusRequest;
use App\Http\Resources\AmenityResource;
use App\Http\Resources\InquiryResource;
use App\Http\Resources\PropertyImageResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\ViewingAppointmentResource;
use App\Services\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceController extends BaseController
{
    public function __construct(private readonly MarketplaceService $marketplaceService)
    {
    }

    public function amenities(): JsonResponse
    {
        return $this->successResponse(AmenityResource::collection($this->marketplaceService->amenities()));
    }

    public function createAmenity(StoreAmenityRequest $request): JsonResponse
    {
        $amenity = $this->marketplaceService->createAmenity($request->validated(), $request->user());

        return $this->successResponse(new AmenityResource($amenity), status: 201);
    }

    public function syncAmenities(SyncAmenitiesRequest $request, int $property): JsonResponse
    {
        $amenities = $this->marketplaceService->syncAmenities($request->validated('amenity_ids'), $property, $request->user());

        return $this->successResponse(AmenityResource::collection($amenities));
    }

    public function addImage(StorePropertyImageRequest $request, int $property): JsonResponse
    {
        $image = $this->marketplaceService->addImage($request->validated(), $property, $request->user());

        return $this->successResponse(new PropertyImageResource($image), status: 201);
    }

    public function deleteImage(Request $request, int $property, int $image): JsonResponse
    {
        $this->marketplaceService->deleteImage($property, $image, $request->user());

        return $this->successResponse();
    }

    public function favorites(Request $request): JsonResponse
    {
        $properties = $this->marketplaceService->favorites($request->user());

        return $this->successResponse(PropertyResource::collection($properties)->response()->getData(true));
    }

    public function addFavorite(Request $request, int $property): JsonResponse
    {
        $this->marketplaceService->addFavorite($property, $request->user());

        return $this->successResponse(message: 'Property saved successfully', status: 201);
    }

    public function removeFavorite(Request $request, int $property): JsonResponse
    {
        $this->marketplaceService->removeFavorite($property, $request->user());

        return $this->successResponse(message: 'Property removed from saved listings');
    }

    public function createInquiry(StoreInquiryRequest $request, int $property): JsonResponse
    {
        $inquiry = $this->marketplaceService->createInquiry($request->validated(), $property, auth('sanctum')->user());

        return $this->successResponse(new InquiryResource($inquiry), status: 201);
    }

    public function inquiries(Request $request): JsonResponse
    {
        $inquiries = $this->marketplaceService->inquiries($request->user());

        return $this->successResponse(InquiryResource::collection($inquiries)->response()->getData(true));
    }

    public function updateInquiry(UpdateInquiryStatusRequest $request, int $inquiry): JsonResponse
    {
        $record = $this->marketplaceService->updateInquiry($request->validated(), $inquiry, $request->user());

        return $this->successResponse(new InquiryResource($record));
    }

    public function appointments(Request $request): JsonResponse
    {
        $appointments = $this->marketplaceService->appointments($request->user());

        return $this->successResponse(ViewingAppointmentResource::collection($appointments)->response()->getData(true));
    }

    public function createAppointment(StoreViewingAppointmentRequest $request, int $property): JsonResponse
    {
        $appointment = $this->marketplaceService->createAppointment($request->validated(), $property, $request->user());

        return $this->successResponse(new ViewingAppointmentResource($appointment), status: 201);
    }

    public function brokerAppointments(Request $request): JsonResponse
    {
        $appointments = $this->marketplaceService->brokerAppointments($request->user());

        return $this->successResponse(ViewingAppointmentResource::collection($appointments)->response()->getData(true));
    }

    public function updateAppointment(UpdateViewingAppointmentStatusRequest $request, int $appointment): JsonResponse
    {
        $viewing = $this->marketplaceService->updateAppointment($request->validated(), $appointment, $request->user());

        return $this->successResponse(new ViewingAppointmentResource($viewing));
    }

    public function cancelAppointment(Request $request, int $appointment): JsonResponse
    {
        $viewing = $this->marketplaceService->cancelAppointment($appointment, $request->user());

        return $this->successResponse(new ViewingAppointmentResource($viewing));
    }
}
