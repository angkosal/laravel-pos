<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    public function test_add_new_customer_succesfully(): void
    {
        

        // Create a user (you may want to create an admin user with specific permissions)
        $user = User::factory()->create();
    

        // Act as the created user and send the POST request
        $response = $this->actingAs($user)->postJson('/admin/customers', [
            'first_name' => 'aze',
            'last_name' => 'aze',
            'email' => 'aze@gmail.com',
            'phone' => 456789,
            'address' => 'myhope',
            'avatar' => 'logo.png',
        ]);

        // Assert the correct status code and check the database
        $response->assertStatus(201);
        $response->assertDatabaseHas('customers', [
            'email' => 'aze@gmail.com'
        ]);

        
    }
}
