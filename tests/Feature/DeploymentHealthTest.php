<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentHealthTest extends TestCase
{
    public function test_api_health_endpoint_is_available_without_a_database_or_authentication(): void
    {
        $this->get('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.service', 'hummingbed-property-api');
    }
}
