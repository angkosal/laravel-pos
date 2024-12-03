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
        
        // $newCustomer = Customer::create('/admin/customers', [

        //     'first_name' => 'aze',
        //     'last_name' => 'aze',
        //     'email' => 'aze@gmail.com',
        //     'phone' => 456789,
        //     'address' => 'myhope',
        //     'avatar' => '/public/images/logo.png',

        // ]);

        // $admin = User::create([
        //     'first_name' => 'z',
        //     'last_name' => 'z',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('admin123'), // or use an appropriate admin password
        // ]);

        // // Acting as the admin user
        // $this->actingAs($admin);


        $response = $this->postJson('/admin/customers', [
            'first_name' => 'aze',
            'last_name' => 'aze',
            'email' => 'aze@gmail.com',
            'phone' => 456789,
            'address' => 'myhope',
            'avatar' => 'logo.png',

        ]);

        $response->assertStatus(201);

        $response->assertDatabaseHas('customers', [
            'email' => 'aze@gmail.com'
        ]);

        
    }
}
