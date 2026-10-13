<?php

namespace Database\Factories;

use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentReport>
 */
class IncidentReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $incidentType = $this->faker->randomElement(['Vehicular Accident', 'Motorcycle Accident', 'Pedestrian Accident', 'Hit and Run', 'Road Obstruction', 'Other']);

        return [
            'report_number' => 'REP-'.now()->format('Y').'-'.str_pad((string) $this->faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
            'reporter_id' => User::factory(),
            'incident_type' => $incidentType,
            'description' => $this->faker->paragraph(),
            'location' => 'Brgy. '.$this->faker->word().' , Arakan',
            'barangay' => $this->faker->word(),
            'severity' => $this->faker->randomElement(['low', 'moderate', 'high', 'critical']),
            'status' => $this->faker->randomElement(['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled']),
            'reported_at' => now()->subHours($this->faker->numberBetween(1, 72)),
            'occurred_at' => now()->subHours($this->faker->numberBetween(1, 72)),
        ];
    }
}
