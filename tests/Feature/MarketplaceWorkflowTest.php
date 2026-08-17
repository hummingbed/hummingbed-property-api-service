<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Broker;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyCharacteristic;
use App\Models\User;
use App\Models\ViewingAppointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketplaceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_amenity_but_other_roles_cannot(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'customer']));
        $this->postJson('/api/v1/amenities', ['name' => 'Gym'])->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->postJson('/api/v1/amenities', ['name' => 'Roof Terrace'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'roof-terrace');

        $this->postJson('/api/v1/amenities', ['name' => 'Roof Terrace'])
            ->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_new_primary_image_replaces_previous_primary_and_images_can_be_deleted(): void
    {
        [$owner, $property] = $this->listing();
        Sanctum::actingAs($owner);

        $first = $this->postJson("/api/v1/properties/{$property->id}/images", [
            'url' => 'https://example.com/first.jpg', 'is_primary' => true,
        ])->assertCreated()->json('data.id');
        $second = $this->postJson("/api/v1/properties/{$property->id}/images", [
            'url' => 'https://example.com/second.jpg', 'is_primary' => true, 'sort_order' => 1,
        ])->assertCreated()->json('data.id');

        $this->assertDatabaseHas('property_images', ['id' => $first, 'is_primary' => false]);
        $this->assertDatabaseHas('property_images', ['id' => $second, 'is_primary' => true]);
        $this->deleteJson("/api/v1/properties/{$property->id}/images/{$first}")->assertOk();
        $this->assertDatabaseMissing('property_images', ['id' => $first]);
    }

    public function test_favorite_operations_are_idempotent_and_scoped_to_current_user(): void
    {
        [, $property] = $this->listing();
        $firstCustomer = User::factory()->create();
        $secondCustomer = User::factory()->create();

        Sanctum::actingAs($firstCustomer);
        $this->postJson("/api/v1/properties/{$property->id}/favorite")->assertCreated();
        $this->postJson("/api/v1/properties/{$property->id}/favorite")->assertCreated();
        $this->assertDatabaseCount('favorites', 1);

        Sanctum::actingAs($secondCustomer);
        $this->getJson('/api/v1/favorites')->assertOk()->assertJsonCount(0, 'data.data');

        Sanctum::actingAs($firstCustomer);
        $this->deleteJson("/api/v1/properties/{$property->id}/favorite")->assertOk();
        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_broker_only_sees_and_updates_inquiries_for_owned_properties(): void
    {
        [$firstOwner, $firstProperty] = $this->listing();
        [, $secondProperty] = $this->listing();
        $firstInquiry = Inquiry::create($this->inquiryData($firstProperty));
        $secondInquiry = Inquiry::create($this->inquiryData($secondProperty, 'second@example.com'));
        Sanctum::actingAs($firstOwner);

        $this->getJson('/api/v1/inquiries')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $firstInquiry->id);

        $this->patchJson("/api/v1/inquiries/{$firstInquiry->id}", ['status' => 'contacted'])
            ->assertOk()->assertJsonPath('data.status', 'contacted');
        $this->patchJson("/api/v1/inquiries/{$secondInquiry->id}", ['status' => 'closed'])
            ->assertForbidden();
    }

    public function test_public_and_authenticated_inquiries_store_expected_user_identity(): void
    {
        [, $property] = $this->listing();
        $payload = ['name' => 'Visitor', 'email' => 'visitor@example.com', 'message' => 'Please contact me.'];

        $this->postJson("/api/v1/properties/{$property->id}/inquiries", $payload)->assertCreated();
        $this->assertDatabaseHas('inquiries', ['email' => 'visitor@example.com', 'user_id' => null]);

        $customer = User::factory()->create();
        $token = $customer->createToken('test')->plainTextToken;
        $this->withToken($token)->postJson("/api/v1/properties/{$property->id}/inquiries", [
            'name' => 'Customer', 'email' => 'customer@example.com', 'message' => 'I am interested.',
        ])->assertCreated();
        $this->assertDatabaseHas('inquiries', ['email' => 'customer@example.com', 'user_id' => $customer->id]);
    }

    public function test_customer_and_broker_can_complete_viewing_workflow_with_scope_enforced(): void
    {
        [$owner, $property] = $this->listing();
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $appointmentId = $this->postJson("/api/v1/properties/{$property->id}/appointments", [
            'scheduled_at' => now()->addDay()->toISOString(),
        ])->assertCreated()->json('data.id');

        Sanctum::actingAs($owner);
        $this->getJson('/api/v1/broker/appointments')->assertOk()->assertJsonCount(1, 'data.data');
        $this->patchJson("/api/v1/broker/appointments/{$appointmentId}", ['status' => 'confirmed'])
            ->assertOk()->assertJsonPath('data.status', 'confirmed');

        $otherCustomer = User::factory()->create();
        Sanctum::actingAs($otherCustomer);
        $this->patchJson("/api/v1/appointments/{$appointmentId}/cancel")->assertNotFound();

        Sanctum::actingAs($customer);
        $this->patchJson("/api/v1/appointments/{$appointmentId}/cancel")
            ->assertOk()->assertJsonPath('data.status', 'cancelled');
    }

    public function test_past_viewing_time_and_invalid_workflow_statuses_are_rejected(): void
    {
        [$owner, $property] = $this->listing();
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);

        $this->postJson("/api/v1/properties/{$property->id}/appointments", [
            'scheduled_at' => now()->subHour()->toISOString(),
        ])->assertUnprocessable()->assertJsonValidationErrors('scheduled_at');

        $appointment = ViewingAppointment::create([
            'user_id' => $customer->id, 'property_id' => $property->id,
            'scheduled_at' => now()->addDay(), 'status' => 'pending',
        ]);
        Sanctum::actingAs($owner);
        $this->patchJson("/api/v1/broker/appointments/{$appointment->id}", ['status' => 'pending'])
            ->assertUnprocessable()->assertJsonValidationErrors('status');
    }

    public function test_properties_can_be_filtered_by_type_status_amenity_featured_and_price(): void
    {
        [, $matching] = $this->listing([
            'city' => 'Lagos', 'is_featured' => true,
        ], ['property_type' => 'duplex', 'status' => 'on sale', 'price' => 80_000_000]);
        [, $other] = $this->listing([
            'city' => 'Abuja', 'is_featured' => false,
        ], ['property_type' => 'bungalow', 'status' => 'sold', 'price' => 25_000_000]);
        $amenity = Amenity::create(['name' => 'Gym', 'slug' => 'gym']);
        $matching->amenities()->attach($amenity);

        $this->getJson('/api/v1/properties?property_type=duplex&status=on%20sale&amenity=gym&featured=1&min_price=70000000&max_price=90000000')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $matching->id)
            ->assertJsonMissing(['id' => $other->id]);
    }

    private function listing(array $propertyOverrides = [], array $characteristicOverrides = []): array
    {
        $owner = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $owner->id]);
        $property = Property::factory()->create($propertyOverrides + ['broker_id' => $broker->id]);
        PropertyCharacteristic::factory()->create($characteristicOverrides + ['property_id' => $property->id]);

        return [$owner, $property];
    }

    private function inquiryData(Property $property, string $email = 'first@example.com'): array
    {
        return [
            'property_id' => $property->id, 'name' => 'Interested Customer',
            'email' => $email, 'message' => 'Please share more information.',
        ];
    }
}
