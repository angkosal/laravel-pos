<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
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

describe('Order Stock Recheck', function () {
    test('checkout fails when cart quantity exceeds stock', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 2, 'status' => true]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect(Order::count())->toBe(0)
            ->and(OrderItem::count())->toBe(0)
            ->and(Payment::count())->toBe(0)
            ->and($product->refresh()->quantity)->toBe(2)
            ->and($this->user->cart()->first()->pivot->quantity)->toBe(3);
    });

    test('checkout rechecks stock that changed after adding to cart', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 10, 'status' => true]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $product->update(['quantity' => 1]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect($product->refresh()->quantity)->toBe(1)
            ->and(Order::count())->toBe(0);
    });

    test('checkout rolls back earlier items when a later item has insufficient stock', function () {
        $first = Product::factory()->create(['price' => 10, 'quantity' => 10, 'status' => true]);
        $second = Product::factory()->create(['price' => 20, 'quantity' => 1, 'status' => true]);
        $this->user->cart()->attach($first->id, ['quantity' => 2]);
        $this->user->cart()->attach($second->id, ['quantity' => 3]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 80])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect($first->refresh()->quantity)->toBe(10)
            ->and($second->refresh()->quantity)->toBe(1)
            ->and(Order::count())->toBe(0)
            ->and(OrderItem::count())->toBe(0)
            ->and(Payment::count())->toBe(0)
            ->and($this->user->cart()->count())->toBe(2);
    });

    test('checkout succeeds when cart quantity equals available stock', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 3, 'status' => true]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150])
            ->assertCreated()
            ->assertJson(['success' => true]);

        expect($product->refresh()->quantity)->toBe(0)
            ->and(Order::count())->toBe(1)
            ->and($this->user->cart()->count())->toBe(0);
    });

    test('checkout fails when a cart product has zero stock', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 0, 'status' => true]);
        $this->user->cart()->attach($product->id, ['quantity' => 1]);

        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 50])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect($product->refresh()->quantity)->toBe(0)
            ->and(Order::count())->toBe(0);
    });

    test('second checkout competing for the same stock fails without going negative', function () {
        // Sequential requests: verifies the conditional decrement logic, not true parallel execution.
        $product = Product::factory()->create(['price' => 50, 'quantity' => 5, 'status' => true]);
        $first = User::factory()->create();
        $second = User::factory()->create();
        $first->cart()->attach($product->id, ['quantity' => 3]);
        $second->cart()->attach($product->id, ['quantity' => 3]);

        $this->actingAs($first)
            ->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150])
            ->assertCreated();

        $this->actingAs($second)
            ->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect($product->refresh()->quantity)->toBe(2)
            ->and(Order::count())->toBe(1)
            ->and($second->cart()->count())->toBe(1);
    });

    test('insufficient stock message uses the cart.available translation', function () {
        $product = Product::factory()->create(['price' => 50, 'quantity' => 2, 'status' => true]);
        $this->user->cart()->attach($product->id, ['quantity' => 3]);

        $response = $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 150]);

        $response->assertStatus(400)
            ->assertJsonPath('message', __('cart.available', ['quantity' => 2]));

        expect($response->json('message'))
            ->not->toBeEmpty()
            ->not->toContain('cart.');
    });

    test('empty cart checkout still returns 400 and creates no order', function () {
        $this->postJson(route('orders.store'), ['customer_id' => null, 'amount' => 0])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        expect(Order::count())->toBe(0)
            ->and(Payment::count())->toBe(0);
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
