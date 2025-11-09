<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->optional()->address(),
            'avatar' => $this->faker->optional()->imageUrl(200, 200, 'people', true),
            'user_id' => User::factory()
        ];
    }

    public function withOutEmail(): CustomerFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'email' => null,
        ]);
    }

    public function withOutPhone(): CustomerFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'phone' => null,
        ]);
    }

    public function withOutAddress(): CustomerFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'address' => null,
        ]);
    }

    public function withOutAvatar(): CustomerFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'avatar' => null,
        ]);
    }

    public function forUser(User $user): CustomerFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }
}
