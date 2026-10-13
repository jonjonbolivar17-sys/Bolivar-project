@extends('layouts.dashboard')

@section('title', 'Admin Dashboard · AkoHanda')

@section('content')
    <x-command-shell title="Admin Dashboard">
        <div class="space-y-8">
            <header class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-800">System overview</p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Welcome, {{ $userName }}!</h1>
                    <p class="mt-2 text-sm text-slate-600">Review incident activity and manage the reporting system.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 transition hover:bg-slate-50 focus-visible:outline-offset-4">Review reports</a>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus-visible:outline-offset-4">Manage users</a>
                </div>
            </header>

            <section aria-label="System summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <article class="rounded-2xl border border-slate-200 border-t-2 border-t-emerald-700 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-600">{{ str_replace('_', ' ', $stat['label']) }}</p>
                        <p class="mt-3 text-3xl font-extrabold tabular-nums text-slate-950">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $stat['meta'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950">Recent accident reports</h2>
                        <p class="mt-1 text-sm text-slate-600">Latest reports submitted to the system.</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="rounded text-sm font-bold text-emerald-900 underline-offset-4 hover:underline focus-visible:outline-offset-4">View all reports</a>
                </div>
                @if ($incidentReports->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-slate-600">No incident reports have been submitted yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr class="transition-colors hover:bg-slate-50">
                                    <th class="px-5 py-3 font-bold">Report</th>
                                    <th class="px-5 py-3 font-bold">Category</th>
                                    <th class="px-5 py-3 font-bold">Location</th>
                                    <th class="px-5 py-3 font-bold">Status</th>
                                    <th class="px-5 py-3 font-bold"><span class="sr-only">Action</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($incidentReports as $report)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <p class="font-bold text-slate-900">{{ $report->report_number }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $report->reported_at?->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-slate-700">{{ $report->category?->name ?? $report->incident_type }}</td>
                                        <td class="px-5 py-4 text-slate-700">{{ $report->barangay ?: $report->location }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <x-dashboard.status-badge :status="$report->status" />
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a href="{{ route('admin.reports.show', $report) }}" class="font-bold text-emerald-900 hover:underline">Open</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section id="weather-widget" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950">Current Weather</h2>
                        <p class="mt-1 text-sm text-slate-600">Arakan, Cotabato · Philippines</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ ($weather['success'] ?? false) ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900' }}">
                        {{ ($weather['success'] ?? false) ? 'AVAILABLE' : 'UNAVAILABLE' }}
                    </span>
                </div>
                @if ($weather['success'] ?? false)
                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div><p class="text-xs font-semibold uppercase text-slate-500">Location</p><p class="mt-1 font-bold text-slate-900">{{ $weather['location'] }}</p></div>
                        <div><p class="text-xs font-semibold uppercase text-slate-500">Temperature</p><p class="mt-1 font-bold text-slate-900">{{ number_format((float) $weather['temperature'], 1) }} °C</p></div>
                        <div><p class="text-xs font-semibold uppercase text-slate-500">Condition</p><p class="mt-1 font-bold text-slate-900">{{ $weather['condition'] }}</p></div>
                        <div><p class="text-xs font-semibold uppercase text-slate-500">Humidity · Wind</p><p class="mt-1 font-bold text-slate-900">{{ $weather['humidity'] }}% · {{ number_format((float) $weather['wind_speed'], 1) }} m/s</p></div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Weather data provided by {{ $weather['provider'] ?? 'Open-Meteo' }}.</p>
                @else
                    <p class="mt-4 text-sm text-slate-700">{{ $weather['message'] ?? 'Weather data is temporarily unavailable.' }}</p>
                    @if (! empty($weather['provider_message']))
                        <p class="mt-2 text-xs text-slate-500">Provider message: {{ $weather['provider_message'] }}</p>
                    @endif
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
