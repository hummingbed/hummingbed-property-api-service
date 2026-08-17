<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\Property;
use App\Models\PropertyCharacteristic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_requests_return_json_even_without_accept_header(): void
    {
        $this->get('/api/v1/favorites')
            ->assertUnauthorized()
            ->assertHeader('content-type', 'application/json')
            ->assertExactJson(['status' => 'failed', 'message' => 'Unauthenticated.', 'data' => null]);
    }

    public function test_validation_errors_use_the_standard_api_shape(): void
    {
        $this->post('/api/v1/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'failed')
            ->assertJsonPath('message', 'The given data was invalid.')
            ->assertJsonStructure(['errors' => ['name', 'email', 'password']]);
    }

    public function test_forbidden_and_not_found_errors_are_json(): void
    {
        $owner = User::factory()->create(['role' => 'broker']);
        $broker = Broker::factory()->create(['user_id' => $owner->id]);
        $property = Property::factory()->create(['broker_id' => $broker->id]);
        PropertyCharacteristic::factory()->create(['property_id' => $property->id]);
        Sanctum::actingAs(User::factory()->create());

        $this->post("/api/v1/properties/{$property->id}/images", ['url' => 'https://example.com/image.jpg'])
            ->assertForbidden()
            ->assertJsonPath('status', 'failed');

        $this->get('/api/v1/properties/999')
            ->assertNotFound()
            ->assertJsonPath('status', 'failed');
    }
}
