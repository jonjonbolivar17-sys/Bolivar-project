<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use App\Models\Responder;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected WeatherService $weatherService) {}

    public function index()
    {
        $weather = $this->weatherService->getWeather();

        $totalUsers = User::count();
        $admins = User::where('role', 'admin')->count();
        $staff = User::where('role', 'staff')->count();
        $reporters = User::where('role', 'reporter')->count();
        $totalReports = IncidentReport::count();
        $pendingReports = IncidentReport::whereIn('status', ['pending', 'under_review', 'assigned', 'in_progress'])->count();
        $resolvedReports = IncidentReport::where('status', 'resolved')->count();
        $fieldResponders = Responder::count();

        $stats = [
            [
                'label' => 'TOTAL USERS',
                'value' => (string) $totalUsers,
                'meta' => 'Registered users in the system',
                'tone' => 'emerald',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1"/><circle cx="10" cy="7" r="3.5"/><path d="M19 19v-1a4 4 0 0 0-3-3.87"/><path d="M16 4.13a4 4 0 0 1 0 7.74"/></svg>',
            ],
            [
                'label' => 'ADMINS',
                'value' => (string) $admins,
                'meta' => 'Command center administrators',
                'tone' => 'warning',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9 12 4l6 5v9.5A1.5 1.5 0 0 1 16.5 20h-9A1.5 1.5 0 0 1 6 18.5V9Z"/><path d="M9 20v-7h6v7"/></svg>',
            ],
            [
                'label' => 'STAFF',
                'value' => (string) $staff,
                'meta' => 'Operations and field staff',
                'tone' => 'teal',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 19v-1a5 5 0 0 1 10 0v1"/><circle cx="12" cy="8" r="3"/><path d="M4 20v-1a4 4 0 0 1 4-4"/><path d="M20 20v-1a4 4 0 0 0-4-4"/></svg>',
            ],
            [
                'label' => 'REPORTERS',
                'value' => (string) $reporters,
                'meta' => 'Citizens reporting incidents',
                'tone' => 'success',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3.5h6l5 5V18a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5.5a2 2 0 0 1 2-2Z"/><path d="M14 3.5v5h5"/><path d="M8 11h8M8 15h8"/></svg>',
            ],
            [
                'label' => 'TOTAL REPORTS',
                'value' => (string) $totalReports,
                'meta' => 'All submitted incident reports',
                'tone' => 'emerald',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5h6M9 9h6M9 13h6M9 17h6M5 3.75A1.75 1.75 0 0 1 6.75 2h10.5A1.75 1.75 0 0 1 19 3.75v16.5A1.75 1.75 0 0 1 17.25 22H6.75A1.75 1.75 0 0 1 5 20.25V3.75Z"/></svg>',
            ],
            [
                'label' => 'PENDING REPORTS',
                'value' => (string) $pendingReports,
                'meta' => 'Needs review or dispatch',
                'tone' => 'warning',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>',
            ],
            [
                'label' => 'RESOLVED REPORTS',
                'value' => (string) $resolvedReports,
                'meta' => 'Issues resolved successfully',
                'tone' => 'success',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 2"/></svg>',
            ],
            [
                'label' => 'FIELD RESPONDERS',
                'value' => (string) $fieldResponders,
                'meta' => 'Units currently registered',
                'tone' => 'teal',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 15h2l1.5-4h8L16 15h2"/><path d="M7 15v2m10-2v2"/><circle cx="8" cy="7" r="2.5"/><circle cx="16" cy="7" r="2.5"/><path d="M10 7h4"/></svg>',
            ],
        ];

        $incidentReports = IncidentReport::query()
            ->with('reporter')
            ->latest('reported_at')
            ->limit(4)
            ->get()
            ->map(function (IncidentReport $incident) {
                return [
                    'id' => '#'.$incident->report_number,
                    'title' => $incident->incident_type,
                    'location' => $incident->barangay ?? $incident->location,
                    'time' => $incident->reported_at?->diffForHumans(),
                    'severity' => ucfirst((string) $incident->severity),
                    'status' => ucfirst(str_replace('_', ' ', (string) $incident->status)),
                    'action' => $incident->status === 'resolved' ? 'View Log' : 'Assign Unit',
                ];
            })
            ->all();

        return view('admin.dashboard', [
            'weather' => $weather,
            'stats' => $stats,
            'incidentReports' => $incidentReports,
            'userName' => Auth::user()?->name ?? 'Admin',
        ]);
    }

    public function map(): View
    {
        return view('admin.map');
    }

    public function weather(Request $request): JsonResponse
    {
        $location = $this->weatherService->getArakanLocation();

        try {
            $latitude = $request->input('latitude', $location['latitude']);
            $longitude = $request->input('longitude', $location['longitude']);

            $validated = $request->validate([
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            ]);

            $latitude = $validated['latitude'] ?? $location['latitude'];
            $longitude = $validated['longitude'] ?? $location['longitude'];

            $weather = $this->weatherService->getCurrentWeather((float) $latitude, (float) $longitude);

            if (! isset($validated['latitude']) && ! isset($validated['longitude'])) {
                $weather['location'] = $location['name'];
                $weather['country'] = $location['country'];
            }

            return response()->json([
                'success' => true,
                'weather' => $weather,
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid latitude or longitude provided.',
            ], 422);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            report($exception);
            $message = match (true) {
                $exception instanceof ConnectionException
                    && str_contains(strtolower($exception->getMessage()), 'timed out') => 'Weather service timed out. Please try again.',
                $exception instanceof ConnectionException => 'Could not connect to Open-Meteo. Check the server network connection.',
                $exception instanceof \RuntimeException => $exception->getMessage(),
                default => 'Could not retrieve weather from Open-Meteo. Check the application logs.',
            };

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 503);
        }
    }
}
