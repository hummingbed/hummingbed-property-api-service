<?php

namespace Tests\Feature;

use App\Enums\ListingTypeEnum;
use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
use App\Models\Broker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PropertyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_created_with_all_characteristics(): void
    {
        $broker = $this->ownedBroker();

        $response = $this->postJson('/api/v1/properties', $this->propertyPayload($broker->id));

        $response
            ->assertCreated()
            ->assertJsonPath('data.characteristics.bathrooms', 4)
            ->assertJsonPath('data.characteristics.status', PropertyStatusEnum::SALE->value)
            ->assertJsonPath('data.broker.id', $broker->id);

        $this->assertDatabaseHas('property_characteristics', [
            'bathrooms' => 4,
            'status' => PropertyStatusEnum::SALE->value,
        ]);
    }

    public function test_properties_can_be_filtered_and_paginated(): void
    {
        $broker = $this->ownedBroker();
        $this->postJson('/api/v1/properties', $this->propertyPayload($broker->id))->assertCreated();

        $this->getJson('/api/v1/properties?city=Lagos&min_price=10000000&per_page=5')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.meta.per_page', 5);
    }

    public function test_property_can_be_updated_and_deleted_with_its_characteristics(): void
    {
        $broker = $this->ownedBroker();
        $propertyId = $this->postJson('/api/v1/properties', $this->propertyPayload($broker->id))
            ->assertCreated()
            ->json('data.id');

        $this->patchJson("/api/v1/properties/{$propertyId}", [
            'price' => 90_000_000,
            'bathrooms' => 5,
        ])->assertOk()->assertJsonPath('data.characteristics.price', 90_000_000);

        $this->deleteJson("/api/v1/properties/{$propertyId}")->assertOk();
        $this->assertDatabaseMissing('properties', ['id' => $propertyId]);
        $this->assertDatabaseMissing('property_characteristics', ['property_id' => $propertyId]);
    }

    public function test_missing_property_returns_not_found(): void
    {
        $this->getJson('/api/v1/properties/999')->assertNotFound();
    }

    private function propertyPayload(int $brokerId): array
    {
        return [
            'broker_id' => $brokerId,
            'address' => '12 Admiralty Way',
            'listing_type' => ListingTypeEnum::OPEN_LISTING->value,
            'city' => 'Lagos',
            'zip_code' => '101233',
            'description' => 'A spacious property in a central location.',
            'build_year' => 2020,
            'price' => 75_000_000,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'square_feet' => 2500,
            'price_square_feet' => 30000,
            'property_type' => PropertyTypeEnum::DUPLEX->value,
            'status' => PropertyStatusEnum::SALE->value,
        ];
    }

    private function ownedBroker(): Broker
    {
        $user = User::factory()->create(['role' => 'broker']);
        Sanctum::actingAs($user);

        return Broker::factory()->create(['user_id' => $user->id]);
    }
}
