@extends('layouts.dashboard')

@section('title', 'Incident Locations · AkoHanda')

@section('content')
    <x-command-shell title="Incident Locations">
        <div class="space-y-6">
            <section class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-800">Location overview</p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Incident locations</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Review coordinates attached to submitted reports. A location appears here only when it was recorded with the report.
                    </p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus-visible:outline-offset-4">
                    <span>Review reports</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                    </svg>
                </a>
            </section>

            @if (! $locationTrackingAvailable)
                <section role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-950">
                    <h2 class="font-bold">Location tracking is not enabled yet</h2>
                    <p class="mt-1 text-sm leading-6">
                        The current database is missing the GPS fields needed for this page. Apply the pending incident-report migrations to enable location tracking.
                    </p>
                </section>
            @else
                <section aria-label="Location report summary" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Reports with coordinates</p>
                            <p class="mt-1 text-sm text-slate-600">Reports that include both latitude and longitude.</p>
                        </div>
                        <p class="text-3xl font-extrabold tabular-nums text-slate-950">{{ $locationReports->total() }}</p>
                    </div>
                </section>

                <section aria-labelledby="location-reports-heading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                        <h2 id="location-reports-heading" class="text-lg font-extrabold text-slate-950">Reported locations</h2>
                        <p class="mt-1 text-sm text-slate-600">Newest reports with saved GPS coordinates appear first.</p>
                    </div>

                    @if ($locationReports->isEmpty())
                        <div class="px-5 py-12 text-center sm:px-6">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="2.5"/>
                                </svg>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-slate-900">No report locations yet</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">
                                Reports will appear here when a reporter chooses to include GPS coordinates. You can still review all reports.
                            </p>
                            <a href="{{ route('admin.reports.index') }}" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-800 transition hover:bg-slate-50 focus-visible:outline-offset-4">
                                View all reports
                            </a>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-100">
                            @foreach ($locationReports as $report)
                                @php
                                    $latitude = (string) $report->latitude;
                                    $longitude = (string) $report->longitude;
                                    $mapUrl = 'https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'#map=16/'.$latitude.'/'.$longitude;
                                @endphp
                                <li class="flex flex-col gap-4 px-5 py-5 sm:px-6 md:flex-row md:items-center md:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-bold text-slate-950">{{ $report->report_number }}</h3>
                                            <x-dashboard.status-badge :status="$report->status" />
                                        </div>
                                        <p class="mt-1 text-sm font-medium text-slate-800">{{ $report->incident_type }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $report->barangay ?: $report->location }}</p>
                                        <p class="mt-2 font-mono text-xs text-slate-500">
                                            <span class="sr-only">Coordinates: </span>{{ $latitude }}, {{ $longitude }}
                                        </p>
                                    </div>
                                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 self-start rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold text-emerald-950 transition hover:border-emerald-800 hover:bg-emerald-50 focus-visible:outline-offset-4 md:self-center">
                                        View on OpenStreetMap
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5m0-5-8 8"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>
                                        </svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @if ($locationReports->hasPages())
                            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">
                                {{ $locationReports->links() }}
                            </div>
                        @endif
                    @endif
                </section>
            @endif
        </div>
    </x-command-shell>
@endsection
