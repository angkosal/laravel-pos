<?php
declare(strict_types=1);

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->supplier = Supplier::factory()->create();
    $this->product = Product::factory()->create([
        'quantity' => 100,
        'purchase_price' => 50.00,
    ]);
});


test('user can view purchases index page', function () {
    actingAs($this->user)
        ->get(route('purchases.index'))
        ->assertOk()
        ->assertViewIs('purchases.index')
        ->assertViewHas('purchases')
        ->assertViewIs('purchases.index');
});

test('purchases index displays all purchases', function () {
    $purchase1 = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    $purchase2 = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);

    $response = actingAs($this->user)
        ->get(route('purchases.index'));

    $response->assertSee($purchase1->id);
    $response->assertSee($purchase2->id);
});

test('purchases are ordered by date descending', function () {
    $oldPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(5),
    ]);

    $newPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now(),
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.index'));

    $purchases = $response->viewData('purchases');

    expect($purchases->first()->id)->toBe($newPurchase->id);
});

test('can filter purchases by status', function () {
    $completedPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
    ]);

    $pendingPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'pending',
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.index', [
            'status' => 'completed'
        ]));

    $purchases = $response->viewData('purchases');

    expect($purchases)->toHaveCount(1)
        ->and($purchases->first()->id)->toBe($completedPurchase->id);
});


test('can search purchases by id', function () {
    $purchase1 = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    $purchase2 = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);

    $response = actingAs($this->user)
        ->get(route('purchases.index', ['search' => $purchase1->id]));

    $purchases = $response->viewData('purchases');

    expect($purchases)->toHaveCount(1);
    expect($purchases->first()->id)->toBe($purchase1->id);
});

test('can search purchases by notes', function () {
    $purchase1 = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Urgent order from warehouse',
    ]);

    $purchase2 = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Regular monthly stock',
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.index', ['search' => 'Urgent']));

    $purchases = $response->viewData('purchases');

    expect($purchases)->toHaveCount(1);
    expect($purchases->first()->id)->toBe($purchase1->id);
});

test('can combine multiple filters', function () {
    $targetPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
        'purchase_date' => now()->subDays(2),
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'pending',
        'purchase_date' => now()->subDays(2),
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.index', [
            'supplier_id' => $this->supplier->id,
            'status' => 'completed',
            'date_from' => now()->subDays(5)->format('Y-m-d'),
        ]));

    $purchases = $response->viewData('purchases');

    expect($purchases)->toHaveCount(1);
    expect($purchases->first()->id)->toBe($targetPurchase->id);
});

test('empty state shows when no purchases found', function () {
    $response = actingAs($this->user)
        ->get(route('purchases.index'));

    $response->assertSee('No purchases found');
    $response->assertSee('Create First Purchase');
});

// AJAX Data Endpoint Tests
test('data endpoint returns json', function () {
    Purchase::factory(3)->create(['supplier_id' => $this->supplier->id]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'purchase_date', 'total_amount', 'status', 'supplier', 'items_count']
        ],
        'current_page',
        'last_page',
        'total',
        'per_page',
        'from',
        'to'
    ]);
});

test('data endpoint filters by status', function () {
    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed'
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'pending'
    ]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', ['status' => 'completed']));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.status'))->toBe('completed');
});

test('data endpoint filters by supplier', function () {
    $supplier2 = Supplier::factory()->create();

    Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    Purchase::factory()->create(['supplier_id' => $supplier2->id]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', ['supplier_id' => $this->supplier->id]));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.supplier.id'))->toBe($this->supplier->id);
});

test('data endpoint filters by date range', function () {
    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(10),
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(2),
    ]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', [
            'date_from' => now()->subDays(5)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

test('data endpoint searches by notes', function () {
    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Urgent delivery',
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Regular order',
    ]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', ['search' => 'Urgent']));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

test('data endpoint paginates results', function () {
    Purchase::factory(15)->create(['supplier_id' => $this->supplier->id]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(10);
    expect($response->json('total'))->toBe(15);
    expect($response->json('last_page'))->toBe(2);
});

test('data endpoint returns items count', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data'));

    $response->assertOk();
    expect($response->json('data.0.items_count'))->toBe(1);
});

test('data endpoint supports page parameter', function () {
    Purchase::factory(15)->create(['supplier_id' => $this->supplier->id]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', ['page' => 2]));

    $response->assertOk();
    expect($response->json('current_page'))->toBe(2);
    expect($response->json('data'))->toHaveCount(5);
});

test('data endpoint combines multiple filters', function () {
    $targetPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
        'purchase_date' => now()->subDays(2),
        'notes' => 'Special order',
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'pending',
        'purchase_date' => now()->subDays(2),
    ]);

    $response = actingAs($this->user)
        ->getJson(route('purchases.data', [
            'status' => 'completed',
            'supplier_id' => $this->supplier->id,
            'search' => 'Special',
        ]));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($targetPurchase->id);
});

// Show Page Tests
test('user can view purchase details', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $purchase));

    $response->assertOk();
    $response->assertViewIs('purchases.show');
    $response->assertSee("#$purchase->id");
    $response->assertSee($this->product->name);
    $response->assertSee($this->supplier->first_name);
});

test('purchase show page displays all items', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    $product2 = Product::factory()->create();

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $purchase->items()->create([
        'product_id' => $product2->id,
        'quantity' => 3,
        'purchase_price' => 30.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $purchase));

    $response->assertSee($this->product->name);
    $response->assertSee($product2->name);
    expect($purchase->items)->toHaveCount(2);
});

test('purchase show displays correct status badge', function () {
    $completedPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $completedPurchase));

    $response->assertSee('badge-success');
    $response->assertSee('Completed');
});

test('purchase show displays supplier information', function () {
    $supplier = Supplier::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@supplier.com',
        'phone' => '1234567890',
    ]);

    $purchase = Purchase::factory()->create(['supplier_id' => $supplier->id]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $purchase));

    $response->assertSee('John Doe');
    $response->assertSee('john@supplier.com');
    $response->assertSee('1234567890');
});

test('purchase show displays notes if present', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Important: Handle with care',
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $purchase));

    $response->assertSee('Important: Handle with care');
});

test('purchase show calculates subtotals correctly', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.show', $purchase));

    $response->assertSee('250.00'); // 5 * 50 = 250
});

// Receipt Tests
test('user can generate purchase receipt', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.receipt', $purchase));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('receipt displays purchase information', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
    ]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.receipt', $purchase));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('pdf');
});

test('receipt includes all purchase items', function () {
    $purchase = Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    $product2 = Product::factory()->create();

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 5,
        'purchase_price' => 50.00,
    ]);

    $purchase->items()->create([
        'product_id' => $product2->id,
        'quantity' => 3,
        'purchase_price' => 30.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.receipt', $purchase));

    $response->assertOk();
    expect($purchase->items)->toHaveCount(2);
});

test('receipt displays supplier information', function () {
    $supplier = Supplier::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
    ]);

    $purchase = Purchase::factory()->create(['supplier_id' => $supplier->id]);

    $purchase->items()->create([
        'product_id' => $this->product->id,
        'quantity' => 1,
        'purchase_price' => 50.00,
    ]);

    $response = actingAs($this->user)
        ->get(route('purchases.receipt', $purchase));

    $response->assertOk();
});

// Scope Tests
test('status scope filters by status', function () {
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'completed']);
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'pending']);

    $completed = Purchase::status('completed')->get();

    expect($completed)->toHaveCount(1);
    expect($completed->first()->status)->toBe('completed');
});

test('completed scope returns only completed purchases', function () {
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'completed']);
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'pending']);

    $completed = Purchase::completed()->get();

    expect($completed)->toHaveCount(1);
    expect($completed->first()->status)->toBe('completed');
});

test('pending scope returns only pending purchases', function () {
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'completed']);
    Purchase::factory()->create(['supplier_id' => $this->supplier->id, 'status' => 'pending']);

    $pending = Purchase::pending()->get();

    expect($pending)->toHaveCount(1);
    expect($pending->first()->status)->toBe('pending');
});

test('bySupplier scope filters by supplier', function () {
    $supplier2 = Supplier::factory()->create();

    Purchase::factory()->create(['supplier_id' => $this->supplier->id]);
    Purchase::factory()->create(['supplier_id' => $supplier2->id]);

    $purchases = Purchase::bySupplier($this->supplier->id)->get();

    expect($purchases)->toHaveCount(1);
    expect($purchases->first()->supplier_id)->toBe($this->supplier->id);
});

test('dateRange scope filters by date range', function () {
    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(10),
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(2),
    ]);

    $purchases = Purchase::dateRange(
        now()->subDays(5)->format('Y-m-d'),
        now()->format('Y-m-d')
    )->get();

    expect($purchases)->toHaveCount(1);
});

test('search scope searches by id and notes', function () {
    $purchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Urgent delivery needed',
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'notes' => 'Regular order',
    ]);

    $results = Purchase::search('Urgent')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($purchase->id);
});

test('recent scope returns purchases from last n days', function () {
    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(40),
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'purchase_date' => now()->subDays(10),
    ]);

    $recent = Purchase::recent(30)->get();

    expect($recent)->toHaveCount(1);
});

test('filter scope combines multiple filters', function () {
    $targetPurchase = Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'completed',
        'purchase_date' => now()->subDays(2),
        'notes' => 'Special order',
    ]);

    Purchase::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => 'pending',
        'purchase_date' => now()->subDays(2),
    ]);

    $filtered = Purchase::filter([
        'status' => 'completed',
        'supplier_id' => $this->supplier->id,
        'date_from' => now()->subDays(5)->format('Y-m-d'),
        'search' => 'Special',
    ])->get();

    expect($filtered)->toHaveCount(1);
    expect($filtered->first()->id)->toBe($targetPurchase->id);
});
