<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Order Index', function () {
    test('authenticated users can view orders index', function () {
        $response = $this->get(route('orders.index'));

        $response->assertOk()
            ->assertViewIs('orders.index')
            ->assertViewHas(['orders', 'total', 'receivedAmount']);
    });

    test('guests cannot view orders index', function () {
        auth()->logout();

        $this->get(route('orders.index'))
            ->assertRedirect(route('login'));
    });

    test('orders are paginated and ordered by latest', function () {
        Order::factory()->count(15)->create();

        $response = $this->get(route('orders.index'));

        $response->assertViewHas('orders', function ($orders) {
            return $orders->count() === 10;
        });
    });

    test('orders can be filtered by date range', function () {
        $oldOrder = Order::factory()->create(['created_at' => now()->subDays(20)]);
        $newOrder = Order::factory()->create(['created_at' => now()->subDays(10)]);

        $response = $this->get(route('orders.index', [
            'start_date' => now()->subDays(15)->format('Y-m-d'),
            'end_date' => now()->subDays(5)->format('Y-m-d'),
        ]));

        $response->assertViewHas('orders', function ($orders) use ($oldOrder, $newOrder) {
            return !$orders->contains($oldOrder) && $orders->contains($newOrder);
        });
    });

    test('total and receivedAmount are calculated correctly', function () {
        $order = Order::factory()->create();
        $order->items()->create([
            'price' => 100,
            'quantity' => 2,
            'product_id' => Product::factory()->create()->id,
        ]);
        $order->payments()->create(['amount' => 75, 'user_id' => $this->user->id]);

        $response = $this->get(route('orders.index'));

        $response->assertViewHas('total', 100)
            ->assertViewHas('receivedAmount', 75);
    });
});

describe('Order Store', function () {
    test('authenticated users can create order from cart', function () {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'quantity' => 10]);

        $this->user->cart()->attach($product->id, ['quantity' => 2]);

        $this->postJson(route('orders.store'), [
            'customer_id' => $customer->id,
            'amount' => 200,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'user_id' => $this->user->id,
        ]);
    });

    test('order creates items and payment correctly', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 10]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $this->postJson(route('orders.store'), [
            'customer_id' => null,
            'amount' => 150,
        ]);

        $order = Order::latest()->first();

        expect($order->items()->count())->toBe(1)
            ->and((float) $order->items()->first()->price)->toBe(150.0)
            ->and($order->payments()->count())->toBe(1)
            ->and((float) $order->payments()->first()->amount)->toBe(150.0);
    });

    test('order reduces product stock and empties cart', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 10]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $this->postJson(route('orders.store'), [
            'customer_id' => null,
            'amount' => 150.0,
        ]);

        $product->refresh();

        expect($product->quantity)->toBe(7)
            ->and($this->user->cart()->count())->toBe(0);
    });

    test('order can be created without customer', function () {
        $product = Product::factory()->create(['price' => 100, 'quantity' => 10]);
        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $this->postJson(route('orders.store'), [
            'customer_id' => null,
            'amount' => 100,
        ]);

        expect(Order::latest()->first()->customer_id)->toBeNull();
    });
});

describe('Order Validation', function () {
    test('customer_id must exist if provided', function () {
        $product = Product::factory()->create(['quantity' => 10]);
        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $response = $this->postJson(route('orders.store'), [
            'customer_id' => 99999,
            'amount' => 100,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('customer_id');
    });

    test('amount is required and must be numeric', function () {
        $product = Product::factory()->create(['quantity' => 10]);
        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => ''])
            ->assertJsonValidationErrors('amount');

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 'invalid'])
            ->assertJsonValidationErrors('amount');

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => -10])
            ->assertJsonValidationErrors('amount');
    });
});

describe('Partial Payment', function () {
    test('users can make partial payment on order', function () {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();

        $order->items()->create([
            'price' => 200,
            'quantity' => 1,
            'product_id' => $product->id,
        ]);

        $order->payments()->create(['amount' => 50, 'user_id' => $this->user->id]);

        $response = $this->post(route('orders.partial-payment'), [
            'order_id' => $order->id,
            'amount' => 100,
        ]);

        $response->assertRedirect(route('orders.index'))
            ->assertSessionHas('success');

        expect($order->payments()->count())->toBe(2)
            ->and($order->receivedAmount())->toBe(150.0);
    });

    test('partial payment cannot exceed remaining balance', function () {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();

        $order->items()->create(['price' => 100, 'quantity' => 1, 'product_id' => $product->id]);
        $order->payments()->create(['amount' => 50, 'user_id' => $this->user->id]);

        $response = $this->post(route('orders.partial-payment'), [
            'order_id' => $order->id,
            'amount' => 100,
        ]);

        $response->assertRedirect(route('orders.index'))
            ->assertSessionHasErrors();
    });
});
