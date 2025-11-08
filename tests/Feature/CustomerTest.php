<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('public');

    $this->validCustomerData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'address' => '123 Main St, City',
    ];
});

describe('Customers Index', function () {
    test('authenticated users can view customers index', function () {
        $this->get(route('customers.index'))
            ->assertViewHas('customers')
            ->assertViewIs('customers.index')
            ->assertOk()
            ->assertSee('Customer List');
    });

    test('guests cannot view index', function () {
        auth()->logout();
        $this->get(route('customers.index'))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });

    test('customers are paginated', function () {
        Customer::factory(15)
            ->withOutEmail()
            ->create();

        $response = $this->get(route('customers.index'));

        $response->assertViewHas('customers', function ($customers) {
            return $customers->count() === 10;
        });
    });

    test('customers index returns json when requested', function () {
        Customer::factory(5)
            ->withOutEmail()
            ->create();

        $response = $this->getJson(route('customers.index'));

        $response->assertOk()
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => ['id', 'first_name', 'last_name', 'email', 'phone', 'address']
            ]);
    });

    test('json response returns all customers without pagination', function () {
        Customer::factory(15)->create();
        $this->getJson(route('customers.index'))
            ->assertJsonCount(15)
            ->assertOk();
    });
});

describe('Customer Create', function () {
    test('authenticated users can view create form', function () {
        $this->get(route('customers.create'))
            ->assertViewIs('customers.create')
            ->assertSee('Create Customer')
            ->assertOk();
    });

    test('guests cannot be create view', function () {
        auth()->logout();
        $this->get(route('customers.create'))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });
});

describe('Customer Store', function () {
    test('authenticated users can create a customer', function () {
        $response = $this->post(route('customers.store', $this->validCustomerData))
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success')
            ->assertStatus(302);

        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'user_id' => $this->user->id,
        ]);
    });

    test('customer is associated with authenticated user', function () {
        $this->post(route('customers.store', $this->validCustomerData));
        $customer = Customer::firstWhere('email', 'john@example.com');

        expect($customer->user_id)
            ->toBe($this->user->id);
    });

    test('customer can be created with avatar', function () {
        $avatar = UploadedFile::fake()->image('avatar.jpg');
        $customerData = array_merge($this->validCustomerData, [
            'avatar' => $avatar
        ]);

        $this->post(route('customers.store'), $customerData)
            ->assertRedirect(route('customers.index'));

        $customer = Customer::where('email', 'john@example.com')->first();

        expect($customer->avatar)
            ->not
            ->toBeNull();

        Storage::disk('public')->assertExists($customer->avatar);
    });

    test('customer can be created with only required fields', function () {
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->post(route('customers.store'), $data);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => null,
            'phone' => null,
            'address' => null,
        ]);
    });
});

describe('Customer Edit', function () {
    test('authenticated user can view edit form', function () {
        $customer = Customer::factory()->create();

        $this->get(route('customers.edit', $customer))
            ->assertViewIs('customers.edit')
            ->assertViewHas('customer', $customer)
            ->assertSee('Update Customer')
            ->assertOk();
    });

    test('guests cannot view edit form', function () {
        auth()->logout();
        $customer = Customer::factory()->create();

        $this->get(route('customers.edit', $customer))
            ->assertRedirect(route('login'))
            ->assertStatus(302);
    });
});

describe('Customer Update', function () {
    test('authenticated users can update a customer', function () {
        $customer = Customer::factory()->create();

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'address' => 'New Address',
        ];

        $response = $this->put(route('customers.update', $customer), $updateData);

        $response->assertRedirect(route('customers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ]);
    });

    test('customer can be updated with new avatar', function () {
        $customer = Customer::factory()->create();
        $newAvatar = UploadedFile::fake()->image('new-avatar.jpg');

        $updateData = array_merge($this->validCustomerData, ['avatar' => $newAvatar]);

        $this->put(route('customers.update', $customer), $updateData);

        $customer->refresh();

        expect($customer->avatar)->not->toBeNull();
        Storage::disk('public')->assertExists($customer->avatar);
    });

    test('old avatar is deleted when updating with new avatar', function () {
        $customer = Customer::factory()->create();
        $oldAvatarPath = UploadedFile::fake()->image('old.jpg')->store('customers', 'public');
        $customer->update(['avatar' => $oldAvatarPath]);

        $newAvatar = UploadedFile::fake()->image('new.jpg');

        $updateData = array_merge($this->validCustomerData, ['avatar' => $newAvatar]);

        $this->put(route('customers.update', $customer), $updateData);

        Storage::disk('public')->assertMissing($oldAvatarPath);
    });


    test('update works without avatar field', function () {
        $customer = Customer::factory()->create([
            'first_name' => 'Original'
        ]);

        $updateCustomerData = [
            'first_name' => 'Modified',
            'last_name' => $customer->last_name,
        ];

        $response = $this->put(route('customers.update', $customer), $updateCustomerData);
        $response->assertSessionHasNoErrors();

        $customer->refresh();

        expect($customer->first_name)->toBe('Modified');
    });

    test('user_id cannot be changed during update', function () {
        $originalUserId = $this->user->id;
        $customer = Customer::factory()->create(['user_id' => $originalUserId]);

        $otherUser = User::factory()->create();

        $updateData = array_merge($this->validCustomerData, ['user_id' => $otherUser->id]);

        $this->put(route('customers.update', $customer), $updateData);

        $customer->refresh();
        expect($customer->user_id)->toBe($originalUserId);
    });
});

describe('Customer Destroy', function () {
    test('authenticated users can delete a customer', function () {
        $customer = Customer::factory()->create();
        $response = $this->deleteJson(route('customers.destroy', $customer));

        $response->assertJson([
            'success' => true
        ])->assertOk();

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id
        ]);
    });

    test('customer avatar is deleted when customer is deleted', function () {
        $customer = Customer::factory()->create();
        $avatarPath = UploadedFile::fake()->image('avatar.jpg')->store('customers', 'public');
        $customer->update(['avatar' => $avatarPath]);

        $this->deleteJson(route('customers.destroy', $customer));

        Storage::disk('public')->assertMissing($avatarPath);
    });

    test('guests cannot delete customer',function (){
        auth()->logout();
        $customer = Customer::factory()->create();
        $this->deleteJson(route('customers.destroy', $customer))
            ->assertUnauthorized();
    });
});
