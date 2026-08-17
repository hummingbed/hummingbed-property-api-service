<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Broker;
use App\Models\Property;
use App\Models\PropertyCharacteristic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ada Customer', 'email' => 'ada@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertCreated()->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_default_amenities_are_seeded_and_publicly_available(): void
    {
        $this->seed();

        $this->getJson('/api/v1/amenities')->assertOk()->assertJsonCount(6, 'data');
    }

    public function test_broker_can_manage_images_and_amenities_on_owned_property(): void
    {
        [$user, $property] = $this->listing();
        Sanctum::actingAs($user);
        $amenity = Amenity::create(['name' => '24-hour power', 'slug' => '24-hour-power']);

        $this->postJson("/api/v1/properties/{$property->id}/images", [
            'url' => 'https://example.com/property.jpg', 'is_primary' => true,
        ])->assertCreated()->assertJsonPath('data.is_primary', true);

        $this->putJson("/api/v1/properties/{$property->id}/amenities", [
            'amenity_ids' => [$amenity->id],
        ])->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_customer_can_favorite_inquire_and_schedule_a_viewing(): void
    {
        [, $property] = $this->listing();
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);

        $this->postJson("/api/v1/properties/{$property->id}/favorite")->assertCreated();
        $this->getJson('/api/v1/favorites')->assertOk()->assertJsonCount(1, 'data.data');
        $this->postJson("/api/v1/properties/{$property->id}/inquiries", [
            'name' => $customer->name, 'email' => $customer->email, 'message' => 'Is this still available?',
        ])->assertCreated();
        $this->postJson("/api/v1/properties/{$property->id}/appointments", [
            'scheduled_at' => now()->addDay()->toISOString(), 'notes' => 'Afternoon preferred',
        ])->assertCreated();
    }

    public function test_customer_cannot_modify_another_brokers_property(): void
    {
        [, $property] = $this->listing();
        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/v1/properties/{$property->id}/images", [
            'url' => 'https://example.com/property.jpg',
        ])->assertForbidden();
    }

    private function listing(): array
    {
        $user = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $user->id]);
        $property = Property::factory()->create(['broker_id' => $broker->id]);
        PropertyCharacteristic::factory()->create(['property_id' => $property->id]);

        return [$user, $property];
    }
}
