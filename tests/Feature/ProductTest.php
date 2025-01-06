<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
 
    public function test_add_new_product(): void
    {
          // Arrange: Data for a new user
          $data = [
            'name' => 'user',
            'description' => 'user',
            'image' => '/public/images/img-placeholder.jpg',
            'barcode' => 'user123',
            'price' => 2,
            'quantity' => 2,
            'status' => true,

           
        ];

        // Act: Make a POST request to the registration endpoint
        $response = $this->postJson('/admin/products/create', $data);
        $response->assertStatus(201);
        
    }
}
