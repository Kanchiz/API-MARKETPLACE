<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'Accept' => 'application/json',
        ]);
    }

    public function test_user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'Usuario Prueba',
            'email' => 'qa_test@marketplace.com',
            'password' => 'password123',
            'phone' => '6671112233',
            'role' => 'buyer',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'phone', 'role', 'createdAt'],
                'token',
                'tokenType',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'qa_test@marketplace.com',
        ]);
    }

    public function test_cannot_register_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Usuario Existente',
            'email' => 'duplicado@marketplace.com',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
        ]);

        $payload = [
            'name' => 'Otro Usuario',
            'email' => 'duplicado@marketplace.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'El correo ya esta registrado.',
            ]);
    }

    public function test_register_validation_fails_with_unprocessable_entity(): void
    {
        $payload = [
            'name' => '',
            'email' => 'email_invalido',
            'password' => '123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        User::create([
            'name' => 'Usuario Login',
            'email' => 'login_valido@marketplace.com',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
        ]);

        $payload = [
            'email' => 'login_valido@marketplace.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'phone', 'role'],
                'token',
                'tokenType',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Usuario Test',
            'email' => 'user@marketplace.com',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
        ]);

        $payload = [
            'email' => 'user@marketplace.com',
            'password' => 'wrong_password',
        ];

        $response = $this->postJson('/api/v1/auth/login', $payload);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Credenciales inválidas.',
            ]);
    }
}