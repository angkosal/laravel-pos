<?php

declare(strict_types=1);

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('public');

    $this->validSupplierData = [
        'first_name' => 'John',
        'last_name' => 'Supplier',
        'email' => 'supplier@example.com',
        'phone' => '+1234567890',
        'address' => '123 Supplier St, City',
    ];
});

describe('Supplier Index', function () {
    test('authenticated users can view suppliers index', function () {
        $this->get(route('suppliers.index'))
            ->assertViewIs('suppliers.index')
            ->assertViewHas('suppliers')
            ->assertSee('Supplier List')
            ->assertOk();
    });

    test('guests cannot view suppliers index', function () {
        auth()->logout();
        $this->get(route('suppliers.index'))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });

    test('suppliers are paginated', function () {
        Supplier::factory(15)->withoutEmail()->create();

        $response = $this->get(route('suppliers.index'));

        $response->assertViewHas('suppliers', function ($suppliers) {
            return $suppliers->count() === 15;
        });
        $response->assertOk();
    });

    test('suppliers are ordered by latest first', function () {
        Supplier::factory()->create(['first_name' => 'Old']);
        $this->travel(1)->day();
        $newSupplier = Supplier::factory()->create(['first_name' => 'New']);

        $response = $this->get(route('suppliers.index'));

        $response->assertViewHas('suppliers', function ($suppliers) use ($newSupplier) {
            return $suppliers->first()->id === $newSupplier->id;
        });
    });


    test('suppliers index returns json when requested', function () {
        Supplier::factory()->count(3)->create();

        $response = $this->getJson(route('suppliers.index'));

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'email', 'phone', 'address']
                ]
            ]);
    });

    test('json response returns all suppliers without pagination', function () {
        Supplier::factory(15)->create();

        $this->getJson(route('suppliers.index'))
            ->assertJsonCount(15, 'data')
            ->assertOk();
    });
});

describe('Supplier Create', function () {
    test('authenticated users can view create form', function () {
        $this->get(route('suppliers.create'))
            ->assertOk()
            ->assertViewIs('suppliers.create');
    });

    test('guests cannot view create form', function () {
        auth()->logout();

        $this->get(route('suppliers.create'))
            ->assertRedirect(route('login'));
    });
});

describe('Supplier Store', function () {
    test('authenticated users can create a supplier', function () {
        $this->post(route('suppliers.store'), $this->validSupplierData)
            ->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'first_name' => 'John',
            'last_name' => 'Supplier',
            'email' => 'supplier@example.com',
            'phone' => '+1234567890',
        ]);
    });

    test('supplier can be created with logo', function () {
        $logo = UploadedFile::fake()->image('logo.jpg');
        $supplierData = array_merge($this->validSupplierData, [
            'avatar' => $logo
        ]);

        $response = $this->post(route('suppliers.store'), $supplierData)
            ->assertRedirect(route('suppliers.index'));

        $response->assertRedirect(route('suppliers.index'));

        $supplier = Supplier::firstWhere('email', 'supplier@example.com');
        expect($supplier->avatar)->not->toBeNull();
    });

    test('supplier can be created with only required fields', function () {
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->post(route('suppliers.store'), $data);
        $response->assertRedirect(route('suppliers.index'));

        $this->assertDatabaseHas('suppliers', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => null,
            'phone' => null,
            'address' => null,
        ]);
    });
});

describe('Supplier Update',function (){
    test('authenticated users can update a supplier', function () {
        $supplier = Supplier::factory()->create();

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'address' => 'New Address',
        ];

        $response = $this->put(route('suppliers.update', $supplier), $updateData);

        $response->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ]);
    });

    test('supplier can be updated with new logo', function () {
        $supplier = Supplier::factory()->create();

        $newLogo = UploadedFile::fake()->image('new-logo.jpg');

        $updateData = array_merge($this->validSupplierData, ['avatar' => $newLogo]);

        $this->put(route('suppliers.update', $supplier), $updateData);

        $supplier->refresh();

        expect($supplier->avatar ?? $supplier->logo ?? null)->not->toBeNull();
    });

    test('update works without logo field', function () {
        $supplier = Supplier::factory()->create(['first_name' => 'Original']);

        $updateData = [
            'first_name' => 'Modified',
            'last_name' => $supplier->last_name,
        ];

        $response = $this->put(route('suppliers.update', $supplier), $updateData);

        $response->assertSessionHasNoErrors();

        $supplier->refresh();
        expect($supplier->first_name)->toBe('Modified');
    });
});


describe('Supplier Destroy', function () {
    test('authenticated users can delete a supplier', function () {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson(route('suppliers.destroy', $supplier));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    });

    test('supplier logo is deleted when supplier is deleted', function () {
        $supplier = Supplier::factory()->create();
        $logoPath = UploadedFile::fake()->image('logo.jpg')->store('suppliers', 'public');
        $supplier->update(['avatar' => $logoPath]);

        $this->deleteJson(route('suppliers.destroy', $supplier));

        Storage::disk('public')->assertMissing($logoPath);
    });

    test('guests cannot delete suppliers', function () {
        auth()->logout();
        $supplier = Supplier::factory()->create();

        $this->deleteJson(route('suppliers.destroy', $supplier))
            ->assertUnauthorized();
    });
});
