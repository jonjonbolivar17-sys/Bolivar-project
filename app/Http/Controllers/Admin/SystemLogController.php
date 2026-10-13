<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SystemLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'action' => [
                'nullable',
                Rule::in([
                    'incident_report_created',
                    'incident_report_updated',
                    'incident_report_status_updated',
                    'incident_report_dispatched',
                    'incident_report_deleted',
                    'incident_reports_exported',
                    'user_created',
                    'user_updated',
                    'user_deleted',
                    'incident_category_created',
                    'incident_category_updated',
                    'incident_category_deleted',
                ]),
            ],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = SystemLog::query()
            ->with('user:id,name,email')
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($validated['action'] ?? null, fn ($query, string $action) => $query->where('action', $action))
            ->when($validated['from'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($validated['to'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', compact('logs'));
    }
}
