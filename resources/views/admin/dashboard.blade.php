@extends('layouts.dashboard')

@section('content')
    @php
        $displayName = trim((string) ($userName ?? auth()->user()->name ?? 'Admin'));
        $initials = collect(explode(' ', $displayName))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        $displayInitials = $initials !== '' ? $initials : 'AD';

        $sidebarIcon = [
            'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V19h14v-8.5"/><path d="M10 19v-6h4v6"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1"/><circle cx="10" cy="7" r="3.5"/><path d="M19 19v-1a4 4 0 0 0-3-3.87"/><path d="M16 4.13a4 4 0 0 1 0 7.74"/></svg>',
            'reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3.5h6l5 5V18a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5.5a2 2 0 0 1 2-2Z"/><path d="M14 3.5v5h5"/><path d="M8 11h8M8 15h8"/></svg>',
            'weather' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 18A4.5 4.5 0 1 1 10 8a5.5 5.5 0 1 1 9 4.5"/><path d="M12 2v2M4.93 4.93l1.41 1.41M2 12h2M19.07 4.93l-1.41 1.41M22 12h-2M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41"/></svg>',
            'dispatch' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h3l2-5h7l2 5h3"/><path d="M7 15a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/><path d="M7 12V8m10 4V8"/></svg>',
            'gis' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18 9 5l6 8 6-5v13l-6 5-6-8-6 5Z"/><path d="M9 5v13M15 13v6"/></svg>',
            'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 3.5h5l.7 2.8a7 7 0 0 1 2.8 1.6l2.7-.8 2.1 2.1-1 2.6a7 7 0 0 1 0 3.2l1 2.7-2.1 2.1-2.7-.8a7 7 0 0 1-2.8 1.6l-.7 2.8h-5l-.7-2.8a7 7 0 0 1-2.8-1.6l-2.7.8-2.1-2.1 1-2.6a7 7 0 0 1 0-3.2l-1-2.7L4 8.4l2.7.8A7 7 0 0 1 9.5 7.6L9.5 3.5Z"/><circle cx="12" cy="12" r="3.5"/></svg>',
        ];

        $statusTone = [
            'Pending Review' => 'warning',
            'In Progress' => 'cyan',
            'Resolved' => 'green',
            'Moderate' => 'warning',
            'Routine' => 'teal',
            'Low' => 'green',
        ];
    @endphp

    <div x-data="{ sidebarState: 'expanded', mobileSidebarOpen: false, userMenuOpen: false, notificationOpen: false, statusFilterOpen: false, refreshing: false }" @keydown.window.ctrl.b.prevent="sidebarState = sidebarState === 'expanded' ? 'rail' : sidebarState === 'rail' ? 'hidden' : 'expanded'" @keydown.window.meta.b.prevent="sidebarState = sidebarState === 'expanded' ? 'rail' : sidebarState === 'rail' ? 'hidden' : 'expanded'" @keydown.window.escape="mobileSidebarOpen = false" class="min-h-screen bg-[#001F18] text-[#F5FFFC]">
        <div class="flex min-h-screen">
            <aside :class="mobileSidebarOpen ? 'w-72 translate-x-0 p-5' : (sidebarState === 'expanded' ? 'w-72 p-5' : (sidebarState === 'rail' ? 'w-18 p-3' : 'w-0 border-r-0 p-0')) + ' -translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-40 transform overflow-hidden border-r border-[#00B894]/15 bg-[#001F18] transition-[width,transform,padding] duration-300 ease-in-out lg:sticky">
                <div class="flex h-full flex-col">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#00B894]/20 bg-[#00382D]">
                            <span class="text-lg font-black tracking-tight text-[#19D3AE]">AH</span>
                        </div>

                        <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h1 class="text-[1.8rem] font-black leading-none tracking-tight text-[#F5FFFC]">AkoHanda</h1>
                                <span class="inline-flex items-center rounded-full border border-[#F5B700]/25 bg-[#F5B700]/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-[0.24em] text-[#F5B700]">OPS</span>
                            </div>
                            <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.22em] text-[#7DB5A5]">MDRRMO Arakan</p>
                        </div>
                    </div>

                    <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="mt-7 rounded-xl border border-[#00B894]/20 bg-[#002D26] px-3 py-3">
                        <div class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#F5B700]">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#F5B700]"></span>
                            DEFCON IV - Guard | SEC-1
                        </div>
                    </div>

                    <nav class="mt-8 space-y-2">
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">CORE DISPATCH</p>

                        <x-dashboard.sidebar-link label="Dashboard" href="{{ route('admin.dashboard') }}" :active="true" :icon="$sidebarIcon['dashboard']" />
                        <x-dashboard.sidebar-link label="User Management" href="{{ route('admin.users.index') }}" :icon="$sidebarIcon['users']" />
                        <x-dashboard.sidebar-link label="Incident Reports" href="#" :icon="$sidebarIcon['reports']" badge="14" />
                        <x-dashboard.sidebar-link label="Weather &amp; Sensors" href="#" :icon="$sidebarIcon['weather']" />

                        <div class="mt-6"></div>
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">RESPONSE &amp; OPERATIONS</p>

                        <x-dashboard.sidebar-link label="Emergency Dispatch" href="#" :icon="$sidebarIcon['dispatch']" />
                        <x-dashboard.sidebar-link label="Analytics &amp; GIS Maps" href="{{ route('admin.map') }}" :icon="$sidebarIcon['gis']" />
                        <x-dashboard.sidebar-link label="Settings" href="#" :icon="$sidebarIcon['settings']" />
                    </nav>

                    <div class="mt-auto space-y-4 pb-2">
                        <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="rounded-2xl border border-[#00B894]/15 bg-[#002A23] p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">MDRRMO Hotline</p>
                            <a href="tel:+63645218899" class="mt-3 block text-[1.8rem] font-bold tracking-tight text-[#F5FFFC]">064-521-8899</a>
                            <p class="mt-2 text-xs text-[#7DB5A5]">24/7 Rapid Incident Desk</p>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="flex items-center gap-2 text-xs text-[#7DB5A5]">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                v2.4-PROD · Live Telemetry
                            </div>
                            <button type="button" @click="sidebarState = sidebarState === 'expanded' ? 'rail' : sidebarState === 'rail' ? 'hidden' : 'expanded'" title="Change sidebar width" aria-label="Change sidebar width" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-[#00B894]/20 text-[#9EF2D4] transition hover:bg-[#00382D]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M14 9l-3 3 3 3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1">
                <header class="sticky top-0 z-30 border-b border-[#00B894]/10 bg-[#001F18]/90 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4 px-4 py-4 md:px-6 xl:px-8">
                        <button type="button" @click="if (window.matchMedia('(max-width: 1023px)').matches) { mobileSidebarOpen = !mobileSidebarOpen } else { sidebarState = sidebarState === 'expanded' ? 'rail' : sidebarState === 'rail' ? 'hidden' : 'expanded' }" title="Toggle navigation · Ctrl/Cmd+B" aria-label="Toggle navigation" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#00B894]/20 bg-[#00382D] text-[#F5FFFC]">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M14 9l-3 3 3 3"/>
                            </svg>
                        </button>

                        <div class="relative hidden flex-1 max-w-xl md:block">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#7DB5A5]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <circle cx="11" cy="11" r="6"/>
                                    <path d="m16 16 5 5"/>
                                </svg>
                            </span>
                            <input type="text" placeholder="Search incidents, barangays, or personnel..." class="w-full rounded-full border border-[#00B894]/15 bg-[#002A23] py-3 pl-11 pr-4 text-sm text-[#F5FFFC] placeholder:text-[#7DB5A5] focus:border-[#00B894]/50 focus:outline-none">
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center rounded-xl border border-[#00B894]/20 bg-[#002A23] p-1 shadow-inner">
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-[#19D3AE] text-[#001F18] shadow-sm font-bold"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#001F18]"></span>
                                    Admin
                                </a>
                                <a
                                    href="{{ route('staff.dashboard') }}"
                                    class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                    Staff / Responder
                                </a>
                                <a
                                    href="{{ route('user.dashboard') }}"
                                    class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                                    Citizen
                                </a>
                            </div>
                        </div>

                        <div class="ml-auto flex items-center gap-3">
                            <div class="hidden items-center gap-2 rounded-full border border-[#19D3AE]/20 bg-[#00382D]/80 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#9EF2D4] sm:flex">
                                <span class="h-2 w-2 rounded-full bg-[#19D3AE]"></span>
                                Live Monitoring Active
                            </div>

                            <div class="relative">
                                <button type="button" @click="notificationOpen = !notificationOpen" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#00B894]/20 bg-[#00382D] text-[#F5FFFC] transition hover:border-[#00B894]/40">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                                        <path d="M10 20a2 2 0 0 0 4 0"/>
                                    </svg>
                                </button>

                                <div x-show="notificationOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" @click.outside="notificationOpen = false" class="absolute right-0 mt-3 w-72 rounded-2xl border border-[#00B894]/15 bg-[#002A23]/95 p-3 shadow-2xl" style="display: none;">
                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-[#F5FFFC]">Notifications</p>
                                        <button type="button" class="text-[10px] uppercase tracking-[0.2em] text-[#7DB5A5]">Clear</button>
                                    </div>
                                    <div class="space-y-2 text-sm text-[#D9FFF5]">
                                        <div class="rounded-xl border border-[#00B894]/10 bg-[#00382D]/70 px-3 py-2">
                                            3 critical units dispatched to Brgy. Poblacion.
                                        </div>
                                        <div class="rounded-xl border border-[#00B894]/10 bg-[#00382D]/70 px-3 py-2">
                                            Weather alert automated summary refreshed 3 minutes ago.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <button type="button" @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 rounded-full border border-[#00B894]/15 bg-[#002A23] px-2 py-1.5 text-left shadow-sm">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#00382D] text-xs font-bold text-[#19D3AE]">{{ $displayInitials }}</span>
                                    <span class="hidden sm:block">
                                        <span class="block text-sm font-semibold text-[#F5FFFC]">{{ $displayName }}</span>
                                        <span class="block text-[10px] uppercase tracking-[0.18em] text-[#7DB5A5]">MDRRMO Officer</span>
                                    </span>
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-[#7DB5A5]">
                                        <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" @click.outside="userMenuOpen = false" class="absolute right-0 mt-3 w-52 rounded-2xl border border-[#00B894]/15 bg-[#002A23]/95 p-2 shadow-2xl" style="display: none;">
                                    <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm text-[#D9FFF5] hover:bg-[#00382D]">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-[#D9FFF5] hover:bg-[#00382D]">Log Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-5 md:px-6 xl:px-8">
                    <div class="flex flex-col gap-4 border-b border-[#00B894]/10 pb-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-[#19D3AE]/20 bg-[#00382D]/80 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#9EF2D4]">
                                <span class="h-2 w-2 rounded-full bg-[#19D3AE]"></span>
                                ARAKAN COMMAND CENTER
                            </div>
                            <h2 class="mt-3 text-3xl font-bold tracking-tight text-[#F5FFFC] md:text-4xl">Welcome, {{ $displayName }}!</h2>
                            <p class="mt-2 text-sm text-[#7DB5A5]">Here's an overview of your municipal disaster response system and field incidents.</p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#00B894]/20 bg-[#001F18] px-4 py-2.5 text-sm font-semibold text-[#D9FFF5] transition hover:border-[#00B894]/40 hover:bg-[#00382D]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 19h14"/>
                                </svg>
                                Export Summary
                            </button>

                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#19D3AE]/30 bg-[#19D3AE] px-4 py-2.5 text-sm font-semibold text-[#001F18] shadow-[0_0_0_1px_rgba(25,211,174,0.15)] transition hover:bg-[#00B894]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                                New Incident Report
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($stats as $stat)
                            <x-dashboard.stat-card
                                label="{{ $stat['label'] }}"
                                value="{{ $stat['value'] }}"
                                meta="{{ $stat['meta'] }}"
                                tone="{{ $stat['tone'] }}"
                                :icon="$stat['icon']"
                            />
                        @endforeach
                    </div>

                    <section id="weather-widget" class="mt-8 rounded-[28px] border border-[#00B894]/15 bg-[#002A23]/80 p-4 md:p-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold text-[#F5FFFC]">Current Weather</h3>
                                <span class="rounded-full border border-[#19D3AE]/20 bg-[#00382D]/70 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#9EF2D4]">
                                    {{ ($weather['success'] ?? false) ? 'AVAILABLE' : 'UNAVAILABLE' }}
                                </span>
                            </div>

                            <button id="refresh-weather" type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-[#00B894]/20 bg-[#001F18] text-[#F5FFFC] transition hover:border-[#00B894]/35" aria-label="Refresh weather">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M20 12a8 8 0 0 1-13.66 5.66M4 12a8 8 0 0 1 13.66-5.66"/>
                                    <path d="M4 4v6h6M20 20v-6h-6"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-5 rounded-2xl border border-[#00B894]/15 bg-[#00382D]/60 p-4 md:p-5">
                            @if(($weather['success'] ?? false))
                                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                                    <x-dashboard.metric-card label="LOCATION" value="{{ $weather['location'] }}" description="Current location" />
                                    <x-dashboard.metric-card label="TEMPERATURE" value="{{ number_format((float) $weather['temperature'], 1) }} °C" description="Metric units" />
                                    <x-dashboard.metric-card label="CONDITION" value="{{ $weather['condition'] }}" description="Current conditions" />
                                    <x-dashboard.metric-card label="HUMIDITY" value="{{ $weather['humidity'] }}%" description="Relative humidity" />
                                    <x-dashboard.metric-card label="WIND SPEED" value="{{ $weather['wind_speed'] !== null ? number_format((float) $weather['wind_speed'], 1).' m/s' : '--' }}" description="Metric units" />
                                </div>

                                <p class="mt-4 text-xs text-[#7DB5A5]">
                                    Weather data by
                                    <a href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer" class="underline">Open-Meteo</a>
                                </p>
                            @else
                                <p role="status" class="text-sm text-[#D9FFF5]">
                                    {{ $weather['message'] ?? 'Weather information is currently unavailable.' }}
                                </p>
                                <button id="weather-try-again" type="button" class="mt-4 rounded-full border border-[#F5B700]/30 bg-[#2A2A1A] px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#F5B700]">
                                    Try Again
                                </button>
                            @endif
                        </div>
                    </section>

                    <script>
                        document.querySelectorAll('#refresh-weather, #weather-try-again').forEach(function (button) {
                            button.addEventListener('click', function () {
                                window.location.reload();
                            });
                        });
                    </script>

                    <section class="mt-8 rounded-[28px] border border-[#00B894]/15 bg-[#002A23]/80 p-4 md:p-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-[#F5FFFC]">Recent Incident Reports &amp; Field Alerts</h3>
                                <p class="mt-2 text-sm text-[#7DB5A5]">Real-time submissions from barangay coordinators and citizen hotlines</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <button type="button" @click="statusFilterOpen = !statusFilterOpen" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#00B894]/20 bg-[#001F18] px-3 py-2 text-sm font-medium text-[#D9FFF5]">
                                        Filter Status
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-[#7DB5A5]">
                                            <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <div x-show="statusFilterOpen" x-transition @click.outside="statusFilterOpen = false" class="absolute right-0 mt-3 w-40 rounded-2xl border border-[#00B894]/15 bg-[#002A23]/95 p-2 shadow-2xl" style="display: none;">
                                        <button type="button" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-[#D9FFF5] hover:bg-[#00382D]">All</button>
                                        <button type="button" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-[#D9FFF5] hover:bg-[#00382D]">Pending Review</button>
                                        <button type="button" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-[#D9FFF5] hover:bg-[#00382D]">In Progress</button>
                                        <button type="button" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-[#D9FFF5] hover:bg-[#00382D]">Resolved</button>
                                    </div>
                                </div>

                                <button type="button" class="inline-flex items-center gap-2 text-sm font-medium text-[#9EF2D4]">
                                    View All Reports
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M4 10h11"/><path d="m10 4 6 6-6 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="min-w-full text-left">
                                <thead>
                                    <tr class="border-b border-[#00B894]/10 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#7DB5A5]">
                                        <th class="pb-3 pr-4 font-medium">Incident ID &amp; Type</th>
                                        <th class="pb-3 pr-4 font-medium">Location</th>
                                        <th class="pb-3 pr-4 font-medium">Timestamp</th>
                                        <th class="pb-3 pr-4 font-medium">Severity</th>
                                        <th class="pb-3 pr-4 font-medium">Status</th>
                                        <th class="pb-3 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incidentReports as $report)
                                        @php
                                            $severityTone = $statusTone[$report['severity']] ?? 'green';
                                            $statusToneKey = $statusTone[$report['status']] ?? 'green';
                                        @endphp

                                        <tr class="border-b border-[#00B894]/10 align-top text-sm text-[#D9FFF5]">
                                            <td class="py-4 pr-4">
                                                <div class="font-semibold text-[#F5FFFC]">{{ $report['id'] }}</div>
                                                <div class="mt-1 text-[#7DB5A5]">{{ $report['title'] }}</div>
                                            </td>
                                            <td class="py-4 pr-4 text-[#D9FFF5]">{{ $report['location'] }}</td>
                                            <td class="py-4 pr-4 text-[#7DB5A5]">{{ $report['time'] }}</td>
                                            <td class="py-4 pr-4">
                                                <x-dashboard.status-badge label="{{ $report['severity'] }}" tone="{{ $severityTone }}" />
                                            </td>
                                            <td class="py-4 pr-4">
                                                <x-dashboard.status-badge label="{{ $report['status'] }}" tone="{{ $statusToneKey }}" />
                                            </td>
                                            <td class="py-4">
                                                <button type="button" class="rounded-full border border-[#00B894]/20 bg-[#001F18] px-3 py-1.5 text-xs font-semibold text-[#9EF2D4] transition hover:border-[#00B894]/40 hover:bg-[#00382D]">
                                                    {{ $report['action'] }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 border-t border-[#00B894]/10 pt-4 text-sm text-[#7DB5A5] md:flex-row md:items-center md:justify-between">
                            <p>Showing 4 of 1,428 total reports</p>
                            <div class="flex items-center gap-2">
                                <button type="button" class="rounded-full border border-[#00B894]/15 bg-[#001F18] px-3 py-1.5 text-sm text-[#D9FFF5]">Previous</button>
                                <button type="button" class="rounded-full bg-[#19D3AE] px-3 py-1.5 text-sm font-semibold text-[#001F18]">1</button>
                                <button type="button" class="rounded-full border border-[#00B894]/15 bg-[#001F18] px-3 py-1.5 text-sm text-[#D9FFF5]">2</button>
                                <button type="button" class="rounded-full border border-[#00B894]/15 bg-[#001F18] px-3 py-1.5 text-sm text-[#D9FFF5]">3</button>
                                <button type="button" class="rounded-full border border-[#00B894]/15 bg-[#001F18] px-3 py-1.5 text-sm text-[#D9FFF5]">Next</button>
                            </div>
                        </div>
                    </section>
                </main>

                <footer class="border-t border-[#00B894]/10 bg-[#001F18]">
                    <div class="mx-auto flex max-w-[1800px] flex-col gap-3 px-4 py-5 text-xs text-[#7DB5A5] md:flex-row md:items-center md:justify-between md:px-6 xl:px-8">
                        <div class="flex items-center gap-2 font-medium text-[#D9FFF5]">
                            <span>AkoHanda</span>
                            <span class="text-[#7DB5A5]">v2.4</span>
                        </div>

                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                            <span>MDRRMO Municipal Command System</span>
                            <span>Municipality of Arakan, Cotabato</span>
                        </div>

                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                            <span>Emergency Protocol Manual</span>
                            <span>System Diagnostics</span>
                            <span>Helpdesk Hotline</span>
                            <span>(064-521-8899)</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <div x-show="mobileSidebarOpen" x-transition @click="mobileSidebarOpen = false" class="fixed inset-0 z-30 bg-[#001F18]/70 lg:hidden" style="display: none;"></div>
    </div>
@endsection