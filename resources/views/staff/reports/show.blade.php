@extends('layouts.dashboard')

@section('title', $incidentReport->report_number.' · AkoHanda')

@section('content')
    <x-command-shell title="Review Assigned Report">
        <div class="mx-auto max-w-4xl space-y-6">
            <div>
                <a href="{{ route('staff.reports.index') }}" class="text-sm font-semibold text-emerald-900 hover:underline">← Back to assigned reports</a>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div><p class="text-sm font-bold text-emerald-900">{{ $incidentReport->report_number }}</p><h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">{{ $incidentReport->category?->name ?? $incidentReport->incident_type }}</h1></div>
                    <x-dashboard.status-badge :status="$incidentReport->status" />
                </div>
                <p class="mt-2 text-sm text-slate-600">Submitted by {{ $incidentReport->reporter?->name ?? 'Unavailable' }} · {{ $incidentReport->reported_at?->format('M j, Y g:i A') }}</p>
            </div>

            <section class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
                <div><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Incident date</h2><p class="mt-2 text-sm text-slate-900">{{ $incidentReport->occurred_at?->format('M j, Y g:i A') ?? 'Not provided' }}</p></div>
                <div><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Severity</h2><p class="mt-2 text-sm font-semibold capitalize text-slate-900">{{ $incidentReport->severity }}</p></div>
                <div class="sm:col-span-2"><h2 class="text-xs font-bold uppercase tracking-wide text-slate-500">Location</h2><p class="mt-2 text-sm text-slate-900">{{ $incidentReport->location }}{{ $incidentReport->barangay ? ', '.$incidentReport->barangay : '' }}</p>
                    @if ($incidentReport->latitude !== null && $incidentReport->longitude !== null)
                        <p class="mt-1 font-mono text-xs text-slate-600">{{ $incidentReport->latitude }}, {{ $incidentReport->longitude }}</p>
                        <a class="mt-2 inline-block text-sm font-bold text-emerald-900 underline-offset-4 hover:underline" href="https://www.openstreetmap.org/?mlat={{ urlencode((string) $incidentReport->latitude) }}&mlon={{ urlencode((string) $incidentReport->longitude) }}#map=16/{{ urlencode((string) $incidentReport->latitude) }}/{{ urlencode((string) $incidentReport->longitude) }}" target="_blank" rel="noopener noreferrer">Open coordinates in OpenStreetMap</a>
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
                <h2 class="text-lg font-extrabold text-slate-950">Update report status</h2>
                @if ($allowedStatuses === [])
                    <p class="mt-2 text-sm text-slate-600">This report has no further status transitions available to staff.</p>
                @else
                    <form method="POST" action="{{ route('staff.reports.update', $incidentReport) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="block text-sm font-bold text-slate-900">Next status</label>
                            <select id="status" name="status" required class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800">
                                @foreach ($allowedStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-bold text-slate-900">Progress note <span class="font-normal text-slate-500">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" maxlength="2000" class="mt-2 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Save update</button>
                    </form>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
