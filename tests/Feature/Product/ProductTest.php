<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $user = $this->user = User::factory()->create();
    $this->actingAs($user);

    Storage::fake('public');

    $this->validProductData = [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'barcode' => '1234567890123',
        'price' => '99.99',
        'quantity' => 10,
        'status' => true,
    ];
});

describe('Product Index', function () {
    test('authenticated users can view products index', function () {
        $this->actingAs($this->user)
            ->get(route('products.index'))
            ->assertViewIs('products.index')
            ->assertOk()
            ->assertViewHas('products');
    });

    test('guests cannot view products index', function () {
        auth()->logout();
        $this->get(route('products.index'))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });

    test('products are paginated', function () {
        Product::factory()->count(15)->create();

        $response = $this->get(route('products.index'));

        $response->assertViewHas('products', function ($products) {
            return $products->count() === 10;
        });
    });

    test('products are ordered by latest first', function () {
        $oldProduct = Product::factory()->create(['name' => 'Old Product']);
        $this->travel(1)->day();
        $newProduct = Product::factory()->create(['name' => 'New Product']);

        $response = $this->get(route('products.index'));

        $response->assertViewHas('products', function ($products) use ($newProduct, $oldProduct) {
            return $products->first()->id === $newProduct->id;
        });
    });
});

describe('Product Create', function () {
    test('authenticated users can view create form', function () {
        $this->get(route('products.create'))
            ->assertOk()
            ->assertSee('Create Product')
            ->assertViewIs('products.create');
    });

    test('guests cannot view create form', function () {
        auth()->logout();
        $this->get(route('products.create'))
            ->assertRedirect(route('login'));
    });
});


describe('Product Store', function () {
    test('authenticated user create a product', function () {
        $this->post(route('products.store', $this->validProductData))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success')
            ->assertStatus(302);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'barcode' => '1234567890123',
            'price' => '99.99',
            'quantity' => 10,
            'status' => true,
        ]);
    });

    test('product can be created with image', function () {
        $image = UploadedFile::fake()->image('product.jpg');
        $data = array_merge($this->validProductData, ['image' => $image]);

        $response = $this->post(route('products.store'), $data);
        $response->assertRedirect(route('products.index'));

        $product = Product::where('barcode', $this->validProductData['barcode'])->first();
        expect($product->image)
            ->not
            ->toBeNull();
        Storage::disk('public')
            ->assertExists($product->image);
    });

    test('product can be created without optional fields', function () {
        $data = [
            'name' => 'Minimal Product',
            'barcode' => '9876543210987',
            'price' => '10.50',
            'quantity' => 5,
            'status' => true,
        ];

        $this->post(route('products.store', $data))
            ->assertRedirect(route('products.index'))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Minimal Product',
            'description' => null,
            'image' => null,
        ]);
    });

    describe('Product Edit', function () {
        test('authenticated users can view edit form', function () {
            $product = Product::factory()->create();

            $this->get(route('products.edit', $product))
                ->assertOk()
                ->assertViewIs('products.edit')
                ->assertViewHas('product', $product);
        });

        test('guests cannot view edit form', function () {
            auth()->logout();
            $product = Product::factory()->create();
            $this->get(route('products.edit', $product))
                ->assertRedirect(route('login'))
                ->assertStatus(302);
        });
    });

    describe('Product Update', function () {

        test('authenticated users can update a product', function () {
            $product = Product::factory()->create();

            $updateData = [
                'name' => 'Updated Product',
                'description' => 'Updated Description',
                'barcode' => $product->barcode,
                'price' => '199.99',
                'quantity' => 20,
                'status' => false,
            ];

            $this->put(route('products.update', $product), $updateData)
                ->assertSessionHas('success')
                ->assertRedirect(route('products.index'))
                ->assertStatus(302);

            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'name' => 'Updated Product',
                'price' => '199.99',
                'quantity' => 20,
                'status' => false,
            ]);
        });

        test('product can be updated with new image', function () {
            Storage::fake('public');

            $oldImage = UploadedFile::fake()->image('old.jpg');

            $product = Product::factory()->create();
            $product->image = $oldImage->store('products', 'public');
            $product->save();

            Storage::disk('public')->assertExists($product->image);

            $newImage = UploadedFile::fake()->image('new.jpg');

            $updateData = array_merge($this->validProductData, [
                'barcode' => $product->barcode,
                'image' => $newImage,
            ]);

            $this->put(route('products.update', $product), $updateData);

            $product->refresh();

            expect($product->image)->not->toBe($oldImage->hashName());
            Storage::disk('public')->assertExists($product->image);
            Storage::disk('public')->assertMissing('products/' . $oldImage->hashName());
        });

        test('old image is deleted when updating with new image', function () {
            Storage::fake('public');

            $product = Product::factory()->create();
            $oldImagePath = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
            $product->update(['image' => $oldImagePath]);

            $newImage = UploadedFile::fake()->image('new.jpg');

            $updateData = array_merge($this->validProductData, [
                'barcode' => $product->barcode,
                'image' => $newImage,
            ]);

            $this->put(route('products.update', $product), $updateData);

            Storage::disk('public')->assertMissing($oldImagePath);
        });

    });

    describe('Product Destroy', function () {
        test('authenticated users can delete a product', function () {
            $product = Product::factory()->create();
            $this->delete(route('products.destroy', $product))
                ->assertOk();

            $this->assertDatabaseMissing('products', [
                'id' => $product->id
            ]);
        });

        test('product image is deleted when product is deleted', function () {
            $product = Product::factory()->create();
            $imagePath = UploadedFile::fake()->image('product.jpg')->store('products', 'public');
            $product->update(['image' => $imagePath]);

            $this->delete(route('products.destroy', $product));

            Storage::disk('public')->assertMissing($imagePath);
        });


        test('guests cannot delete products', function () {
            auth()->logout();
            $product = Product::factory()->create();

            $this->deleteJson(route('products.destroy', $product))
                ->assertUnauthorized();
        });
    });
});
