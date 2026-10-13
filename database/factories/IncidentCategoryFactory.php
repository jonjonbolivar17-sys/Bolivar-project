<?php

namespace Database\Factories;

use App\Models\IncidentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentCategory>
 */
class IncidentCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => str($name)->slug()->toString(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
