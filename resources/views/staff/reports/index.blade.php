@extends('layouts.dashboard')

@section('title', 'Assigned Reports · AkoHanda')

@section('content')
    <x-command-shell title="Assigned Reports">
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">Assigned reports</h1>
                <p class="mt-2 text-sm text-slate-600">Review and update reports assigned to your responder profile.</p>
            </div>
            <form method="GET" action="{{ route('staff.reports.index') }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_14rem_auto_auto] sm:items-end">
                <div><label for="search" class="block text-sm font-bold text-slate-900">Search</label><input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="ID, category, or location" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800"></div>
                <div>
                    <label for="status" class="block text-sm font-bold text-slate-900">Status</label>
                    <select id="status" name="status" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Apply filters</button>
                <a href="{{ route('staff.reports.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Reset</a>
            </form>
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                @if ($reports->isEmpty())
                    <div class="px-5 py-12 text-center"><h2 class="font-bold text-slate-900">No assigned reports</h2><p class="mt-2 text-sm text-slate-600">Reports assigned to your responder profile will appear here.</p></div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-5 py-3">Report</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Location</th><th class="px-5 py-3">Incident date</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"><span class="sr-only">Action</span></th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($reports as $report)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-900">{{ $report->report_number }}</td>
                                        <td class="px-5 py-4 text-slate-700">{{ $report->category?->name ?? $report->incident_type }}</td>
                                        <td class="px-5 py-4 text-slate-700">{{ $report->barangay ?: $report->location }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $report->occurred_at?->format('M j, Y g:i A') ?? 'Not provided' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4"><x-dashboard.status-badge :status="$report->status" /></td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right"><a href="{{ route('staff.reports.show', $report) }}" class="font-bold text-emerald-900 hover:underline">Review</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-5 py-4">{{ $reports->links() }}</div>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
