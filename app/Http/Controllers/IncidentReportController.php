<?php

namespace App\Http\Controllers;

use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\SystemLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentReportController extends Controller
{
    public function dashboard(Request $request): View
    {
        $reports = IncidentReport::query()
            ->where('reporter_id', $request->user()->id);

        $recentReports = (clone $reports)
            ->with('category')
            ->latest('reported_at')
            ->limit(5)
            ->get();

        $stats = [
            'total' => (clone $reports)->count(),
            'pending' => (clone $reports)->whereIn('status', ['pending', 'under_review'])->count(),
            'in_progress' => (clone $reports)->whereIn('status', ['assigned', 'in_progress'])->count(),
            'resolved' => (clone $reports)->where('status', 'resolved')->count(),
        ];

        return view('reporter.dashboard', compact('recentReports', 'stats'));
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled'])],
        ]);

        $reports = IncidentReport::query()
            ->with('category')
            ->where('reporter_id', $request->user()->id)
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('report_number', 'like', "%{$search}%")
                        ->orWhere('incident_type', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('reported_at')
            ->paginate(15)
            ->withQueryString();

        return view('reporter.reports.index', compact('reports'));
    }

    public function create(): View
    {
        $categories = IncidentCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('reporter.reports.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'incident_category_id' => [
                'required',
                Rule::exists('incident_categories', 'id')->where('is_active', true),
            ],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'persons_involved' => ['nullable', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'severity' => ['required', Rule::in(['low', 'moderate', 'high', 'critical'])],
            'evidence' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $category = IncidentCategory::query()->findOrFail($validated['incident_category_id']);
        $evidencePath = $request->file('evidence')?->store('incident-evidence', 'local');

        $report = DB::transaction(function () use ($validated, $category, $request, $evidencePath): IncidentReport {
            $report = IncidentReport::query()->create([
                'report_number' => 'IR-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'reporter_id' => $request->user()->id,
                'incident_category_id' => $category->id,
                'incident_type' => $category->name,
                'description' => $validated['description'],
                'persons_involved' => $validated['persons_involved'] ?? null,
                'location' => $validated['location'],
                'barangay' => $validated['barangay'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'evidence_path' => $evidencePath,
                'severity' => $validated['severity'],
                'status' => 'pending',
                'reported_at' => now(),
                'occurred_at' => $validated['occurred_at'],
            ]);

            SystemLog::query()->create([
                'user_id' => $request->user()->id,
                'action' => 'incident_report_created',
                'description' => "Created incident report {$report->report_number}.",
                'ip_address' => $request->ip(),
            ]);

            return $report;
        });

        return redirect()
            ->route('reporter.reports.show', $report)
            ->with('success', 'Your incident report has been submitted.');
    }

    public function show(Request $request, IncidentReport $incidentReport): View
    {
        abort_unless($incidentReport->reporter_id === $request->user()->id, 404);

        $incidentReport->load(['category', 'dispatches.responder.user']);

        return view('reporter.reports.show', compact('incidentReport'));
    }

    public function downloadEvidence(Request $request, IncidentReport $incidentReport): StreamedResponse
    {
        $user = $request->user();
        $canView = $user->role === 'admin'
            || $incidentReport->reporter_id === $user->id
            || ($user->role === 'staff' && $incidentReport->dispatches()
                ->whereHas('responder', fn ($query) => $query->where('user_id', $user->id))
                ->exists());

        abort_unless($canView && $incidentReport->evidence_path, 404);
        abort_unless(Storage::disk('local')->exists($incidentReport->evidence_path), 404);

        return Storage::disk('local')->download($incidentReport->evidence_path);
    }
}
