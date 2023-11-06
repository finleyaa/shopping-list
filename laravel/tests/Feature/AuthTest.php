<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use InteractsWithDatabase, RefreshDatabase;

    /**
     * Test that the register route creates a new user.
     */
    public function testCreateUser(): void
    {
        $response = $this->post('/api/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test that the login route creates and returns a new token.
     */
    public function testLoginUser(): void
    {
        // first create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // then login as that user
        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user'
            ],
            'message'
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'auth_token',
            'tokenable_id' => $response->json('data.user.id'),
            'tokenable_type' => 'App\Models\User',
        ]);
    }

    /**
     * Test that the logout route deletes the user's token.
     */
    public function testLogoutUser(): void
    {
        // first create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        // then logout that user
        $logoutResponse = $this->post('/api/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $logoutResponse->assertStatus(200);
        // there should be no auth tokens for the user
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'auth_token',
            'tokenable_id' => $user->id,
            'tokenable_type' => 'App\Models\User',
        ]);
    }

    /**
     * Test that the update user route updates the user spend limit.
     */
    public function testUpdateUserSpendLimit(): void
    {
        // first create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'spend_limit' => 500,
        ]);

        // then update the user's spend limit
        $response = $this->actingAs($user)->patch('/api/user', [
            'spend_limit' => 1000,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
            'spend_limit' => 1000,
        ]);
    }
}
