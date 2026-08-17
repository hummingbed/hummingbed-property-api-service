<?php

namespace Database\Factories;

use App\Enums\ListingTypeEnum;
use App\Models\Broker;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'broker_id' => Broker::factory(),
            'address' => fake()->unique()->streetAddress(),
            'listing_type' => ListingTypeEnum::OPEN_LISTING->value,
            'city' => fake()->city(),
            'zip_code' => fake()->postcode(),
            'description' => fake()->paragraph(),
            'build_year' => (string) fake()->numberBetween(1950, now()->year),
        ];
    }
}
