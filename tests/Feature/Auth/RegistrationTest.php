<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->validData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];
});

describe('Registration Screen', function () {
    test('can be rendered', function () {
        $this->get('/register')
            ->assertOk()
            ->assertViewIs('auth.register')
            ->assertSee('Register');
    });

    test('redirects authenticated users away from register page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('home'));
    });
});

describe('User Registration', function () {
    test('new users can register with valid data', function () {
        $response = $this->post(route('register'), $this->validData);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
    });

    test('user password is hashed after registration', function () {
        $this->post(route('register'), $this->validData);

        $user = User::where('email', $this->validData['email'])->first();

        expect($user->password)->not->toBe($this->validData['password'])
            ->and(Hash::check($this->validData['password'], $user->password))->toBeTrue();
    });

    test('registered user is automatically authenticated', function () {
        $this->post(route('register'), $this->validData);

        $this->assertAuthenticated();
        expect(auth()->user()->email)->toBe($this->validData['email']);
    });
});

describe('First Name Validation', function () {
    test('first name is required', function () {
        $data = array_merge($this->validData, ['first_name' => '']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('first_name');
        $this->assertGuest();
    });

    test('first name must be a string', function () {
        $data = array_merge($this->validData, ['first_name' => 12345]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('first_name');
    });

    test('first name cannot exceed 255 characters', function () {
        $data = array_merge($this->validData, ['first_name' => str_repeat('a', 256)]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('first_name');
    });

    test('first name can be 255 characters', function () {
        $data = array_merge($this->validData, ['first_name' => str_repeat('a', 255)]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasNoErrors('first_name');
        $this->assertAuthenticated();
    });
});

describe('Last Name Validation', function () {
    test('last name is required', function () {
        $data = array_merge($this->validData, ['last_name' => '']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('last_name');
        $this->assertGuest();
    });

    test('last name must be a string', function () {
        $data = array_merge($this->validData, ['last_name' => 12345]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('last_name');
    });

    test('last name cannot exceed 255 characters', function () {
        $data = array_merge($this->validData, ['last_name' => str_repeat('a', 256)]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('last_name');
    });
});

describe('Email Validation', function () {
    test('email is required', function () {
        $data = array_merge($this->validData, ['email' => '']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });


    test('email must be unique', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = array_merge($this->validData, ['email' => 'existing@example.com']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });
});

describe('Password Validation', function () {
    test('password is required', function () {
        $data = array_merge($this->validData, ['password' => '']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    });

    test('password must be at least 8 characters', function () {
        $data = array_merge($this->validData, [
            'password' => 'Pass12!',
            'password_confirmation' => 'Pass12!',
        ]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
    });

    test('password can be exactly 8 characters', function () {
        $data = array_merge($this->validData, [
            'password' => 'Pass123!',
            'password_confirmation' => 'Pass123!',
        ]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasNoErrors('password');
        $this->assertAuthenticated();
    });

    test('password confirmation is required', function () {
        $data = array_merge($this->validData, ['password_confirmation' => '']);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    });

    test('password and confirmation must match', function () {
        $data = array_merge($this->validData, [
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPass123!',
        ]);

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    });
});

describe('Multiple Field Validation', function () {
    test('registration fails when all fields are empty', function () {
        $response = $this->post(route('register'), [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'password']);
        $this->assertGuest();
    });

    test('registration fails with multiple invalid fields', function () {
        $response = $this->post(route('register'), [
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['first_name', 'email', 'password']);
    });
});

describe('User Data Integrity', function () {
    test('user full name is correctly stored', function () {
        $this->post(route('register'), $this->validData);

        $user = User::where('email', $this->validData['email'])->first();

        expect($user->getFullname())->toBe('John Doe');
    });


    test('remember token is null on registration', function () {
        $this->post(route('register'), $this->validData);

        $user = User::where('email', $this->validData['email'])->first();

        expect($user->remember_token)->toBeNull();
    });

    test('remember token is generated when logging in with remember me', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
            'remember' => true,
        ]);

        $user->refresh();

        expect($user->remember_token)->not->toBeNull();
    });
});
