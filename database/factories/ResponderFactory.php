<?php

namespace Database\Factories;

use App\Models\Responder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Responder>
 */
class ResponderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'responder_type' => $this->faker->randomElement(['Fire Rescue Unit', 'Medical Team', 'Search and Rescue', 'Police Response Team']),
            'contact_number' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['available', 'on_patrol', 'dispatched', 'off_duty']),
            'current_location' => 'Arakan, Cotabato',
        ];
    }
}
