<?php

namespace App\Services;

use App\Models\Amenity;
use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Models\ViewingAppointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class MarketplaceService
{
    public function amenities(): Collection
    {
        return Amenity::orderBy('name')->get();
    }

    public function createAmenity(array $attributes, User $user): Amenity
    {
        $this->requireAdmin($user);

        return Amenity::create(['name' => $attributes['name'], 'slug' => Str::slug($attributes['name'])]);
    }

    public function syncAmenities(array $amenityIds, int $propertyId, User $user): Collection
    {
        $property = $this->ownedProperty($propertyId, $user);
        $property->amenities()->sync($amenityIds);

        return $property->load('amenities')->amenities;
    }

    public function addImage(array $attributes, int $propertyId, User $user): PropertyImage
    {
        $property = $this->ownedProperty($propertyId, $user);
        if ($attributes['is_primary'] ?? false) {
            $property->images()->update(['is_primary' => false]);
        }

        return $property->images()->create($attributes);
    }

    public function deleteImage(int $propertyId, int $imageId, User $user): void
    {
        $this->ownedProperty($propertyId, $user)->images()->findOrFail($imageId)->delete();
    }

    public function favorites(User $user): LengthAwarePaginator
    {
        return Property::whereHas('favorites', fn ($query) => $query->where('user_id', $user->id))
            ->with(['broker', 'characteristic', 'images', 'amenities'])->paginate(10);
    }

    public function addFavorite(int $propertyId, User $user): void
    {
        Property::findOrFail($propertyId);
        Favorite::firstOrCreate(['user_id' => $user->id, 'property_id' => $propertyId]);
    }

    public function removeFavorite(int $propertyId, User $user): void
    {
        Favorite::where(['user_id' => $user->id, 'property_id' => $propertyId])->delete();
    }

    public function createInquiry(array $attributes, int $propertyId, ?User $user): Inquiry
    {
        Property::findOrFail($propertyId);

        return Inquiry::create($attributes + ['property_id' => $propertyId, 'user_id' => $user?->id]);
    }

    public function inquiries(User $user): LengthAwarePaginator
    {
        $query = Inquiry::with('property');
        if ($user->role !== 'admin') {
            $query->whereHas('property.broker', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query->latest()->paginate(20);
    }

    public function updateInquiry(array $attributes, int $inquiryId, User $user): Inquiry
    {
        $inquiry = Inquiry::with('property.broker')->findOrFail($inquiryId);
        abort_unless($user->role === 'admin' || $inquiry->property->broker->user_id === $user->id, 403);
        $inquiry->update($attributes);

        return $inquiry;
    }

    public function appointments(User $user): LengthAwarePaginator
    {
        return ViewingAppointment::with('property')->where('user_id', $user->id)->latest()->paginate(20);
    }

    public function createAppointment(array $attributes, int $propertyId, User $user): ViewingAppointment
    {
        Property::findOrFail($propertyId);

        return ViewingAppointment::create($attributes + ['property_id' => $propertyId, 'user_id' => $user->id]);
    }

    public function brokerAppointments(User $user): LengthAwarePaginator
    {
        $query = ViewingAppointment::with(['property', 'property.broker']);
        if ($user->role !== 'admin') {
            $query->whereHas('property.broker', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query->latest('scheduled_at')->paginate(20);
    }

    public function updateAppointment(array $attributes, int $appointmentId, User $user): ViewingAppointment
    {
        $appointment = ViewingAppointment::with('property.broker')->findOrFail($appointmentId);
        abort_unless($user->role === 'admin' || $appointment->property->broker->user_id === $user->id, 403);
        $appointment->update($attributes);

        return $appointment;
    }

    public function cancelAppointment(int $appointmentId, User $user): ViewingAppointment
    {
        $appointment = ViewingAppointment::where('user_id', $user->id)->findOrFail($appointmentId);
        $appointment->update(['status' => 'cancelled']);

        return $appointment;
    }

    private function ownedProperty(int $id, User $user): Property
    {
        $property = Property::with('broker')->findOrFail($id);
        abort_unless($user->role === 'admin' || $property->broker->user_id === $user->id, 403);

        return $property;
    }

    private function requireAdmin(User $user): void
    {
        abort_unless($user->role === 'admin', 403);
    }
}
