<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;
use App\Models\User;


beforeEach(function () {
    $this->validPassword = 'Password123!';
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make($this->validPassword),
    ]);
});

describe('login screen', function () {
    test('can be rendered', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Sign in to start your session');
    });

    test('redirects authenticated users away from login page', function () {
        $this->actingAs($this->user)
            ->get(route('login'))
            ->assertRedirect(route('home'));
    });
});

describe('Authentication', function () {
    test(/**
     * @throws JsonException
     */ 'users can authenticate with valid credentials', function () {
        $response = $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => $this->validPassword,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin')
            ->assertSessionHasNoErrors();
    });

    test('users cannot authenticate with invalid email', function () {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => $this->validPassword,
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    });

    test('users cannot authenticate with invalid password', function () {
        $response = $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    });

    test('users cannot authenticate with empty credentials', function () {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email', 'password']);
    });
});

describe('Logout', function () {
    test('authenticated users can logout', function () {
        $this->actingAs($this->user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    });

    test('guest users cannot access logout', function () {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    });

    test('logout invalidates session', function () {
        $this->actingAs($this->user);

        $sessionToken = session()->token();

        $this->post(route('logout'));

        $this->assertNotEquals($sessionToken, session()->token());
    });
});
