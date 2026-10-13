<?php

namespace Database\Factories;

use App\Models\Dispatch;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispatch>
 */
class DispatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'incident_report_id' => IncidentReport::factory(),
            'responder_id' => Responder::factory(),
            'dispatched_by' => User::factory(),
            'dispatched_at' => now(),
            'status' => 'dispatched',
            'notes' => null,
        ];
    }
}
