<?php

use App\Models\Dispatch;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('stores submitted report details and private evidence for its reporter', function () {
    Storage::fake('local');
    $reporter = User::factory()->create(['role' => 'reporter']);
    $category = IncidentCategory::query()
        ->where('name', 'Vehicular Accident')
        ->firstOrFail();

    $response = $this->actingAs($reporter)->post(route('reporter.reports.store'), [
        'incident_category_id' => $category->id,
        'occurred_at' => now()->subHour()->format('Y-m-d\TH:i'),
        'description' => 'A vehicle collision occurred at the main intersection.',
        'persons_involved' => 'Two occupants were present.',
        'location' => 'Main intersection',
        'barangay' => 'Greenfield',
        'latitude' => '7.3512000',
        'longitude' => '125.0489000',
        'severity' => 'high',
        'evidence' => UploadedFile::fake()->image('scene.jpg'),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $report = IncidentReport::query()->firstOrFail();
    expect($report->reporter_id)->toBe($reporter->id)
        ->and($report->incident_category_id)->toBe($category->id)
        ->and($report->incident_type)->toBe('Vehicular Accident')
        ->and($report->status)->toBe('pending')
        ->and($report->latitude)->toBe('7.3512000')
        ->and($report->longitude)->toBe('125.0489000');

    Storage::disk('local')->assertExists($report->evidence_path);
    $this->assertDatabaseHas('system_logs', [
        'user_id' => $reporter->id,
        'action' => 'incident_report_created',
    ]);
});

it('prevents a reporter from viewing another reporters report', function () {
    $owner = User::factory()->create(['role' => 'reporter']);
    $otherReporter = User::factory()->create(['role' => 'reporter']);
    $report = IncidentReport::factory()->create(['reporter_id' => $owner->id]);

    $this->actingAs($otherReporter)
        ->get(route('reporter.reports.show', $report))
        ->assertNotFound();
});

it('serves evidence only to users authorized for the report', function () {
    Storage::fake('local');
    Storage::disk('local')->put('incident-evidence/private.jpg', 'private evidence');

    $owner = User::factory()->create(['role' => 'reporter']);
    $otherReporter = User::factory()->create(['role' => 'reporter']);
    $report = IncidentReport::factory()->create([
        'reporter_id' => $owner->id,
        'evidence_path' => 'incident-evidence/private.jpg',
    ]);

    $this->actingAs($owner)
        ->get(route('reports.evidence', $report))
        ->assertOk()
        ->assertHeader('content-disposition');

    $this->actingAs($otherReporter)
        ->get(route('reports.evidence', $report))
        ->assertNotFound();
});

it('assigns a report to a staff responder and records the dispatch', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);
    $responder = Responder::factory()->for($staff)->create();
    $reporter = User::factory()->create(['role' => 'reporter']);
    $report = IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.reports.dispatches.store', $report), [
            'responder_id' => $responder->id,
            'notes' => 'Review the northbound lane.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($report->fresh()->status)->toBe('assigned');
    $this->assertDatabaseHas('dispatches', [
        'incident_report_id' => $report->id,
        'responder_id' => $responder->id,
        'dispatched_by' => $admin->id,
        'status' => 'dispatched',
        'notes' => 'Review the northbound lane.',
    ]);
    $this->assertDatabaseHas('system_logs', [
        'user_id' => $admin->id,
        'action' => 'incident_report_dispatched',
    ]);
});

it('lets assigned staff advance a report and rejects unauthorized transitions', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);
    $reporter = User::factory()->create(['role' => 'reporter']);
    $otherStaff = User::factory()->create(['role' => 'staff']);
    $responder = Responder::factory()->for($staff)->create();
    $report = IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'status' => 'assigned',
    ]);
    $dispatch = Dispatch::factory()->create([
        'incident_report_id' => $report->id,
        'responder_id' => $responder->id,
        'dispatched_by' => $admin->id,
        'status' => 'dispatched',
    ]);

    $this->actingAs($otherStaff)
        ->get(route('staff.reports.show', $report))
        ->assertNotFound();

    $this->actingAs($staff)
        ->patch(route('staff.reports.update', $report), [
            'status' => 'resolved',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('status');

    expect($report->fresh()->status)->toBe('assigned');

    $this->actingAs($staff)
        ->patch(route('staff.reports.update', $report), [
            'status' => 'in_progress',
            'notes' => 'Responder is on scene.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($report->fresh()->status)->toBe('in_progress')
        ->and($dispatch->fresh()->status)->toBe('in_progress')
        ->and($dispatch->fresh()->arrived_at)->not->toBeNull();
    $this->assertDatabaseHas('system_logs', [
        'user_id' => $staff->id,
        'action' => 'incident_report_status_updated',
    ]);
});

it('limits API report results to the authenticated reporters records', function () {
    $reporter = User::factory()->create(['role' => 'reporter']);
    $otherReporter = User::factory()->create(['role' => 'reporter']);
    $ownReport = IncidentReport::factory()->create(['reporter_id' => $reporter->id]);
    IncidentReport::factory()->create(['reporter_id' => $otherReporter->id]);

    Sanctum::actingAs($reporter);

    $this->getJson('/api/incident-reports')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ownReport->id);
});

it('returns not found when a reporter requests another reporters API report', function () {
    $reporter = User::factory()->create(['role' => 'reporter']);
    $otherReporter = User::factory()->create(['role' => 'reporter']);
    $report = IncidentReport::factory()->create(['reporter_id' => $otherReporter->id]);

    Sanctum::actingAs($reporter);

    $this->getJson("/api/incident-reports/{$report->id}")
        ->assertNotFound();
});

it('creates an API report with validated fields and an audit entry', function () {
    $reporter = User::factory()->create(['role' => 'reporter']);
    $category = IncidentCategory::query()
        ->where('name', 'Road Obstruction')
        ->firstOrFail();

    Sanctum::actingAs($reporter);

    $this->postJson('/api/incident-reports', [
        'incident_type' => 'Road Obstruction',
        'incident_category_id' => $category->id,
        'occurred_at' => now()->subHour()->toIso8601String(),
        'description' => 'A fallen tree is blocking the eastbound road.',
        'location' => 'Eastbound road, kilometer 12',
        'severity' => 'moderate',
        'latitude' => 7.3512,
        'longitude' => 125.0489,
        'reporter_id' => 999999,
        'status' => 'resolved',
    ])
        ->assertCreated()
        ->assertJsonPath('data.reporter_id', $reporter->id)
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonMissingPath('data.evidence_path');

    $report = IncidentReport::query()->firstOrFail();
    expect($report->incident_category_id)->toBe($category->id)
        ->and($report->status)->toBe('pending');

    $this->assertDatabaseHas('system_logs', [
        'user_id' => $reporter->id,
        'action' => 'incident_report_created',
    ]);
});

it('requires an authenticated reporter to create API reports', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Sanctum::actingAs($admin);

    $this->postJson('/api/incident-reports', [
        'incident_type' => 'Vehicular Accident',
        'occurred_at' => now()->subHour()->toIso8601String(),
        'description' => 'A vehicle collision occurred.',
        'location' => 'Poblacion',
        'severity' => 'moderate',
    ])->assertForbidden();
});

it('exports filtered reports as a streamed CSV without spreadsheet formulas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $reporter = User::factory()->create([
        'role' => 'reporter',
        'name' => '=SUM(1,2)',
    ]);
    IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'report_number' => 'IR-CSV-101',
        'status' => 'pending',
    ]);
    IncidentReport::factory()->create([
        'reporter_id' => $reporter->id,
        'report_number' => 'IR-CSV-102',
        'status' => 'resolved',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.export', ['status' => 'pending']));

    $response->assertOk()->assertStreamed();
    $csv = $response->streamedContent();
    expect($csv)->toContain('IR-CSV-101')
        ->and($csv)->not->toContain('IR-CSV-102')
        ->and($csv)->toContain("'=SUM(1,2)");

    $this->assertDatabaseHas('system_logs', [
        'user_id' => $admin->id,
        'action' => 'incident_reports_exported',
    ]);
});

it('shows recorded actions to admins and denies the activity log to reporters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $reporter = User::factory()->create(['role' => 'reporter']);
    SystemLog::query()->create([
        'user_id' => $admin->id,
        'action' => 'incident_report_status_updated',
        'description' => 'Updated IR-LOG-101 to in progress.',
        'ip_address' => '127.0.0.1',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.activity.index', ['action' => 'incident_report_status_updated']))
        ->assertOk()
        ->assertSeeText('Activity log')
        ->assertSeeText('Updated IR-LOG-101 to in progress.')
        ->assertSeeText('127.0.0.1');

    $this->actingAs($reporter)
        ->get(route('admin.activity.index'))
        ->assertForbidden();
});

it('prevents deletion of a user account that owns incident records', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $reporter = User::factory()->create(['role' => 'reporter']);
    $report = IncidentReport::factory()->create(['reporter_id' => $reporter->id]);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $reporter))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($reporter->fresh())->not->toBeNull()
        ->and($report->fresh())->not->toBeNull();
});
