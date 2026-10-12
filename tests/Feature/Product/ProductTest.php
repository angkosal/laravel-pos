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

describe('Product Search', function () {
    test('search matches product name containing the term', function () {
        $match = Product::factory()->create(['name' => 'Green Apple Juice', 'barcode' => '1000000000001', 'status' => true]);
        Product::factory()->create(['name' => 'Orange Soda', 'barcode' => '1000000000002', 'status' => true]);

        $this->getJson(route('products.index', ['search' => 'Apple']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });

    test('search matches exact barcode', function () {
        $match = Product::factory()->create(['name' => 'Milk', 'barcode' => '5901234123457', 'status' => true]);
        Product::factory()->create(['name' => 'Bread', 'barcode' => '4006381333931', 'status' => true]);

        $this->getJson(route('products.index', ['search' => '5901234123457']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });

    test('search matches barcode prefix', function () {
        $first = Product::factory()->create(['name' => 'Milk', 'barcode' => '5901234000001', 'status' => true]);
        $second = Product::factory()->create(['name' => 'Bread', 'barcode' => '5901234000002', 'status' => true]);
        Product::factory()->create(['name' => 'Eggs', 'barcode' => '4006381333931', 'status' => true]);

        $ids = collect($this->getJson(route('products.index', ['search' => '5901234']))
            ->assertOk()
            ->json('data'))->pluck('id');

        expect($ids)->toHaveCount(2)
            ->and($ids->all())->toEqualCanonicalizing([$first->id, $second->id]);
    });

    test('search does not match barcode in the middle', function () {
        Product::factory()->create(['name' => 'Milk', 'barcode' => '5901234123457', 'status' => true]);

        $this->getJson(route('products.index', ['search' => '1234123']))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    test('search can match name of one product and barcode of another', function () {
        $byName = Product::factory()->create(['name' => 'Pack 777 Snacks', 'barcode' => '1000000000001', 'status' => true]);
        $byBarcode = Product::factory()->create(['name' => 'Water', 'barcode' => '7770000000001', 'status' => true]);
        Product::factory()->create(['name' => 'Tea', 'barcode' => '1000000000002', 'status' => true]);

        $ids = collect($this->getJson(route('products.index', ['search' => '777']))->json('data'))->pluck('id');

        expect($ids->all())->toEqualCanonicalizing([$byName->id, $byBarcode->id]);
    });

    test('search returns empty data when nothing matches', function () {
        Product::factory()->create(['name' => 'Milk', 'barcode' => '5901234123457', 'status' => true]);

        $this->getJson(route('products.index', ['search' => 'no-such-product']))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    test('empty search returns all products paginated', function () {
        Product::factory()->count(12)->create(['status' => true]);

        $this->getJson(route('products.index', ['search' => '']))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('total', 12);
    });

    test('percent sign in search is matched literally', function () {
        $match = Product::factory()->create(['name' => 'Promo 50% Off', 'barcode' => '1000000000001', 'status' => true]);
        Product::factory()->create(['name' => 'Plain Item', 'barcode' => '1000000000002', 'status' => true]);

        $this->getJson(route('products.index', ['search' => '%']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });

    test('underscore in search is matched literally', function () {
        $match = Product::factory()->create(['name' => 'snake_case item', 'barcode' => '1000000000001', 'status' => true]);
        Product::factory()->create(['name' => 'snakeXcase item', 'barcode' => '1000000000002', 'status' => true]);

        $this->getJson(route('products.index', ['search' => 'e_c']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });

    test('escape character in search is matched literally', function () {
        $match = Product::factory()->create(['name' => 'Wow! Chips', 'barcode' => '1000000000001', 'status' => true]);
        Product::factory()->create(['name' => 'Plain Chips', 'barcode' => '1000000000002', 'status' => true]);

        $this->getJson(route('products.index', ['search' => '!']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });

    test('url-encoded special characters reach the search intact', function () {
        $match = Product::factory()->create(['name' => 'Salt & Pepper #1', 'barcode' => '1000000000001', 'status' => true]);
        Product::factory()->create(['name' => 'Salt', 'barcode' => '1000000000002', 'status' => true]);

        $this->getJson('/admin/products?search=' . rawurlencode('Salt & Pepper #1'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    });
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
