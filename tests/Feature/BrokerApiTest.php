<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\Property;
use App\Models\PropertyCharacteristic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BrokerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_broker_account_can_create_only_one_profile(): void
    {
        $user = User::factory()->create(['role' => 'broker']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/brokers', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id);

        $this->postJson('/api/v1/brokers', $this->payload(['phone_number' => '+2348099999999']))
            ->assertUnprocessable();
        $this->assertDatabaseCount('brokers', 1);
    }

    public function test_customer_cannot_create_a_broker_profile(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'customer']));

        $this->postJson('/api/v1/brokers', $this->payload())->assertForbidden();
    }

    public function test_owner_can_update_profile_without_triggering_its_own_phone_uniqueness(): void
    {
        $owner = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $owner->id, 'phone_number' => '+2348012345678']);
        Sanctum::actingAs($owner);

        $this->patchJson("/api/v1/brokers/{$broker->id}", [
            'name' => 'Updated Realty',
            'phone_number' => '+2348012345678',
        ])->assertOk()->assertJsonPath('data.name', 'Updated Realty');
    }

    public function test_another_user_cannot_update_or_delete_a_broker(): void
    {
        $owner = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $owner->id]);
        Sanctum::actingAs(User::factory()->create(['role' => 'broker']));

        $this->patchJson("/api/v1/brokers/{$broker->id}", ['name' => 'Stolen'])->assertForbidden();
        $this->deleteJson("/api/v1/brokers/{$broker->id}")->assertForbidden();
        $this->assertDatabaseHas('brokers', ['id' => $broker->id]);
    }

    public function test_deleting_broker_cascades_to_properties_and_characteristics(): void
    {
        $owner = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $owner->id]);
        $property = Property::factory()->create(['broker_id' => $broker->id]);
        PropertyCharacteristic::factory()->create(['property_id' => $property->id]);
        Sanctum::actingAs($owner);

        $this->deleteJson("/api/v1/brokers/{$broker->id}")->assertOk();
        $this->assertDatabaseMissing('brokers', ['id' => $broker->id]);
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('property_characteristics', ['property_id' => $property->id]);
    }

    public function test_admin_can_manage_another_users_broker_profile(): void
    {
        $broker = Broker::factory()->create(['user_id' => User::factory()->create(['role' => 'broker'])->id]);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->patchJson("/api/v1/brokers/{$broker->id}", ['city' => 'Abuja'])
            ->assertOk()->assertJsonPath('data.city', 'Abuja');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Hummingbed Realty',
            'address' => '15 Admiralty Way',
            'city' => 'Lagos',
            'zip_code' => '101233',
            'phone_number' => '+2348012345678',
        ], $overrides);
    }
}
