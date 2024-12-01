<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase; // This will ensure that the database is migrated before each test

    /** @test */
    public function a_user_can_register()
    {
        $data = [
            'first_name' => 'John Doe',
            'last_name' => 'John Doe',
            'email' => 'user@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'user@gmail.com',
        ]);
    }
}
