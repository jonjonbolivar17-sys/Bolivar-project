<?php

use App\Models\Dispatch;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\User;

it('renders real user records in admin user management', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'System Administrator',
    ]);
    $reporter = User::factory()->create([
        'role' => 'reporter',
        'name' => 'Current Reporter',
    ]);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSeeText('User management')
        ->assertSeeText($admin->email)
        ->assertSeeText($reporter->email)
        ->assertDontSeeText('1,842')
        ->assertDontSeeText('Juan Dela Cruz');
});

it('shows assigned database reports on the staff dashboard only', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'name' => 'Field Staff',
    ]);
    $reporter = User::factory()->create(['role' => 'reporter']);
    $otherStaff = User::factory()->create(['role' => 'staff']);
    $responder = Responder::factory()->for($staff)->create();
    $assignedReport = IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'report_number' => 'IR-STAFF-101',
        'location' => 'Assigned test location',
        'barangay' => null,
        'status' => 'assigned',
    ]);
    IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'report_number' => 'IR-STAFF-102',
        'location' => 'Unassigned test location',
        'barangay' => null,
        'status' => 'pending',
    ]);
    $otherResponder = Responder::factory()->for($otherStaff)->create();
    Dispatch::factory()->create([
        'incident_report_id' => $assignedReport->id,
        'responder_id' => $responder->id,
        'dispatched_by' => $reporter->id,
    ]);

    $this->actingAs($staff)
        ->get('/staff/dashboard')
        ->assertOk()
        ->assertSeeText('IR-STAFF-101')
        ->assertSeeText('Assigned test location')
        ->assertDontSeeText('IR-STAFF-102')
        ->assertDontSeeText('Unassigned test location');
});

it('shows only the reporters own records on its dashboard', function () {
    $citizen = User::factory()->create([
        'role' => 'reporter',
        'name' => 'Test Reporter',
    ]);
    $otherReporter = User::factory()->create(['role' => 'reporter']);
    IncidentReport::factory()->create([
        'reporter_id' => $citizen->id,
        'report_number' => 'IR-OWN-101',
    ]);
    IncidentReport::factory()->create([
        'reporter_id' => $otherReporter->id,
        'report_number' => 'IR-OTHER-102',
    ]);

    $this->actingAs($citizen)
        ->get('/user/dashboard')
        ->assertRedirect(route('reporter.dashboard'));

    $this->actingAs($citizen)
        ->get(route('reporter.dashboard'))
        ->assertOk()
        ->assertSeeText('Welcome, Test Reporter')
        ->assertSeeText('IR-OWN-101')
        ->assertDontSeeText('IR-OTHER-102');
});

it('allows admins to preview role dashboards without seeded demo content', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Commander Admin',
    ]);

    $this->actingAs($admin)
        ->get('/staff/dashboard')
        ->assertOk()
        ->assertSeeText('Welcome, Commander Admin')
        ->assertSeeText('No reports assigned');

    $this->actingAs($admin)
        ->get(route('reporter.dashboard'))
        ->assertOk()
        ->assertSeeText('Welcome, Commander Admin')
        ->assertSeeText('No reports submitted yet');
});
