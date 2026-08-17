<?php

namespace Database\Seeders;

use App\Models\Amenity;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        collect(['Parking', 'Security', 'Furnished', 'Swimming Pool', '24-hour Power', 'Water Supply'])
            ->each(fn (string $name) => Amenity::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name],
            ));
    }
}
