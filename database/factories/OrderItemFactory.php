<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(4, 10, 999),
            'quantity' => $this->faker->numberBetween(0, 100),
            'product_id' => Product::factory(),
            'order_id' => Order::factory()
        ];
    }

    public function forOrder(Order $order): Factory|OrderItemFactory
    {
        return $this->state(fn(array $attributes): array => [
            'order_id' => $order->id,
        ]);
    }

    public function forUser(User $user): Factory|OrderItemFactory
    {
        return $this->state(fn(array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function forProduct(Product $product): Factory|OrderItemFactory
    {
        return $this->state(fn(array $attributes): array => [
            'product_id' => $product->id,
            'price' => $product->price
        ]);
    }
}
