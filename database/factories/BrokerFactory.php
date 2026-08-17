<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrokerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'zip_code' => fake()->postcode(),
            'phone_number' => fake()->unique()->numerify('+23480########'),
        ];
    }
}
