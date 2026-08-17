<?php

namespace Database\Factories;

use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyCharacteristicFactory extends Factory
{
    public function definition(): array
    {
        $squareFeet = fake()->numberBetween(300, 6000);
        $price = fake()->numberBetween(5_000_000, 500_000_000);

        return [
            'property_id' => Property::factory(),
            'price' => $price,
            'bathrooms' => fake()->numberBetween(1, 8),
            'bedrooms' => fake()->numberBetween(1, 8),
            'square_feet' => $squareFeet,
            'price_square_feet' => (int) round($price / $squareFeet),
            'property_type' => PropertyTypeEnum::DUPLEX->value,
            'status' => PropertyStatusEnum::SALE->value,
        ];
    }
}
