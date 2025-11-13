<?php

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;


beforeEach(function () {
    $this->user = User::factory()->create();
    $this->supplier = Supplier::factory()->create();
    $this->product = Product::factory()->create([
        'quantity' => 100,
        'purchase_price' => 50,
    ]);
});

test('user can create purchase create page', function () {
    actingAs($this->user)
        ->get(route('purchases.create'))
        ->assertOk()
        ->assertViewIs('purchases.create')
        ->assertSee('New Purchase');
});

test('user can create a completed purchase', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'completed',
            'notes' => 'Test purchase',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertRedirect(route('purchases.index'))
        ->assertSessionHas('success');

    assertDatabaseHas('purchases', [
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'total_amount' => 100.00,
        'status' => 'completed',
    ]);

    expect($this->product->fresh()->quantity)->toBeInt()->toBe(102);
});

test('user can create a pending purchase', function () {
    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)
        ->toBeInt()
        ->toBe($initialStock);
});

test('user can create a cancelled purchase', function () {
    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'cancelled',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)
        ->toBeInt()
        ->toBe($initialStock);
});

test('completed purchase updates product purchase price', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 120.00,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 60.00,
                ]
            ]
        ]);

    expect($this->product->fresh()->purchase_price)
        ->toBeInt()
        ->toBe(60);
});

test('pending purchase does not update product purchase price', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 120.00,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 60.00,
                ]
            ]
        ]);

    expect($this->product->fresh()->purchase_price)
        ->toBe($this->product->purchase_price);
});

test('user can update purchase status from pending to completed', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->put(route('purchases.update', $purchase), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => $purchase->purchase_date->format('Y-m-d'),
            'total_amount' => $purchase->total_amount,
            'status' => 'completed',
        ])
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)->toBe($initialStock + 5);
});

test('user can update purchase status from completed to pending', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'status' => 'completed',
    ]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $this->product->increment('quantity', 5);
    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->put(route('purchases.update', $purchase), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => $purchase->purchase_date->format('Y-m-d'),
            'total_amount' => $purchase->total_amount,
            'status' => 'pending',
        ])
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)->toBe($initialStock - 5);
});

test('user can update purchase status from completed to cancelled', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'status' => 'completed',
    ]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $this->product->increment('quantity', 5);
    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->put(route('purchases.update', $purchase), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => $purchase->purchase_date->format('Y-m-d'),
            'total_amount' => $purchase->total_amount,
            'status' => 'cancelled',
        ])
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)->toBe($initialStock - 5);
});

test('user can delete a pending purchase without stock changes', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $initialStock = $this->product->quantity;

    actingAs($this->user)
        ->delete(route('purchases.destroy', $purchase))
        ->assertRedirect(route('purchases.index'));

    expect($this->product->fresh()->quantity)->toBe($initialStock);
    assertDatabaseMissing('purchases', [
        'id' => $purchase->id
    ]);
});

test('purchase requires supplier', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertSessionHasErrors('supplier_id');
});

test('purchase requires at least one item', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 0,
            'status' => 'completed',
            'items' => []
        ])
        ->assertSessionHasErrors('items');
});

test('purchase requires valid purchase date', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => 'invalid-date',
            'total_amount' => 100.00,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertSessionHasErrors('purchase_date');
});

test('purchase requires valid status', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'invalid-status',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertSessionHasErrors('status');
});

test('purchase item requires positive quantity', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 0,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertSessionHasErrors('items.0.quantity');
});

test('purchase item requires valid product', function () {
    actingAs($this->user)
        ->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'total_amount' => 100.00,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => 99999,
                    'quantity' => 2,
                    'purchase_price' => 50.00,
                ]
            ]
        ])
        ->assertSessionHasErrors('items.0.product_id');
});

test('user can add product to purchase cart', function () {
    actingAs($this->user)
        ->post(route('purchase-cart.store'), [
            'barcode' => $this->product->barcode,
        ])
        ->assertOk()
        ->assertJson(['message' => 'Product added to cart!']);

    assertDatabaseHas('user_purchase_cart', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);
});


test('adding same product to cart increases quantity', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->post(route('purchase-cart.store'), [
            'barcode' => $this->product->barcode,
        ])
        ->assertOk();

    assertDatabaseHas('user_purchase_cart', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);
});

test('user can change quantity in purchase cart', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->post(route('purchase-cart.change-qty'), [
            'product_id' => $this->product->id,
            'quantity' => 5,
        ])
        ->assertOk();

    assertDatabaseHas('user_purchase_cart', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 5,
    ]);
});

test('user can change price in purchase cart', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->post(route('purchase-cart.change-price'), [
            'product_id' => $this->product->id,
            'purchase_price' => 75.00,
        ])
        ->assertOk();

    assertDatabaseHas('user_purchase_cart', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'purchase_price' => 75.00,
    ]);
});

test('user can delete item from purchase cart', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->delete(route('purchase-cart.delete'), [
            'product_id' => $this->product->id,
        ])
        ->assertOk();

    assertDatabaseMissing('user_purchase_cart', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

test('user can empty purchase cart', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->delete(route('purchase-cart.empty'))
        ->assertOk();

    assertDatabaseMissing('user_purchase_cart', [
        'user_id' => $this->user->id,
    ]);
});

test('user can view purchase cart items', function () {
    $this->user->purchaseCart()->attach($this->product->id, [
        'quantity' => 2,
        'purchase_price' => 50.00,
    ]);

    actingAs($this->user)
        ->get(route('purchase-cart.index'))
        ->assertOk()
        ->assertJsonCount(1);
});

test('purchase cart returns empty array when no items', function () {
    actingAs($this->user)
        ->get(route('purchase-cart.index'))
        ->assertOk()
        ->assertJson([]);
});
