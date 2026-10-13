@extends('layouts.dashboard')

@section('title', 'My Dashboard · AkoHanda')

@section('content')
    <x-command-shell title="Reporter Dashboard">
        <div class="space-y-8">
            <header class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-800">Reporter workspace</p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Welcome, {{ auth()->user()->name }}</h1>
                    <p class="mt-2 text-sm text-slate-600">Submit an incident report and return here to follow its status.</p>
                </div>
                <a href="{{ route('reporter.reports.create') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-lg bg-red-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-800 focus-visible:outline-offset-4">
                    <span aria-hidden="true">+</span>
                    Submit an accident report
                </a>
            </header>

            <section aria-label="My report summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Total reports', 'value' => $stats['total']],
                    ['label' => 'Pending review', 'value' => $stats['pending']],
                    ['label' => 'In progress', 'value' => $stats['in_progress']],
                    ['label' => 'Resolved', 'value' => $stats['resolved']],
                ] as $stat)
                    <article class="rounded-2xl border border-slate-200 border-t-2 border-t-emerald-700 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-600">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-extrabold tabular-nums text-slate-950">{{ $stat['value'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950">Recent submissions</h2>
                        <p class="mt-1 text-sm text-slate-600">Only your reports are shown here.</p>
                    </div>
                    <a href="{{ route('reporter.reports.index') }}" class="rounded text-sm font-bold text-emerald-900 underline-offset-4 hover:underline focus-visible:outline-offset-4">View all</a>
                </div>
                @if ($recentReports->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <p class="font-bold text-slate-900">No reports submitted yet</p>
                        <p class="mt-2 text-sm text-slate-600">Your submitted reports and status updates will appear here.</p>
                        <a href="{{ route('reporter.reports.create') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-lg bg-red-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-800 focus-visible:outline-offset-4">Submit your first report</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr><th class="px-5 py-3">Report</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Submitted</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"><span class="sr-only">Action</span></th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentReports as $report)
                                    <tr class="transition-colors hover:bg-slate-50">
                                        <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-900">{{ $report->report_number }}</td>
                                        <td class="px-5 py-4 text-slate-700">{{ $report->category?->name ?? $report->incident_type }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $report->reported_at?->format('M j, Y g:i A') }}</td>
                                        <td class="whitespace-nowrap px-5 py-4"><x-dashboard.status-badge :status="$report->status" /></td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right"><a href="{{ route('reporter.reports.show', $report) }}" class="font-bold text-emerald-900 hover:underline">Details</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
