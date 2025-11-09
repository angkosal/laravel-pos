<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'products', true),
            'barcode' => $this->faker->unique()->ean13(),
            'price' => $this->faker->randomFloat(2, 10, 999),
            'status' => $this->faker->boolean(),
            'quantity' => $this->faker->numberBetween(0, 100)
        ];
    }

    public function inactive(): ProductFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'status' => false,
        ]);
    }

    public function active(): ProductFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'status' => true,
        ]);
    }

    public function withOutImage(): ProductFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'image' => null,
        ]);
    }

    public function withOutDescription(): ProductFactory|Factory
    {
        return $this->state(fn(array $attributes): array => [
            'description' => null,
        ]);
    }

}
