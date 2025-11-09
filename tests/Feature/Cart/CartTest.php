<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Cart Index', function () {
    test('authenticated users can view cart index', function () {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertViewIs('cart.index');
    });

    test('guests cannot view cart index', function () {
        auth()->logout();

        $this->get(route('cart.index'))
            ->assertRedirect(route('login'));
    });

    test('cart index returns json when requested', function () {
        $product = Product::factory()->create();
        $this->user->cart()->attach($product->id, ['quantity' => 2]);

        $response = $this->getJson(route('cart.index'));
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $product->id,
                'barcode' => $product->barcode
            ]);
    });

    test('cart returns empty array when cart is empty', function () {
        $this->getJson(route('cart.index'))
            ->assertOk()
            ->assertJsonCount(0);
    });


    test('cart items include pivot quantity', function () {
        $product = Product::factory()->create();
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $response = $this->getJson(route('cart.index'));
        $response->assertOk()
            ->assertJsonFragment([
                'quantity' => 3
            ]);
    });
});

describe('Cart Store', function () {
    test('authenticated users can add product to cart by barcode', function () {
        $product = Product::factory()->create([
            'barcode' => '1234567890',
            'quantity' => 10,
        ]);

        $this->postJson(route('cart.store'), [
            'barcode' => '1234567890',
        ]);

        expect($this->user->cart()->count())->toBe(1)
            ->and($this->user->cart()->first()->id)->toBe($product->id)
            ->and($this->user->cart()->first()->pivot->quantity)->toBe(1);
    });

    test('adding same product increases quantity', function () {
        $product = Product::factory()->create([
            'barcode' => '1234567890',
            'quantity' => 10,
        ]);

        $this->postJson(route('cart.store'), ['barcode' => '1234567890']);
        $this->postJson(route('cart.store'), ['barcode' => '1234567890']);
        $this->postJson(route('cart.store'), ['barcode' => '1234567890']);

        expect($this->user->cart()->count())->toBe(1)
            ->and($this->user->cart()->first()->pivot->quantity)->toBe(3);
    });

    test('cannot add product with insufficient stock', function () {
        $product = Product::factory()->create([
            'barcode' => '1234567890',
            'quantity' => 0,
        ]);

        $response = $this->post(route('cart.store'), [
            'barcode' => '1234567890',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => __('cart.outstock')]);

        expect($this->user->cart->count())
            ->toBeInt()
            ->not->toBeString()
            ->toBe(0);
    });

    test('cannot add more than available quantity', function () {
        $product = Product::factory()->create([
            'barcode' => '1234567890',
            'quantity' => 2,
        ]);

        $this->postJson(route('cart.store'), ['barcode' => '1234567890']);
        $this->postJson(route('cart.store'), ['barcode' => '1234567890']);

        $response = $this->postJson(route('cart.store'), ['barcode' => '1234567890']);

        $response->assertStatus(400)
            ->assertJson([
                'message' => __('cart.available', ['quantity' => 2])
            ]);

        expect($this->user->cart()->first()->pivot->quantity)
            ->toBeInt()
            ->not->toBeString()
            ->toBe(2);
    });

    test('barcode is required', function () {
        $response = $this->postJson(route('cart.store'), [
            'barcode' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('barcode');
    });

    test('barcode must exist in products', function () {
        $response = $this->postJson(route('cart.store'), [
            'barcode' => 'non-existent-barcode',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('barcode');
    });

    test('different users have separate carts', function () {
        $product = Product::factory()->create(['barcode' => '1234567890', 'quantity' => 10]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1)->postJson(route('cart.store'), ['barcode' => '1234567890']);
        $this->actingAs($user2)->postJson(route('cart.store'), ['barcode' => '1234567890']);

        expect($user1->cart()->count())->toBe(1)
            ->and($user2->cart()->count())->toBe(1)
            ->and($user1->cart()->first()->id)->toBe($product->id)
            ->and($user2->cart()->first()->id)->toBe($product->id);
    });

    test('authenticated users can change product quantity in cart', function () {
        $product = Product::factory()->create([
            'quantity' => 10
        ]);

        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        expect($this->user->cart()->first()->pivot->quantity)->toBe(5);
    });


    test('quantity can be decreased', function () {
        $product = Product::factory()->create([
            'quantity' => 10
        ]);
        $this->user->cart()->attach($product->id, ['quantity' => 5]);

        $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        expect($this->user->cart()->first()->pivot->quantity)->toBe(2);
    });

    test('cannot set quantity more than available stock', function () {
        $product = Product::factory()->create(['quantity' => 5]);
        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment([
                'message' => 'Available'
            ]);

        expect($this->user->cart()->first()->pivot->quantity)->toBe(1);
    });


    test('product_id is required', function () {
        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => '',
            'quantity' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    });

    test('product_id must exist', function () {
        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => 99999,
            'quantity' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    });

    test('quantity is required', function () {
        $product = Product::factory()->create();

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    });

    test('quantity must be at least 1', function () {
        $product = Product::factory()->create();

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    });

    test('quantity must be an integer', function () {
        $product = Product::factory()->create();

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 'not-a-number',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    });

    test('does nothing if product not in cart', function () {
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->postJson(route('cart.index') . '/change-qty', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertOk();
        expect($this->user->cart()->count())->toBe(0);
    });
});

describe('Cart Delete', function () {
    test('authenticated users can remove product from cart', function () {
        $product = Product::factory()->create();
        $this->user->cart()->attach($product->id, [
            'quantity' => 3
        ]);

        $this->deleteJson(route('cart.index') . '/delete', [
            'product_id' => $product->id,
        ]);

        expect($this->user->cart()->count())->toBe(0);
    });

    test('removing product does not affect other products in cart', function () {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $this->user->cart()->attach($product1->id, ['quantity' => 2]);
        $this->user->cart()->attach($product2->id, ['quantity' => 1]);

        $this->deleteJson(route('cart.index') . '/delete', [
            'product_id' => $product1->id,
        ]);;

        expect($this->user->cart()->count())
            ->toBeInt()
            ->toBe(1)
            ->and($this->user->cart()->first()->id)
            ->toBe($product2->id);
    });


    test('product_id is required for deletion', function () {
        $response = $this->deleteJson(route('cart.index') . '/delete', [
            'product_id' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    });

    test('product_id must exist for deletion', function () {
        $response = $this->deleteJson(route('cart.index') . '/delete', [
            'product_id' => 99999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    });

    test('deleting non-existent cart item succeeds silently', function () {
        $product = Product::factory()->create();

        $response = $this->deleteJson(route('cart.index') . '/delete', [
            'product_id' => $product->id,
        ]);

        $response->assertOk();
    });
});

describe('Cart Empty', function () {
    test('authenticated users can empty entire cart', function () {
        $products = Product::factory()->count(3)->create();

        foreach ($products as $product) {
            $this->user->cart()->attach($product->id, ['quantity' => rand(1, 5)]);
        }

        expect($this->user->cart->count())->toBe(3);

        $response = $this->deleteJson(route('cart.index') . '/empty');
        $response->assertOk();
        expect($this->user->cart()->count())->toBe(0);
    });


    test('emptying already empty cart succeeds', function () {
        $response = $this->deleteJson(route('cart.index') . '/empty');

        $response->assertOk();
        expect($this->user->cart()->count())->toBe(0);
    });

    test('emptying cart does not affect other users', function () {
        $product = Product::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->cart()->attach($product->id, ['quantity' => 1]);
        $user2->cart()->attach($product->id, ['quantity' => 1]);

        $this->actingAs($user1)->deleteJson(route('cart.index') . '/empty');

        expect($user1->cart()->count())->toBe(0)
            ->and($user2->cart()->count())->toBe(1);
    });
});
