<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $assignedReports = IncidentReport::query()
            ->whereHas('dispatches.responder', fn ($query) => $query->where('user_id', $request->user()->id));

        $recentReports = (clone $assignedReports)
            ->with(['category', 'reporter'])
            ->latest('reported_at')
            ->limit(6)
            ->get();

        $stats = [
            'assigned' => (clone $assignedReports)->count(),
            'pending' => (clone $assignedReports)->whereIn('status', ['pending', 'under_review'])->count(),
            'in_progress' => (clone $assignedReports)->where('status', 'in_progress')->count(),
            'resolved' => (clone $assignedReports)->where('status', 'resolved')->count(),
        ];

        return view('staff.dashboard', compact('recentReports', 'stats'));
    }
}
