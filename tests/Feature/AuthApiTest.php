<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_customer_by_default_and_hashes_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()
            ->assertJsonPath('data.user.role', 'customer')
            ->assertJsonMissingPath('data.user.password');

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_registration_rejects_duplicate_email_invalid_role_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Duplicate User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'role' => 'admin',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'role']);
    }

    public function test_login_me_and_logout_token_lifecycle(): void
    {
        User::factory()->create(['email' => 'ada@example.com', 'password' => 'password123']);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'ada@example.com',
            'password' => 'password123',
        ])->assertOk()->json('data.token');

        $headers = ['Authorization' => "Bearer {$token}"];
        $this->withHeaders($headers)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'ada@example.com');

        $this->withHeaders($headers)->postJson('/api/v1/auth/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        app('auth')->forgetGuards();
        $this->withHeaders($headers)->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'ada@example.com', 'password' => 'password123']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ada@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
