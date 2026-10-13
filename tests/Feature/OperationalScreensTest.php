<?php

use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

it('renders saved incident coordinates on the admin location page', function () {
    $user = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
    IncidentReport::factory()->create([
        'report_number' => 'IR-LOC-001',
        'incident_type' => 'Motorcycle Accident',
        'location' => 'Poblacion, Arakan',
        'latitude' => 7.419,
        'longitude' => 125.204,
        'status' => 'pending',
    ]);
    IncidentReport::factory()->create([
        'report_number' => 'IR-LOC-002',
        'latitude' => null,
        'longitude' => null,
    ]);

    $this->actingAs($user)
        ->get(route('admin.map'))
        ->assertOk()
        ->assertSee('Incident locations')
        ->assertSee('Incident Locations')
        ->assertSee('IR-LOC-001')
        ->assertSee('Motorcycle Accident')
        ->assertSee('7.4190000, 125.2040000')
        ->assertSee('View on OpenStreetMap')
        ->assertDontSee('IR-LOC-002')
        ->assertDontSee('Live Unit Telemetry')
        ->assertDontSee('Alpha-4');
});

it('shows an empty state when no reports include coordinates', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get(route('admin.map'))
        ->assertOk()
        ->assertSee('No report locations yet')
        ->assertSee('Reports with coordinates')
        ->assertSee('0');
});

it('explains when GPS tracking migrations have not been applied', function () {
    Schema::shouldReceive('hasColumn')
        ->once()
        ->with('incident_reports', 'latitude')
        ->andReturn(false);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get(route('admin.map'))
        ->assertOk()
        ->assertSee('Location tracking is not enabled yet')
        ->assertSee('Apply the pending incident-report migrations')
        ->assertDontSee('Reports with coordinates');
});

it('renders the incident intake form for reporters', function () {
    $user = User::factory()->create(['role' => 'reporter', 'name' => 'Citizen User']);

    $this->actingAs($user)
        ->get(route('reporter.reports.create'))
        ->assertOk()
        ->assertSee('Submit an accident report')
        ->assertSee('Use my location')
        ->assertSee('Photo evidence')
        ->assertSee('Choose a category');
});
