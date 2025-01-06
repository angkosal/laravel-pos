<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

// use App\Models\Customer;
// use Faker\Generator as Faker;

// $factory->define(Customer::class, function (Faker $faker) {
//     return [
//         'first_name' => $faker->firstName,
//         'last_name' => $faker->lastName,
//         'email' => $faker->unique()->safeEmail,
//         'phone' => $faker->phoneNumber,
//         'address' => $faker->address,
//     ];
// });


namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    // Define the associated model
    protected $model = Customer::class;

    // Define the default state for your model
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
        ];
    }
}
