<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\SystemLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $reports = $this->reportQuery($filters)
            ->paginate(20)
            ->withQueryString();

        $categories = IncidentCategory::query()->orderBy('name')->get();

        return view('admin.reports.index', compact('reports', 'categories'));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $reports = $this->reportQuery($filters)->with('reporter');

        SystemLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => 'incident_reports_exported',
            'description' => 'Exported incident reports as a CSV file.',
            'ip_address' => $request->ip(),
        ]);

        return response()->streamDownload(function () use ($reports): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Report ID',
                'Category',
                'Status',
                'Severity',
                'Location',
                'Barangay',
                'Latitude',
                'Longitude',
                'Incident date',
                'Reported date',
                'Reporter',
                'Reporter email',
            ]);

            foreach ($reports->lazy(500) as $report) {
                fputcsv($output, [
                    $this->safeCsvText($report->report_number),
                    $this->safeCsvText($report->incident_type),
                    $this->safeCsvText($report->status),
                    $this->safeCsvText($report->severity),
                    $this->safeCsvText($report->location),
                    $this->safeCsvText($report->barangay),
                    $report->latitude,
                    $report->longitude,
                    $report->occurred_at?->toIso8601String(),
                    $report->reported_at?->toIso8601String(),
                    $this->safeCsvText($report->reporter?->name),
                    $this->safeCsvText($report->reporter?->email),
                ]);
            }

            fclose($output);
        }, 'incident-reports-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(IncidentReport $incidentReport): View
    {
        $incidentReport->load(['category', 'reporter', 'dispatches.responder.user', 'dispatches.dispatchedBy']);
        $responders = Responder::query()
            ->with('user')
            ->whereHas('user', fn ($query) => $query->where('role', 'staff'))
            ->orderBy('responder_type')
            ->get();

        return view('admin.reports.show', compact('incidentReport', 'responders'));
    }

    public function update(Request $request, IncidentReport $incidentReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled'])],
        ]);

        DB::transaction(function () use ($request, $incidentReport, $validated): void {
            $incidentReport->update([
                'status' => $validated['status'],
                'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
            ]);

            SystemLog::query()->create([
                'user_id' => $request->user()->id,
                'action' => 'incident_report_status_updated',
                'description' => "Changed {$incidentReport->report_number} status to {$validated['status']}.",
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Incident report status updated.');
    }

    public function dispatch(Request $request, IncidentReport $incidentReport): RedirectResponse
    {
        $validated = $request->validate([
            'responder_id' => ['required', 'exists:responders,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $isStaffResponder = Responder::query()
            ->whereKey($validated['responder_id'])
            ->whereHas('user', fn ($query) => $query->where('role', 'staff'))
            ->exists();

        if (! $isStaffResponder) {
            return back()
                ->withErrors(['responder_id' => 'Choose a responder profile belonging to a staff account.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $incidentReport, $validated): void {
            Dispatch::query()->create([
                'incident_report_id' => $incidentReport->id,
                'responder_id' => $validated['responder_id'],
                'dispatched_by' => $request->user()->id,
                'dispatched_at' => now(),
                'status' => 'dispatched',
                'notes' => $validated['notes'] ?? null,
            ]);

            $incidentReport->update(['status' => 'assigned']);

            SystemLog::query()->create([
                'user_id' => $request->user()->id,
                'action' => 'incident_report_dispatched',
                'description' => "Assigned {$incidentReport->report_number} to responder {$validated['responder_id']}.",
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Incident report assigned to the selected responder.');
    }

    /**
     * @return array{search?: string, status?: string, category?: int, from?: string, to?: string}
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled'])],
            'category' => ['nullable', 'integer', 'exists:incident_categories,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
    }

    /**
     * @param  array{search?: string, status?: string, category?: int, from?: string, to?: string}  $filters
     */
    private function reportQuery(array $filters): Builder
    {
        return IncidentReport::query()
            ->with(['category', 'reporter'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('report_number', 'like', "%{$search}%")
                        ->orWhere('incident_type', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn (Builder $query, int $categoryId) => $query->where('incident_category_id', $categoryId))
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('occurred_at', '<=', $date))
            ->latest('reported_at');
    }

    private function safeCsvText(?string $value): string
    {
        if ($value !== null && preg_match('/^[\t\r\n ]*[=+\-@]/', $value) === 1) {
            return "'".$value;
        }

        return $value ?? '';
    }
}
