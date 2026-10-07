<?php

namespace Database\Seeders;

use App\Models\Dispatch;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\SystemLog;
use App\Models\User;
use App\Models\WeatherReading;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommandCenterSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ako_handa.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@ako_handa.test'],
            [
                'name' => 'Operations Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        $reporterOne = User::firstOrCreate(
            ['email' => 'reporter1@ako_handa.test'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'reporter',
            ]
        );

        $reporterTwo = User::firstOrCreate(
            ['email' => 'reporter2@ako_handa.test'],
            [
                'name' => 'Joel Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'reporter',
            ]
        );

        $reporterThree = User::firstOrCreate(
            ['email' => 'reporter3@ako_handa.test'],
            [
                'name' => 'Ana Ramos',
                'password' => Hash::make('password'),
                'role' => 'reporter',
            ]
        );

        $incidentOne = IncidentReport::firstOrCreate(
            ['report_number' => 'REP-2024-0891'],
            [
                'reporter_id' => $reporterOne->id,
                'incident_type' => 'Fallen Bamboo / Minor Road Block',
                'description' => 'Large bamboo trunk obstructed the road lane near the barangay crossing.',
                'location' => 'Brgy. Sallab, Arakan',
                'barangay' => 'Sallab',
                'severity' => 'moderate',
                'status' => 'pending',
                'reported_at' => now()->subMinutes(12),
            ]
        );

        $incidentTwo = IncidentReport::firstOrCreate(
            ['report_number' => 'REP-2024-0890'],
            [
                'reporter_id' => $reporterTwo->id,
                'incident_type' => 'River Water Gauge Check',
                'description' => 'River level continues rising with moderate debris accumulation at the bridge.',
                'location' => 'Brgy. Poblacion Bridge, Arakan',
                'barangay' => 'Poblacion',
                'severity' => 'routine',
                'status' => 'in_progress',
                'reported_at' => now()->subMinutes(45),
            ]
        );

        $incidentThree = IncidentReport::firstOrCreate(
            ['report_number' => 'REP-2024-0889'],
            [
                'reporter_id' => $reporterThree->id,
                'incident_type' => 'Culvert Debris Clearance',
                'description' => 'Culvert route was cleared and drainage is flowing normally again.',
                'location' => 'Brgy. Doles, Arakan',
                'barangay' => 'Doles',
                'severity' => 'low',
                'status' => 'resolved',
                'reported_at' => now()->subHours(2),
                'resolved_at' => now()->subMinutes(30),
            ]
        );

        $incidentFour = IncidentReport::firstOrCreate(
            ['report_number' => 'REP-2024-0888'],
            [
                'reporter_id' => $reporterOne->id,
                'incident_type' => 'Power Line Vegetation Trimming',
                'description' => 'Vegetation trimming completed near the power line corridor.',
                'location' => 'Brgy. Pangao-an, Arakan',
                'barangay' => 'Pangao-an',
                'severity' => 'moderate',
                'status' => 'resolved',
                'reported_at' => now()->subHours(4),
                'resolved_at' => now()->subHours(2),
            ]
        );

        $responder = Responder::firstOrCreate(
            ['user_id' => $staff->id],
            [
                'responder_type' => 'Fire Rescue Unit',
                'contact_number' => '09181234567',
                'status' => 'available',
                'current_location' => 'Arakan Public Safety Compound',
            ]
        );

        Dispatch::firstOrCreate(
            ['incident_report_id' => $incidentOne->id, 'responder_id' => $responder->id],
            [
                'dispatched_by' => $admin->id,
                'dispatched_at' => now()->subMinutes(10),
                'status' => 'dispatched',
                'notes' => 'Crew dispatched for road clearing operation.',
            ]
        );

        Dispatch::firstOrCreate(
            ['incident_report_id' => $incidentTwo->id, 'responder_id' => $responder->id],
            [
                'dispatched_by' => $admin->id,
                'dispatched_at' => now()->subMinutes(30),
                'status' => 'on_scene',
                'notes' => 'Monitoring bridge water level and debris accumulation.',
            ]
        );

        WeatherReading::firstOrCreate(
            ['location' => 'Arakan, Cotabato', 'recorded_at' => now()->subMinutes(5)],
            [
                'temperature' => 31.2,
                'feels_like' => 32.1,
                'humidity' => 68,
                'wind_speed' => 12.5,
                'wind_direction' => 'East-Northeast',
                'precipitation' => 0.6,
                'condition' => 'Partly cloudy',
                'alert_status' => 'green',
            ]
        );

        WeatherReading::firstOrCreate(
            ['location' => 'Arakan, Cotabato', 'recorded_at' => now()->subHours(3)],
            [
                'temperature' => 29.8,
                'feels_like' => 30.5,
                'humidity' => 72,
                'wind_speed' => 10.2,
                'wind_direction' => 'North-East',
                'precipitation' => 1.2,
                'condition' => 'Scattered Clouds',
                'alert_status' => 'green',
            ]
        );

        SystemLog::firstOrCreate(
            ['user_id' => $admin->id, 'action' => 'Incident created'],
            [
                'description' => 'Initial incident report created for fallen bamboo road blockage.',
                'ip_address' => '127.0.0.1',
            ]
        );

        SystemLog::firstOrCreate(
            ['user_id' => $admin->id, 'action' => 'Responder dispatched'],
            [
                'description' => 'Field team dispatched to respond to the fallen bamboo obstruction.',
                'ip_address' => '127.0.0.1',
            ]
        );
    }
}
