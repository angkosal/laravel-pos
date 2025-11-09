<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(4, 10, 99),
            'user_id' => User::factory(),
            'order_id' => Order::factory()
        ];
    }

    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes): array => [
            'order_id' => $order->id
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes): array => [
            'user_id' => $user->id
        ]);
    }

}
