@extends('layouts.dashboard')

@section('title', 'Activity Log · AkoHanda')

@section('content')
    <x-command-shell title="Activity Log">
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">Activity log</h1>
                <p class="mt-2 text-sm text-slate-600">Review recorded report, account, and category actions.</p>
            </div>

            <form method="GET" action="{{ route('admin.activity.index') }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-2 xl:grid-cols-[minmax(14rem,1fr)_14rem_12rem_12rem_auto_auto] xl:items-end">
                <div>
                    <label for="search" class="block text-sm font-bold text-slate-900">Search log</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Action, person, IP, or details" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                </div>
                <div>
                    <label for="action" class="block text-sm font-bold text-slate-900">Action</label>
                    <select id="action" name="action" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                        <option value="">All actions</option>
                        @foreach ([
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
                        ] as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="from" class="block text-sm font-bold text-slate-900">From</label>
                    <input id="from" name="from" type="date" value="{{ request('from') }}" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                </div>
                <div>
                    <label for="to" class="block text-sm font-bold text-slate-900">To</label>
                    <input id="to" name="to" type="date" value="{{ request('to') }}" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Apply</button>
                <a href="{{ route('admin.activity.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Reset</a>
            </form>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                @if ($logs->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <h2 class="font-bold text-slate-900">No activity recorded</h2>
                        <p class="mt-2 text-sm text-slate-600">Actions recorded by the system will appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Time</th>
                                    <th scope="col" class="px-5 py-3">User</th>
                                    <th scope="col" class="px-5 py-3">Action</th>
                                    <th scope="col" class="px-5 py-3">Details</th>
                                    <th scope="col" class="px-5 py-3">IP address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $log->user?->name ?? 'Account unavailable' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ ucwords(str_replace('_', ' ', $log->action)) }}</td>
                                        <td class="min-w-64 px-5 py-4 text-slate-700">{{ $log->description }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-5 py-4">{{ $logs->links() }}</div>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
