<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use App\Models\SystemLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IncidentReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled'])],
        ]);

        $reports = IncidentReport::query()
            ->with(['category', 'reporter'])
            ->whereHas('dispatches.responder', fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('report_number', 'like', "%{$search}%")
                        ->orWhere('incident_type', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('reported_at')
            ->paginate(20)
            ->withQueryString();

        return view('staff.reports.index', compact('reports'));
    }

    public function show(Request $request, IncidentReport $incidentReport): View
    {
        abort_unless($this->isAssignedTo($request, $incidentReport), 404);

        $incidentReport->load(['category', 'reporter', 'dispatches.responder.user']);
        $allowedStatuses = match ($incidentReport->status) {
            'pending' => ['under_review'],
            'assigned', 'under_review' => ['in_progress'],
            'in_progress' => ['resolved'],
            default => [],
        };

        return view('staff.reports.show', compact('incidentReport', 'allowedStatuses'));
    }

    public function update(Request $request, IncidentReport $incidentReport): RedirectResponse
    {
        abort_unless($this->isAssignedTo($request, $incidentReport), 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['under_review', 'in_progress', 'resolved'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $allowedTransitions = match ($incidentReport->status) {
            'pending' => ['under_review'],
            'assigned', 'under_review' => ['in_progress'],
            'in_progress' => ['resolved'],
            default => [],
        };

        if (! in_array($validated['status'], $allowedTransitions, true)) {
            return back()
                ->withErrors(['status' => 'That status change is not allowed from the report’s current status.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $incidentReport, $validated): void {
            $incidentReport->update([
                'status' => $validated['status'],
                'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
            ]);

            $dispatch = $incidentReport->dispatches()
                ->whereHas('responder', fn ($query) => $query->where('user_id', $request->user()->id))
                ->latest('dispatched_at')
                ->first();

            if ($dispatch !== null) {
                $dispatch->update([
                    'status' => $validated['status'],
                    'arrived_at' => $validated['status'] === 'in_progress' ? ($dispatch->arrived_at ?? now()) : $dispatch->arrived_at,
                    'completed_at' => $validated['status'] === 'resolved' ? now() : null,
                    'notes' => $validated['notes'] ?? $dispatch->notes,
                ]);
            }

            SystemLog::query()->create([
                'user_id' => $request->user()->id,
                'action' => 'incident_report_status_updated',
                'description' => "Changed {$incidentReport->report_number} status to {$validated['status']}.",
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Incident report status updated.');
    }

    private function isAssignedTo(Request $request, IncidentReport $incidentReport): bool
    {
        return $incidentReport->dispatches()
            ->whereHas('responder', fn ($query) => $query->where('user_id', $request->user()->id))
            ->exists();
    }
}
