@props([
    'activeRole' => null,
    'activeRoute' => '',
    'title' => 'AkoHanda',
])

@php
    $user = auth()->user();
    $role = $user?->role ?? $activeRole ?? 'reporter';
    $navigation = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Accident Reports', 'route' => 'admin.reports.index', 'active' => request()->routeIs('admin.reports.*')],
            ['label' => 'Incident Locations', 'route' => 'admin.map', 'active' => request()->routeIs('admin.map')],
            ['label' => 'Incident Categories', 'route' => 'admin.categories.index', 'active' => request()->routeIs('admin.categories.*')],
            ['label' => 'Activity Log', 'route' => 'admin.activity.index', 'active' => request()->routeIs('admin.activity.*')],
            ['label' => 'User Management', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.*')],
        ],
        'staff' => [
            ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'active' => request()->routeIs('staff.dashboard')],
            ['label' => 'Assigned Reports', 'route' => 'staff.reports.index', 'active' => request()->routeIs('staff.reports.*')],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'reporter.dashboard', 'active' => request()->routeIs('reporter.dashboard')],
            ['label' => 'Submit Accident Report', 'route' => 'reporter.reports.create', 'active' => request()->routeIs('reporter.reports.create')],
            ['label' => 'My Reports', 'route' => 'reporter.reports.index', 'active' => request()->routeIs('reporter.reports.index', 'reporter.reports.show')],
        ],
    };
@endphp

<div
    x-data="{ mobileSidebarOpen: false }"
    @keydown.escape.window="mobileSidebarOpen = false"
    class="min-h-screen bg-slate-50 text-slate-900"
>
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
        aria-hidden="true"
    ></div>

    <aside
        id="dashboard-navigation"
        :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0 lg:shadow-none"
        aria-label="Application sidebar"
    >
        <div class="flex min-h-20 shrink-0 items-center gap-3 border-b border-slate-200 px-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-900 text-sm font-black text-white" aria-hidden="true">AH</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-extrabold text-slate-900">AkoHanda</p>
                <p class="truncate text-xs text-slate-500">Accident Reporting System</p>
            </div>
            <button
                type="button"
                @click="mobileSidebarOpen = false"
                aria-label="Close navigation menu"
                class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 focus-visible:outline-offset-2 lg:hidden"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Dashboard navigation">
            @foreach ($navigation as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @click="mobileSidebarOpen = false"
                    @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus-visible:outline-offset-2',
                        'bg-emerald-50 text-emerald-950 ring-1 ring-inset ring-emerald-200' => $item['active'],
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! $item['active'],
                    ])
                    @if ($item['active']) aria-current="page" @endif
                >
                    <span class="h-2 w-2 rounded-full {{ $item['active'] ? 'bg-emerald-700' : 'bg-slate-300' }}" aria-hidden="true"></span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
            <a
                href="{{ route('profile.edit') }}"
                @click="mobileSidebarOpen = false"
                @class([
                    'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus-visible:outline-offset-2',
                    'bg-emerald-50 text-emerald-950 ring-1 ring-inset ring-emerald-200' => request()->routeIs('profile.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('profile.*'),
                ])
                @if (request()->routeIs('profile.*')) aria-current="page" @endif
            >
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('profile.*') ? 'bg-emerald-700' : 'bg-slate-300' }}" aria-hidden="true"></span>
                <span>Profile &amp; Settings</span>
            </a>
        </nav>

        <div class="shrink-0 border-t border-slate-200 p-4">
            <p class="truncate text-sm font-bold text-slate-900">{{ $user?->name }}</p>
            <p class="mt-0.5 text-xs capitalize text-slate-500">{{ $role }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-800 focus-visible:outline-offset-2">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="flex min-h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
                <button
                    type="button"
                    @click="mobileSidebarOpen = true"
                    aria-label="Open navigation menu"
                    aria-controls="dashboard-navigation"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:bg-slate-100 focus-visible:outline-offset-2 lg:hidden"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-900">{{ $title }}</p>
                    <p class="hidden text-xs text-slate-500 sm:block">AkoHanda · Accident Reporting System</p>
                </div>
                <span class="hidden rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold capitalize text-emerald-900 sm:inline-flex">{{ $role }} account</span>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-950">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-900">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
