<?php

use App\Models\User;

it('renders user management and access control with arakan telemetry for admin', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Insp. Rodrigo Almodovar',
    ]);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSeeText('User Management & Access Control')
        ->assertSeeText('MDRRMO ARAKAN')
        ->assertSeeText('TOTAL SYSTEM USERS')
        ->assertSeeText('1,842')
        ->assertSeeText('ACTIVE FIELD RESPONDERS')
        ->assertSeeText('32 Units')
        ->assertSeeText('VERIFIED CITIZEN REPORTERS')
        ->assertSeeText('1,786')
        ->assertSeeText('MDRRMO COMMAND STAFF')
        ->assertSeeText('24 Officers')
        ->assertSeeText('RBAC Role Permissions Matrix')
        ->assertSeeText('Quick Provisioning Console')
        ->assertSeeText('Greenfield')
        ->assertSeeText('Poblacion')
        ->assertSeeText('Meocan');
});

it('renders staff responder field dashboard with tactical telemetry', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'name' => 'Officer Mark Alvarez',
    ]);

    $this->actingAs($staff)
        ->get('/staff/dashboard')
        ->assertOk()
        ->assertSeeText('Field Responder Command Desk')
        ->assertSeeText('ALPHA-LEAD')
        ->assertSeeText('148.525 MHz')
        ->assertSeeText('7.3512° N, 125.0489° E')
        ->assertSeeText('MY ASSIGNED INCIDENTS')
        ->assertSeeText('ETA TO ACTIVE TARGET')
        ->assertSeeText('Fallen Bamboo & Road Obstruction on Arakan–Greenfield Highway')
        ->assertSeeText('Juan Dela Cruz')
        ->assertSeeText('Start Turn-by-Turn GPS')
        ->assertSeeText('Signal Beacon (SOS)');
});

it('renders citizen user dashboard with live incident tracker and 4-step wizard', function () {
    $citizen = User::factory()->create([
        'role' => 'reporter',
        'name' => 'Juan Dela Cruz',
    ]);

    $this->actingAs($citizen)
        ->get('/user/dashboard')
        ->assertOk()
        ->assertSeeText('Welcome, Juan Dela Cruz')
        ->assertSeeText('PhilSys Verified Resident')
        ->assertSeeText('Brgy. Greenfield, Purok 3, Arakan')
        ->assertSeeText('Greenfield Central Gymnasium')
        ->assertSeeText('Report New Incident / Hazard')
        ->assertSeeText('Track My Submitted Reports')
        ->assertSeeText('GPS Emergency Identifier')
        ->assertSeeText('Evacuation Routes & Shelters')
        ->assertSeeText('TICKET #REP-2024-0892')
        ->assertSeeText('Responder Dispatched')
        ->assertSeeText('MDRRMO Hotline: (064) 521-8899');
});

it('allows admin cross-role preview on staff and citizen dashboards', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Commander Admin',
    ]);

    $this->actingAs($admin)
        ->get('/staff/dashboard')
        ->assertOk()
        ->assertSeeText('Field Responder Command Desk');

    $this->actingAs($admin)
        ->get('/user/dashboard')
        ->assertOk()
        ->assertSeeText('Report New Incident / Hazard');
});

