<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user signup
     */
    public function testSuccessfulSignUp()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 'client'
        ];

        $response = $this->postJson('/api/signup', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id', 'name', 'email', 'role'
                ],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role']
        ]);
    }

    /**
     * Test signup with invalid data
     */
    public function testSignUpWithInvalidData()
    {
        // Test missing required fields
        $invalidUserData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'invalid-role'
        ];

        $response = $this->postJson('/api/signup', $invalidUserData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test duplicate email registration
     */
    public function testDuplicateEmailSignUp()
    {
        // Create a user first
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'client'
        ];

        $response = $this->postJson('/api/signup', $userData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test successful login
     */
    public function testSuccessfulLogin()
    {
        // Create a user first
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make($password)
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token']);
    }

    /**
     * Test login with invalid credentials
     */
    public function testLoginWithInvalidCredentials()
    {
        // Test with non-existent email
        $invalidLoginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $invalidLoginData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'something is wrong']);
    }

    /**
     * Test login with invalid email format
     */
    public function testLoginWithInvalidEmailFormat()
    {
        $invalidEmailData = [
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $invalidEmailData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test login with short password
     */
    public function testLoginWithShortPassword()
    {
        $invalidPasswordData = [
            'email' => 'test@example.com',
            'password' => '123'
        ];

        $response = $this->postJson('/api/login', $invalidPasswordData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }
}