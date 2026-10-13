@extends('layouts.dashboard')

@section('title', $incidentReport->report_number.' · AkoHanda')

@section('content')
    <x-command-shell title="Report Details">
        <div class="mx-auto max-w-4xl space-y-6">
            <div>
                <a href="{{ route('reporter.reports.index') }}" class="text-sm font-semibold text-emerald-900 hover:underline">← Back to my reports</a>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-emerald-900">{{ $incidentReport->report_number }}</p>
                        <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">{{ $incidentReport->category?->name ?? $incidentReport->incident_type }}</h1>
                    </div>
                    <x-dashboard.status-badge :status="$incidentReport->status" />
                </div>
                <p class="mt-2 text-sm text-slate-600">Submitted {{ $incidentReport->reported_at?->format('M j, Y g:i A') }}</p>
            </div>

            <section class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
                <div><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Incident date</h2><p class="mt-2 text-sm text-slate-900">{{ $incidentReport->occurred_at?->format('M j, Y g:i A') ?? 'Not provided' }}</p></div>
                <div><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Severity</h2><p class="mt-2 text-sm font-semibold capitalize text-slate-900">{{ $incidentReport->severity }}</p></div>
                <div class="sm:col-span-2"><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Location</h2><p class="mt-2 text-sm text-slate-900">{{ $incidentReport->location }}{{ $incidentReport->barangay ? ', '.$incidentReport->barangay : '' }}</p>
                    @if ($incidentReport->latitude !== null && $incidentReport->longitude !== null)
                        <p class="mt-1 font-mono text-xs text-slate-600">{{ $incidentReport->latitude }}, {{ $incidentReport->longitude }}</p>
                    @endif
                </div>
                <div class="sm:col-span-2"><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Description</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $incidentReport->description }}</p></div>
                @if ($incidentReport->persons_involved)
                    <div class="sm:col-span-2"><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">People involved</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-800">{{ $incidentReport->persons_involved }}</p></div>
                @endif
                @if ($incidentReport->evidence_path)
                    <div class="sm:col-span-2"><a href="{{ route('reports.evidence', $incidentReport) }}" class="font-bold text-emerald-900 underline-offset-4 hover:underline">Download attached evidence</a></div>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-lg font-extrabold text-slate-950">Report progress</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">The status shown above is the latest status recorded by authorized staff. Contact the relevant authority directly if you need immediate assistance.</p>
                @if ($incidentReport->resolved_at)
                    <p class="mt-3 text-sm font-semibold text-emerald-900">Resolved {{ $incidentReport->resolved_at->format('M j, Y g:i A') }}</p>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
