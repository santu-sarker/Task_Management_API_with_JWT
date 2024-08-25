<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    /** @test */
    public function a_user_can_login_with_valid_credentials()
    {
        // Create a user first
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    /** @test */
    public function a_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid Credentials']);
    }

    /** @test */
    public function a_user_can_logout()
    {
        // Create and login a user
        $user = User::factory()->create();
        $token = $this->actingAs($user, 'api')->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    /** @test */
    public function a_protected_route_requires_authentication()
    {
        $response = $this->getJson('/api/auth/test');

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated.']);
    }

    /** @test */
    public function an_authenticated_user_can_access_protected_routes()
    {
        // Create and login a user
        $user = User::factory()->create();
        $token = $this->actingAs($user, 'api')->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/test');

        $response->assertStatus(200);
        $response->assertJson(['data' => 'success']);
    }
}
