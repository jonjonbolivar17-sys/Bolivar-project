<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IncidentReportApiController extends Controller
{
    /**
     * GET /api/incident-reports
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'reporter', 'staff'], true), 403);

        $reports = IncidentReport::query()
            ->with(['category', 'reporter'])
            ->when($request->user()->role === 'reporter', fn ($query) => $query->where('reporter_id', $request->user()->id))
            ->when($request->user()->role === 'staff', fn ($query) => $query->whereHas('dispatches.responder', fn ($builder) => $builder->where('user_id', $request->user()->id)))
            ->latest()
            ->paginate(15);

        return response()->json($reports, 200);
    }

    /**
     * POST /api/incident-reports
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'reporter', 403);

        $validated = $request->validate([
            'incident_type' => ['required', 'string', 'max:255'],
            'incident_category_id' => [
                'nullable',
                Rule::exists('incident_categories', 'id')->where('is_active', true),
            ],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'persons_involved' => ['nullable', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'severity' => [
                'required',
                Rule::in(['low', 'moderate', 'high', 'critical']),
            ],
            'evidence' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $category = isset($validated['incident_category_id'])
            ? IncidentCategory::query()->findOrFail($validated['incident_category_id'])
            : null;
        $evidencePath = $request->file('evidence')?->store('incident-evidence', 'local');

        $report = DB::transaction(function () use ($validated, $category, $evidencePath, $request): IncidentReport {
            $report = IncidentReport::query()->create([
                'description' => $validated['description'],
                'occurred_at' => $validated['occurred_at'],
                'persons_involved' => $validated['persons_involved'] ?? null,
                'location' => $validated['location'],
                'barangay' => $validated['barangay'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'incident_type' => $category?->name ?? $validated['incident_type'],
                'incident_category_id' => $category?->id,
                'report_number' => 'IR-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'reporter_id' => $request->user()->id,
                'evidence_path' => $evidencePath,
                'severity' => $validated['severity'],
                'status' => 'pending',
                'reported_at' => now(),
            ]);

            SystemLog::query()->create([
                'user_id' => $request->user()->id,
                'action' => 'incident_report_created',
                'description' => "Created incident report {$report->report_number} through the API.",
                'ip_address' => $request->ip(),
            ]);

            return $report->load(['category', 'reporter']);
        });

        return response()->json([
            'message' => 'Incident report created successfully.',
            'data' => $report,
        ], 201);
    }

    /**
     * GET /api/incident-reports/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $report = IncidentReport::findOrFail($id);
        $this->authorizeReport($request, $report, 'view');

        return response()->json([
            'data' => $report->load(['category', 'reporter']),
        ], 200);
    }

    /**
     * PUT/PATCH /api/incident-reports/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $report = IncidentReport::findOrFail($id);
        $this->authorizeReport($request, $report, 'update');

        if ($request->user()->role === 'staff') {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['under_review', 'in_progress', 'resolved'])],
            ]);

            $allowedTransitions = match ($report->status) {
                'pending' => ['under_review'],
                'assigned', 'under_review' => ['in_progress'],
                'in_progress' => ['resolved'],
                default => [],
            };

            if (! in_array($validated['status'], $allowedTransitions, true)) {
                return response()->json([
                    'message' => 'The requested status transition is not allowed.',
                ], 422);
            }

            DB::transaction(function () use ($request, $report, $validated): void {
                $report->update([
                    'status' => $validated['status'],
                    'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
                ]);

                $dispatch = $report->dispatches()
                    ->whereHas('responder', fn ($query) => $query->where('user_id', $request->user()->id))
                    ->latest('dispatched_at')
                    ->first();

                $dispatch?->update([
                    'status' => $validated['status'],
                    'arrived_at' => $validated['status'] === 'in_progress' ? ($dispatch->arrived_at ?? now()) : $dispatch->arrived_at,
                    'completed_at' => $validated['status'] === 'resolved' ? now() : null,
                ]);

                SystemLog::query()->create([
                    'user_id' => $request->user()->id,
                    'action' => 'incident_report_status_updated',
                    'description' => "Changed {$report->report_number} status to {$validated['status']} through the API.",
                    'ip_address' => $request->ip(),
                ]);
            });
        } else {
            $validated = $request->validate([
                'incident_type' => ['sometimes', 'required', 'string', 'max:255'],
                'incident_category_id' => [
                    'sometimes',
                    'nullable',
                    Rule::exists('incident_categories', 'id')->where('is_active', true),
                ],
                'occurred_at' => ['sometimes', 'nullable', 'date', 'before_or_equal:now'],
                'description' => ['sometimes', 'nullable', 'string', 'max:10000'],
                'persons_involved' => ['sometimes', 'nullable', 'string', 'max:5000'],
                'location' => ['sometimes', 'required', 'string', 'max:255'],
                'barangay' => ['sometimes', 'nullable', 'string', 'max:255'],
                'latitude' => ['sometimes', 'nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
                'longitude' => ['sometimes', 'nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
                'severity' => ['sometimes', Rule::in(['low', 'moderate', 'high', 'critical'])],
                'evidence' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            ]);

            $category = isset($validated['incident_category_id'])
                ? IncidentCategory::query()->findOrFail($validated['incident_category_id'])
                : null;

            if ($category !== null) {
                $validated['incident_type'] = $category->name;
            }
            unset($validated['evidence']);

            $evidencePath = null;
            $oldEvidencePath = $report->evidence_path;
            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('incident-evidence', 'local');
                $validated['evidence_path'] = $evidencePath;
            }

            DB::transaction(function () use ($request, $report, $validated): void {
                $report->update($validated);

                SystemLog::query()->create([
                    'user_id' => $request->user()->id,
                    'action' => 'incident_report_updated',
                    'description' => "Updated {$report->report_number} through the API.",
                    'ip_address' => $request->ip(),
                ]);
            });

            if ($evidencePath !== null && $oldEvidencePath !== null) {
                Storage::disk('local')->delete($oldEvidencePath);
            }
        }

        return response()->json([
            'message' => 'Incident report updated successfully.',
            'data' => $report->fresh(['category', 'reporter']),
        ], 200);
    }

    /**
     * DELETE /api/incident-reports/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $report = IncidentReport::findOrFail($id);
        abort_unless($request->user()->role === 'admin', 403);
        $evidencePath = $report->evidence_path;

        SystemLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => 'incident_report_deleted',
            'description' => "Deleted incident report {$report->report_number} through the API.",
            'ip_address' => $request->ip(),
        ]);

        $report->delete();

        if ($evidencePath !== null) {
            Storage::disk('local')->delete($evidencePath);
        }

        return response()->json(null, 204);
    }

    private function authorizeReport(Request $request, IncidentReport $report, string $ability): void
    {
        $user = $request->user();

        $authorized = match ($user->role) {
            'admin' => true,
            'reporter' => $ability !== 'delete' && $report->reporter_id === $user->id,
            'staff' => $ability !== 'delete' && $report->dispatches()
                ->whereHas('responder', fn ($query) => $query->where('user_id', $user->id))
                ->exists(),
            default => false,
        };

        abort_unless($authorized, 404);
    }
}
