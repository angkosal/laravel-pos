<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn(array $attributes): array => [
            'customer_id' => $customer->id,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }
}
