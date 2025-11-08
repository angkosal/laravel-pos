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

describe('Home Dashboard', function () {
    test('authenticated users can view dashboard', function () {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewIs('home')
            ->assertViewHas([
                'orders_count',
                'income',
                'income_today',
                'customers_count',
                'low_stock_products',
                'best_selling_products',
                'current_month_products',
                'past_months_products',
            ]);
    });

    test('guests cannot view dashboard', function () {
        auth()->logout();

        $this->get(route('home'))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });

    test('root path redirects to dashboard', function () {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    });
});

describe('Dashboard Statistics', function () {
    test('order count is calculated correctly ', function () {
        Order::factory(15)->create();

        $this->get(route('home'))
            ->assertViewHas('orders_count', 15);
    });

    test('customers count is calculated correctly', function () {
        Customer::factory()->count(10)->create();

        $response = $this->get(route('home'));

        $response->assertViewHas('customers_count', 10);
    });

    test('income is calculated from all orders', function () {
        $order1 = Order::factory()->create();
        $product1 = Product::factory()->create();
        $order1->items()->create(['price' => 100, 'quantity' => 1, 'product_id' => $product1->id]);
        $order1->payments()->create(['amount' => 100, 'user_id' => $this->user->id]);

        $order2 = Order::factory()->create();
        $product2 = Product::factory()->create();
        $order2->items()->create(['price' => 200, 'quantity' => 1, 'product_id' => $product2->id]);
        $order2->payments()->create(['amount' => 150, 'user_id' => $this->user->id]);

        $response = $this->get(route('home'));
        $response->assertViewHas('income', 250);
    });

    test('income uses total when received amount exceeds total', function () {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->items()->create([
            'price' => 100,
            'quantity' => 1,
            'product_id' => $product->id
        ]);

        $order->payments()->create([
            'amount' => 150,
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('home'));
        $response->assertViewHas('income', 100);
    });

    test('income today only includes today orders', function () {
        $todayOrder = Order::factory()->create(['created_at' => now()]);
        $product1 = Product::factory()->create();
        $todayOrder->items()->create(['price' => 100, 'quantity' => 1, 'product_id' => $product1->id]);
        $todayOrder->payments()->create(['amount' => 100, 'user_id' => $this->user->id]);

        $yesterdayOrder = Order::factory()->create(['created_at' => now()->subDay()]);
        $product2 = Product::factory()->create();
        $yesterdayOrder->items()->create(['price' => 200, 'quantity' => 1, 'product_id' => $product2->id]);
        $yesterdayOrder->payments()->create(['amount' => 200, 'user_id' => $this->user->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('income_today', 100);
    });
});

describe('Low Stock Products', function () {
    test('products with quantity less than 10 are shown as low stock', function () {
        $lowStock1 = Product::factory()->create(['quantity' => 5]);
        $lowStock2 = Product::factory()->create(['quantity' => 9]);
        $normalStock = Product::factory()->create(['quantity' => 10]);
        $highStock = Product::factory()->create(['quantity' => 50]);

        $response = $this->get(route('home'));

        $response->assertViewHas('low_stock_products', function ($products) use ($lowStock1, $lowStock2, $normalStock) {
            return $products->count() === 2
                && $products->contains($lowStock1)
                && $products->contains($lowStock2)
                && !$products->contains($normalStock);
        });
    });

    test('no low stock products when all products have sufficient quantity', function () {
        Product::factory()->count(5)->create(['quantity' => 20]);

        $response = $this->get(route('home'));

        $response->assertViewHas('low_stock_products', function ($products) {
            return $products->count() === 0;
        });
    });
});

describe('Best Selling Products', function () {
    test('best selling products have more than 10 total sales', function () {
        $product1 = Product::factory()->create(['name' => 'Product 1']);
        $product2 = Product::factory()->create(['name' => 'Product 2']);
        $product3 = Product::factory()->create(['name' => 'Product 3']);

        $order1 = Order::factory()->create();
        $order1->items()->create(['price' => 100, 'quantity' => 15, 'product_id' => $product1->id]);

        $order2 = Order::factory()->create();
        $order2->items()->create(['price' => 50, 'quantity' => 5, 'product_id' => $product2->id]);

        $order3 = Order::factory()->create();
        $order3->items()->create(['price' => 75, 'quantity' => 20, 'product_id' => $product3->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('best_selling_products', function ($products) use ($product1, $product2, $product3) {
            return $products->count() === 2
                && $products->contains('id', $product1->id)
                && !$products->contains('id', $product2->id)
                && $products->contains('id', $product3->id);
        });
    });

    test('best selling products include total_sold count', function () {
        $product = Product::factory()->create();

        $order1 = Order::factory()->create();
        $order1->items()->create(['price' => 100, 'quantity' => 8, 'product_id' => $product->id]);

        $order2 = Order::factory()->create();
        $order2->items()->create(['price' => 100, 'quantity' => 7, 'product_id' => $product->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('best_selling_products', function ($products) {
            return $products->count() === 1
                && $products->first()->total_sold == 15;
        });
    });
});

describe('Current Month Best Selling', function () {
    test('current month products have more than 500 sales this month', function () {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $order1 = Order::factory()->create(['created_at' => now()]);
        $order1->items()->create(['price' => 100, 'quantity' => 600, 'product_id' => $product1->id]);

        $order2 = Order::factory()->create(['created_at' => now()]);
        $order2->items()->create(['price' => 50, 'quantity' => 400, 'product_id' => $product2->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('current_month_products', function ($products) use ($product1, $product2) {
            return $products->count() === 1
                && $products->contains('id', $product1->id)
                && !$products->contains('id', $product2->id);
        });
    });

    test('current month products exclude previous months', function () {
        $product = Product::factory()->create();

        $lastMonth = Order::factory()->create(['created_at' => now()->subMonth()]);
        $lastMonth->items()->create(['price' => 100, 'quantity' => 600, 'product_id' => $product->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('current_month_products', function ($products) {
            return $products->count() === 0;
        });
    });
});

describe('Past Six Months Hot Products', function () {
    test('hot products have more than 1000 sales in past 6 months', function () {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $order1 = Order::factory()->create(['created_at' => now()->subMonths(3)]);
        $order1->items()->create(['price' => 100, 'quantity' => 1500, 'product_id' => $product1->id]);

        $order2 = Order::factory()->create(['created_at' => now()->subMonths(2)]);
        $order2->items()->create(['price' => 50, 'quantity' => 800, 'product_id' => $product2->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('past_months_products', function ($products) use ($product1, $product2) {
            return $products->count() === 1
                && $products->contains('id', $product1->id)
                && !$products->contains('id', $product2->id);
        });
    });

    test('hot products exclude orders older than 6 months', function () {
        $product = Product::factory()->create();

        $oldOrder = Order::factory()->create(['created_at' => now()->subMonths(7)]);
        $oldOrder->items()->create(['price' => 100, 'quantity' => 1500, 'product_id' => $product->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('past_months_products', function ($products) {
            return $products->count() === 0;
        });
    });

    test('hot products aggregate sales across multiple orders', function () {
        $product = Product::factory()->create();

        $order1 = Order::factory()->create(['created_at' => now()->subMonths(5)]);
        $order1->items()->create(['price' => 100, 'quantity' => 600, 'product_id' => $product->id]);

        $order2 = Order::factory()->create(['created_at' => now()->subMonths(3)]);
        $order2->items()->create(['price' => 100, 'quantity' => 500, 'product_id' => $product->id]);

        $response = $this->get(route('home'));

        $response->assertViewHas('past_months_products', function ($products) {
            return $products->count() === 1
                && $products->first()->total_sold == 1100;
        });
    });
});
