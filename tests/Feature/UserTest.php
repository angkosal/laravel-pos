<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase; // This will ensure that the database is migrated before each test

    /** @test */
    public function test_user_can_register()
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

    public function test_user_can_login_with_valid_credentials()
    {
        // Create a user instance
        $user = User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
        ]);

        // Try logging in with an empty password
        $this->assertTrue($user->login('admin123')); // Should return false
    }


    public function test_login_unsuccessful_wrong_password()
    {
        $user = User::create([
            'first_name' => 'aze',
            'last_name' => 'aze',
            'email' => 'aze@email.com',
            'password' => Hash::make('123'),
        ]);

        // Try logging in with an incorrect password
        $this->assertFalse($user->login('wrongpassword')); // Should return false
    }

    public function test_login_unsuccessful_empty_password()
    {
        $user = User::create([
            'first_name' => 'aze',
            'last_name' => 'aze',
            'email' => 'aze@email.com',
            'password' => Hash::make('123'),
        ]);

        // Try logging in with an incorrect password
        $this->assertFalse($user->login('   ')); // Should return false
    }

    // public function test_email_must_be_unique(){
    //     $user = User::create([
    //         'first_name' => 'aze',
    //         'last_name' => 'aze',
    //         'email' => 'aze@email.com',
    //         'password' => Hash::make('123'),
    //     ]);

    //     $this->assertFalse($user->login('')); // Should return false

    // }

  
}
