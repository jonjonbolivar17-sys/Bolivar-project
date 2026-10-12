<?php

use App\Models\User;

it('renders the tactical map page for admins', function () {
    $user = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);

    $this->actingAs($user)
        ->get(route('admin.map'))
        ->assertOk()
        ->assertSee('Municipal CAD Radar')
        ->assertSee('Live Unit Telemetry');
});

it('renders the incident intake form for reporters', function () {
    $user = User::factory()->create(['role' => 'reporter', 'name' => 'Citizen User']);

    $this->actingAs($user)
        ->get(route('reporter.reports.create'))
        ->assertOk()
        ->assertSee('Report an Accident / Hazard')
        ->assertSee('Five W’s Intake Protocol');
});
