<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 50),
            'purchase_price' => fake()->randomFloat(2, 5, 500),
        ];
    }

    /**
     * Purchase item for a specific purchase
     */
    public function forPurchase(Purchase $purchase): static
    {
        return $this->state(fn (array $attributes): array => [
            'purchase_id' => $purchase->id,
        ]);
    }

    /**
     * Purchase item for a specific product
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes): array => [
            'product_id' => $product->id,
            'purchase_price' => $product->purchase_price ?? fake()->randomFloat(2, 5, 500),
        ]);
    }

}
