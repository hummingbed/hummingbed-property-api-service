<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Models\Amenity;
use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\ViewingAppointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketplaceController extends BaseController
{
    public function amenities(): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => Amenity::orderBy('name')->get()]);
    }

    public function createAmenity(Request $request): JsonResponse
    {
        $this->requireAdmin($request);
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:amenities,name']]);
        $amenity = Amenity::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);

        return response()->json(['status' => 'success', 'data' => $amenity], 201);
    }

    public function syncAmenities(Request $request, int $property): JsonResponse
    {
        $listing = $this->ownedProperty($request, $property);
        $data = $request->validate(['amenity_ids' => ['present', 'array'], 'amenity_ids.*' => ['integer', 'exists:amenities,id']]);
        $listing->amenities()->sync($data['amenity_ids']);

        return response()->json(['status' => 'success', 'data' => $listing->load('amenities')->amenities]);
    }

    public function addImage(Request $request, int $property): JsonResponse
    {
        $listing = $this->ownedProperty($request, $property);
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        if ($data['is_primary'] ?? false) {
            $listing->images()->update(['is_primary' => false]);
        }
        $image = $listing->images()->create($data);

        return response()->json(['status' => 'success', 'data' => $image], 201);
    }

    public function deleteImage(Request $request, int $property, int $image): JsonResponse
    {
        $listing = $this->ownedProperty($request, $property);
        $listing->images()->findOrFail($image)->delete();

        return response()->json(['status' => 'success', 'data' => null]);
    }

    public function favorites(Request $request): JsonResponse
    {
        $properties = Property::whereHas('favorites', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['broker', 'characteristic', 'images', 'amenities'])->paginate(10);

        return response()->json(['status' => 'success', 'data' => PropertyResource::collection($properties)->response()->getData(true)]);
    }

    public function addFavorite(Request $request, int $property): JsonResponse
    {
        Property::findOrFail($property);
        Favorite::firstOrCreate(['user_id' => $request->user()->id, 'property_id' => $property]);

        return response()->json(['status' => 'success', 'message' => 'Property saved successfully'], 201);
    }

    public function removeFavorite(Request $request, int $property): JsonResponse
    {
        Favorite::where(['user_id' => $request->user()->id, 'property_id' => $property])->delete();

        return response()->json(['status' => 'success', 'message' => 'Property removed from saved listings']);
    }

    public function createInquiry(Request $request, int $property): JsonResponse
    {
        Property::findOrFail($property);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email'],
            'phone_number' => ['nullable', 'string', 'max:30'], 'message' => ['required', 'string', 'max:5000'],
        ]);
        $inquiry = Inquiry::create($data + ['property_id' => $property, 'user_id' => auth('sanctum')->id()]);

        return response()->json(['status' => 'success', 'data' => $inquiry], 201);
    }

    public function inquiries(Request $request): JsonResponse
    {
        $query = Inquiry::with('property');
        $request->user()->role === 'admin'
            ? $query
            : $query->whereHas('property.broker', fn ($q) => $q->where('user_id', $request->user()->id));

        return response()->json(['status' => 'success', 'data' => $query->latest()->paginate(20)]);
    }

    public function updateInquiry(Request $request, int $inquiry): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,contacted,closed']]);
        $record = Inquiry::with('property.broker')->findOrFail($inquiry);
        abort_unless($request->user()->role === 'admin' || $record->property->broker->user_id === $request->user()->id, 403);
        $record->update($data);

        return response()->json(['status' => 'success', 'data' => $record]);
    }

    public function appointments(Request $request): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => ViewingAppointment::with('property')->where('user_id', $request->user()->id)->latest()->paginate(20)]);
    }

    public function createAppointment(Request $request, int $property): JsonResponse
    {
        Property::findOrFail($property);
        $data = $request->validate(['scheduled_at' => ['required', 'date', 'after:now'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $appointment = ViewingAppointment::create($data + ['property_id' => $property, 'user_id' => $request->user()->id]);

        return response()->json(['status' => 'success', 'data' => $appointment], 201);
    }

    public function brokerAppointments(Request $request): JsonResponse
    {
        $query = ViewingAppointment::with(['property', 'property.broker']);
        if ($request->user()->role !== 'admin') {
            $query->whereHas('property.broker', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        return response()->json(['status' => 'success', 'data' => $query->latest('scheduled_at')->paginate(20)]);
    }

    public function updateAppointment(Request $request, int $appointment): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:confirmed,completed,cancelled']]);
        $viewing = ViewingAppointment::with('property.broker')->findOrFail($appointment);
        abort_unless($request->user()->role === 'admin' || $viewing->property->broker->user_id === $request->user()->id, 403);
        $viewing->update($data);

        return response()->json(['status' => 'success', 'data' => $viewing]);
    }

    public function cancelAppointment(Request $request, int $appointment): JsonResponse
    {
        $viewing = ViewingAppointment::where('user_id', $request->user()->id)->findOrFail($appointment);
        $viewing->update(['status' => 'cancelled']);

        return response()->json(['status' => 'success', 'data' => $viewing]);
    }

    private function ownedProperty(Request $request, int $id): Property
    {
        $property = Property::with('broker')->findOrFail($id);
        abort_unless($request->user()->role === 'admin' || $property->broker->user_id === $request->user()->id, 403);

        return $property;
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->user()->role === 'admin', 403);
    }
}
