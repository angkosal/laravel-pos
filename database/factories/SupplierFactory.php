<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

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
            'avatar' => $this->faker->optional()->imageUrl(200, 200, 'business', true),
        ];
    }


    public function withoutEmail(): static
    {
        return $this->state(fn(array $attributes): array => [
            'email' => null,
        ]);
    }

    public function withoutPhone(): static
    {
        return $this->state(fn(array $attributes): array => [
            'phone' => null,
        ]);
    }

    public function withoutAddress(): static
    {
        return $this->state(fn(array $attributes): array => [
            'address' => null,
        ]);
    }

    public function withoutAvatar(): static
    {
        return $this->state(fn(array $attributes): array => [
            'avatar' => null,
        ]);
    }
}
