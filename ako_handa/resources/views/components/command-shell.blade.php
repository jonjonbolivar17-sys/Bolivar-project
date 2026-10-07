@props([
    'activeRole' => 'admin', // 'admin', 'staff', or 'citizen'
    'activeRoute' => '',
    'title' => 'AkoHanda OPS · MDRRMO Arakan',
])

@php
    $user = auth()->user();
    $role = $activeRole ?: ($user?->role ?? 'admin');
    if ($role === 'reporter') {
        $role = 'citizen';
    }

    $userName = match ($role) {
        'admin' => $user?->name ?: 'Insp. Rodrigo Almodovar',
        'staff' => $user?->role === 'staff' ? $user->name : 'Officer Mark Alvarez',
        'citizen' => $user?->role === 'reporter' ? $user->name : 'Juan Dela Cruz',
        default => $user?->name ?: 'Operator',
    };

    $userRoleTitle = match ($role) {
        'admin' => 'MDRRMO Incident Commander',
        'staff' => 'Team Alpha Lead / Paramedic',
        'citizen' => 'PhilSys Verified Resident',
        default => 'Operator',
    };

    $initials = collect(explode(' ', $userName))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');

    $displayInitials = $initials !== '' ? $initials : 'AH';

    $arakanBarangays = [
        'Poblacion', 'Greenfield', 'Meocan', 'Doroluman', 'Gambodes', 'Badiangon',
        'Datu Ladayon', 'Kabalantian', 'Santo Niño', 'San Miguel', 'Maria Caridad',
        'Kulaman Valley', 'Malibatuan', 'Katipunan', 'Napalico', 'Salasang', 'Dallag',
        'Anapolon', 'Binoongan', 'Ganatan', 'Kinawayan', 'Libertad', 'Makalangot',
        'Naje', 'New Rosary', 'Sumalili', 'Tumanding', 'Lanao Kuran'
    ];
@endphp

<div
    x-data="{
        sidebarState: 'expanded',
        mobileSidebarOpen: false,
        userMenuOpen: false,
        notificationOpen: false,
        spotlightOpen: false,
        spotlightQuery: '',
        activeRole: '{{ $role }}',
        toggleSidebar() {
            if (window.innerWidth < 1024) {
                this.mobileSidebarOpen = !this.mobileSidebarOpen;
            } else {
                this.sidebarState = this.sidebarState === 'expanded' ? 'rail' : (this.sidebarState === 'rail' ? 'hidden' : 'expanded');
            }
        }
    }"
    @keydown.window.ctrl.b.prevent="toggleSidebar()"
    @keydown.window.meta.b.prevent="toggleSidebar()"
    @keydown.window.ctrl.k.prevent="spotlightOpen = true"
    @keydown.window.meta.k.prevent="spotlightOpen = true"
    @keydown.window.escape="mobileSidebarOpen = false; spotlightOpen = false; notificationOpen = false; userMenuOpen = false"
    class="min-h-screen bg-[#051613] text-[#F5FFFC] font-sans antialiased selection:bg-emerald-500 selection:text-[#051613]"
>
    <div class="flex min-h-screen">
        <!-- ==================== INTERACTIVE COLLAPSIBLE SIDEBAR ==================== -->
        <aside
            :class="mobileSidebarOpen
                ? 'w-72 translate-x-0 p-4'
                : (sidebarState === 'expanded'
                    ? 'w-72 p-4'
                    : (sidebarState === 'rail' ? 'w-[72px] p-2.5' : 'w-0 border-r-0 p-0 overflow-hidden')) + ' -translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 transform border-r border-[#163b33] bg-[#0d1f1b]/95 backdrop-blur-xl transition-[width,transform,padding] duration-300 ease-in-out lg:sticky flex flex-col justify-between"
        >
            <div class="flex flex-col h-full overflow-y-auto overflow-x-hidden scrollbar-none">
                <!-- Sidebar Brand Header -->
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] shadow-[0_0_15px_rgba(16,185,129,0.12)]">
                        <span class="text-base font-black tracking-tight text-emerald-400">AH</span>
                    </div>

                    <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black leading-none tracking-tight text-[#F5FFFC]">AkoHanda</span>
                            <span class="inline-flex items-center rounded-md border border-amber-500/30 bg-amber-500/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-400">OPS</span>
                        </div>
                        <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.2em] text-emerald-400/80">MDRRMO ARAKAN</p>
                    </div>
                </div>

                <!-- Defcon / Node Ticker -->
                <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="mt-5 rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2.5">
                    <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider">
                        <span class="flex items-center gap-1.5 text-amber-400">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            DEFCON IV · GUARD
                        </span>
                        <span class="font-mono text-[#7DB5A5] text-[9px]">SEC-1</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[9px] font-mono text-[#7DB5A5]">
                        <span>NODE: DRRMO-ARK-01</span>
                        <span class="text-emerald-400">ONLINE</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-6 space-y-6">
                    <!-- Category: Core Dispatch -->
                    <div class="space-y-1">
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]/80">
                            CORE DISPATCH
                        </p>

                        <!-- Live Command Desk -->
                        <a
                            href="{{ route('admin.dashboard') }}"
                            title="Live Command Desk"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border py-2 text-sm font-medium transition duration-150 {{ $activeRoute === 'admin.dashboard' ? 'border-emerald-500/40 bg-emerald-950/40 text-emerald-300 shadow-[inset_3px_0_0_#10b981]' : 'border-transparent text-[#94a3b8] hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]' }}"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 {{ $activeRoute === 'admin.dashboard' ? 'text-emerald-400' : 'text-[#7DB5A5] group-hover:text-emerald-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Live Command Desk</span>
                            </span>
                        </a>

                        <!-- User Management & RBAC -->
                        <a
                            href="{{ route('admin.users.index') }}"
                            title="User Management & RBAC"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border py-2 text-sm font-medium transition duration-150 {{ $activeRoute === 'users' ? 'border-emerald-500/40 bg-emerald-950/40 text-emerald-300 shadow-[inset_3px_0_0_#10b981]' : 'border-transparent text-[#94a3b8] hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]' }}"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 {{ $activeRoute === 'users' ? 'text-emerald-400' : 'text-[#7DB5A5] group-hover:text-emerald-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>User Management &amp; RBAC</span>
                            </span>
                            <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="rounded-full bg-emerald-900/60 border border-emerald-700/50 px-1.5 py-0.5 text-[9px] font-mono font-bold text-emerald-300">
                                1,842
                            </span>
                        </a>

                        <!-- Incident Manifest -->
                        <a
                            href="{{ route('admin.dashboard') }}#reports"
                            title="Incident Manifest"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Incident Manifest</span>
                            </span>
                            <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="rounded-full bg-amber-500/20 border border-amber-500/40 px-1.5 py-0.5 text-[9px] font-mono font-bold text-amber-300">
                                14 Active
                            </span>
                        </a>

                        <!-- GIS & Hazard Zones -->
                        <a
                            href="#gis-hazards"
                            title="GIS & Hazard Zones"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" x2="9" y1="3" y2="18"/><line x1="15" x2="15" y1="6" y2="21"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>GIS &amp; Hazard Zones</span>
                            </span>
                        </a>
                    </div>

                    <!-- Category: Operations -->
                    <div class="space-y-1">
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]/80">
                            OPERATIONS
                        </p>

                        <!-- Weather & Sensor Hub -->
                        <a
                            href="{{ route('admin.weather') }}"
                            title="Weather & Sensor Hub"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border py-2 text-sm font-medium transition duration-150 {{ $activeRoute === 'weather' ? 'border-emerald-500/40 bg-emerald-950/40 text-emerald-300 shadow-[inset_3px_0_0_#10b981]' : 'border-transparent text-[#94a3b8] hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]' }}"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Weather &amp; Sensors</span>
                            </span>
                            <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="font-mono text-[10px] text-cyan-400">
                                38mm/h
                            </span>
                        </a>

                        <!-- Asset Logistics -->
                        <a
                            href="#logistics"
                            title="Asset Logistics"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Asset Logistics</span>
                            </span>
                        </a>

                        <!-- Evacuation Shelter Status -->
                        <a
                            href="#shelters"
                            title="Evacuation Shelters"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Evacuation Shelters</span>
                            </span>
                            <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="font-mono text-[9px] text-emerald-400">
                                68% Cap.
                            </span>
                        </a>
                    </div>

                    <!-- Category: Field Responders -->
                    <div class="space-y-1">
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]/80">
                            FIELD RESPONDERS
                        </p>

                        <!-- Staff / Field Dashboard -->
                        <a
                            href="{{ route('staff.dashboard') }}"
                            title="Staff & Field Units"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border py-2 text-sm font-medium transition duration-150 {{ $activeRoute === 'staff' ? 'border-amber-500/40 bg-amber-950/40 text-amber-300 shadow-[inset_3px_0_0_#f59e0b]' : 'border-transparent text-[#94a3b8] hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]' }}"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 {{ $activeRoute === 'staff' ? 'text-amber-400' : 'text-[#7DB5A5] group-hover:text-amber-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08v0c.82.82 2.13.85 3 .07l2.07-1.9"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Active Units &amp; Radios</span>
                            </span>
                            <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="rounded-full bg-amber-500/20 border border-amber-500/40 px-1.5 py-0.5 text-[9px] font-mono font-bold text-amber-300">
                                32 Units
                            </span>
                        </a>

                        <!-- Citizen Emergency Alerts -->
                        <a
                            href="{{ route('user.dashboard') }}"
                            title="Citizen Emergency Portal"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border py-2 text-sm font-medium transition duration-150 {{ $activeRoute === 'citizen' ? 'border-cyan-500/40 bg-cyan-950/40 text-cyan-300 shadow-[inset_3px_0_0_#06b6d4]' : 'border-transparent text-[#94a3b8] hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]' }}"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 {{ $activeRoute === 'citizen' ? 'text-cyan-400' : 'text-[#7DB5A5] group-hover:text-cyan-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Citizen Emergency Alerts</span>
                            </span>
                        </a>
                    </div>

                    <!-- Category: Administration -->
                    <div class="space-y-1">
                        <p x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]/80">
                            ADMINISTRATION
                        </p>

                        <!-- After-Action Logs -->
                        <a
                            href="#logs"
                            title="After-Action Logs"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>After-Action Logs</span>
                            </span>
                        </a>

                        <!-- Telemetry Nodes & API -->
                        <a
                            href="#nodes"
                            title="Telemetry Nodes & API"
                            :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
                            class="group flex items-center gap-3 rounded-xl border border-transparent py-2 text-sm font-medium text-[#94a3b8] transition duration-150 hover:border-[#163b33] hover:bg-[#102621] hover:text-[#F5FFFC]"
                        >
                            <span class="flex items-center gap-3">
                                <svg class="h-4 w-4 shrink-0 text-[#7DB5A5] group-hover:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" x2="12.01" y1="20" y2="20"/>
                                </svg>
                                <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>Telemetry Nodes &amp; API</span>
                            </span>
                        </a>
                    </div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="mt-auto pt-6 space-y-3">
                    <!-- 24/7 Rapid Hotline Card -->
                    <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" x-transition.opacity class="rounded-xl border border-[#163b33] bg-[#102621] p-3 shadow-md">
                        <div class="flex items-center justify-between text-[9px] font-semibold uppercase tracking-wider text-[#7DB5A5]">
                            <span>RAPID INCIDENT DESK</span>
                            <span class="text-emerald-400">24/7 LIVE</span>
                        </div>
                        <a href="tel:0645218899" class="mt-1.5 block font-mono text-base font-bold tracking-tight text-[#F5FFFC] hover:text-emerald-300 transition">
                            (064) 521-8899
                        </a>
                        <p class="mt-1 text-[10px] text-[#7DB5A5]">MDRRMO Arakan Command</p>
                    </div>

                    <!-- Sync status & collapse toggle -->
                    <div class="flex items-center justify-between gap-2 px-1">
                        <div x-show="sidebarState === 'expanded' || mobileSidebarOpen" class="flex items-center gap-2 text-[11px] text-[#7DB5A5]">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="font-mono text-[10px]">Engine v2.4-PROD · 99.98% Synced</span>
                        </div>

                        <button
                            type="button"
                            @click="toggleSidebar()"
                            title="Toggle sidebar size (Ctrl/Cmd+B)"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:border-emerald-500/40 hover:text-emerald-300 transition"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ==================== MAIN APPLICATION AREA ==================== -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- ==================== TOP NAVIGATION BAR ==================== -->
            <header class="sticky top-0 z-30 border-b border-[#163b33] bg-[#051613]/90 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-3 px-4 py-3 md:px-6 xl:px-8">
                    <!-- Left: Mobile Toggle & Brand Mark & Status Pill -->
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="toggleSidebar()"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#163b33] bg-[#0d1f1b] text-[#F5FFFC] transition hover:border-emerald-500/40 hover:bg-[#102621]"
                            title="Toggle navigation (⌘B)"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/><path d="m14 9 3 3-3 3"/>
                            </svg>
                        </button>

                        <div class="hidden sm:block min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm tracking-tight text-[#F5FFFC]">AkoHanda OPS</span>
                                <span class="text-xs text-[#7DB5A5]">· MDRRMO Arakan Command Portal</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[10px] font-mono tracking-wider uppercase text-emerald-400 font-semibold">ALL SYSTEMS NOMINAL · 28 BARANGAYS ONLINE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Instant Role Switcher Tab Bar -->
                    <div class="flex items-center">
                        <div class="flex items-center rounded-xl border border-[#163b33] bg-[#0d1f1b] p-1 shadow-inner">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ $role === 'admin' ? 'bg-emerald-600 text-[#051613] shadow-sm font-bold' : 'text-[#7DB5A5] hover:text-[#F5FFFC]' }}"
                            >
                                <span class="h-1.5 w-1.5 rounded-full {{ $role === 'admin' ? 'bg-[#051613]' : 'bg-emerald-400' }}"></span>
                                Admin
                            </a>
                            <a
                                href="{{ route('staff.dashboard') }}"
                                class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ $role === 'staff' ? 'bg-amber-500 text-[#051613] shadow-sm font-bold' : 'text-[#7DB5A5] hover:text-[#F5FFFC]' }}"
                            >
                                <span class="h-1.5 w-1.5 rounded-full {{ $role === 'staff' ? 'bg-[#051613]' : 'bg-amber-400' }}"></span>
                                Staff / Responder
                            </a>
                            <a
                                href="{{ route('user.dashboard') }}"
                                class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ $role === 'citizen' ? 'bg-cyan-500 text-[#051613] shadow-sm font-bold' : 'text-[#7DB5A5] hover:text-[#F5FFFC]' }}"
                            >
                                <span class="h-1.5 w-1.5 rounded-full {{ $role === 'citizen' ? 'bg-[#051613]' : 'bg-cyan-400' }}"></span>
                                Citizen
                            </a>
                        </div>
                    </div>

                    <!-- Right: Global Search & Notifications & Operator Profile -->
                    <div class="flex items-center gap-2.5">
                        <!-- Global Search Button (⌘K Spotlight) -->
                        <button
                            type="button"
                            @click="spotlightOpen = true"
                            class="hidden md:flex items-center gap-2 rounded-xl border border-[#163b33] bg-[#0d1f1b] px-3 py-1.5 text-xs text-[#7DB5A5] hover:border-emerald-500/40 hover:text-[#F5FFFC] transition"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                            <span>Search incidents, barangays...</span>
                            <kbd class="ml-2 font-mono text-[10px] bg-[#102621] border border-[#163b33] px-1.5 py-0.5 rounded text-emerald-400">⌘K</kbd>
                        </button>

                        <!-- Notification Bell with Popover -->
                        <div class="relative">
                            <button
                                type="button"
                                @click="notificationOpen = !notificationOpen"
                                class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-[#163b33] bg-[#0d1f1b] text-[#F5FFFC] transition hover:border-emerald-500/40 hover:bg-[#102621]"
                                aria-label="Incident Notifications"
                            >
                                <svg class="h-4.5 w-4.5 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                </svg>
                                <span class="absolute top-2 right-2 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div
                                x-show="notificationOpen"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.outside="notificationOpen = false"
                                class="absolute right-0 mt-2 w-80 rounded-2xl border border-[#163b33] bg-[#0d1f1b]/98 backdrop-blur-2xl p-4 shadow-2xl z-50"
                                style="display: none;"
                            >
                                <div class="flex items-center justify-between pb-3 border-b border-[#163b33]">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Arakan Telemetry Feed</span>
                                        <span class="rounded bg-red-500/20 px-1.5 py-0.2 text-[9px] font-mono text-red-400">3 UNREAD</span>
                                    </div>
                                    <span class="text-[10px] text-[#7DB5A5]">Live</span>
                                </div>
                                <div class="mt-3 space-y-2.5 text-xs">
                                    <div class="p-2.5 rounded-xl border border-amber-500/30 bg-amber-950/20">
                                        <div class="flex items-center justify-between text-[10px] font-mono text-amber-300">
                                            <span>#REP-2024-0891 · DISPATCH</span>
                                            <span>2m ago</span>
                                        </div>
                                        <p class="mt-1 text-[#F5FFFC] text-[11px]">Unit Alpha dispatched to Brgy. Greenfield for fallen bamboo obstruction.</p>
                                    </div>
                                    <div class="p-2.5 rounded-xl border border-cyan-500/30 bg-cyan-950/20">
                                        <div class="flex items-center justify-between text-[10px] font-mono text-cyan-300">
                                            <span>WATERSHED GAUGE · MT. SINAKA</span>
                                            <span>8m ago</span>
                                        </div>
                                        <p class="mt-1 text-[#F5FFFC] text-[11px]">Precipitation recorded at 38mm/h. Tinanan River alert level 2.</p>
                                    </div>
                                    <div class="p-2.5 rounded-xl border border-emerald-500/30 bg-emerald-950/20">
                                        <div class="flex items-center justify-between text-[10px] font-mono text-emerald-300">
                                            <span>VHF RELAY NODE 1</span>
                                            <span>15m ago</span>
                                        </div>
                                        <p class="mt-1 text-[#F5FFFC] text-[11px]">Radio mesh operational at 100% capacity covering all 28 barangays.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Operator Profile Dropdown -->
                        <div class="relative">
                            <button
                                type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2.5 rounded-xl border border-[#163b33] bg-[#0d1f1b] p-1.5 pr-3 text-left transition hover:border-emerald-500/40 hover:bg-[#102621]"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#102621] border border-emerald-500/30 text-xs font-bold font-mono text-emerald-400">
                                    {{ $displayInitials }}
                                </span>
                                <div class="hidden lg:block text-left">
                                    <span class="block text-xs font-semibold leading-tight text-[#F5FFFC]">{{ $userName }}</span>
                                    <span class="block text-[10px] font-mono tracking-tight text-[#7DB5A5]">{{ $userRoleTitle }}</span>
                                </div>
                                <svg class="h-3.5 w-3.5 text-[#7DB5A5]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Profile Menu -->
                            <div
                                x-show="userMenuOpen"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.outside="userMenuOpen = false"
                                class="absolute right-0 mt-2 w-56 rounded-2xl border border-[#163b33] bg-[#0d1f1b]/98 backdrop-blur-2xl p-2 shadow-2xl z-50"
                                style="display: none;"
                            >
                                <div class="px-3 py-2 border-b border-[#163b33] mb-1">
                                    <p class="text-xs font-bold text-[#F5FFFC]">{{ $userName }}</p>
                                    <p class="text-[10px] font-mono text-emerald-400">{{ $userRoleTitle }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs text-[#94a3b8] hover:bg-[#102621] hover:text-[#F5FFFC]">
                                    <svg class="h-4 w-4 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Security Profile &amp; 2FA
                                </a>
                                <a href="tel:0645218899" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs text-[#94a3b8] hover:bg-[#102621] hover:text-[#F5FFFC]">
                                    <svg class="h-4 w-4 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    VHF Command Desk Patch
                                </a>
                                <div class="my-1 border-t border-[#163b33]"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-xs text-red-400 hover:bg-red-950/30 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                        Log Out Operator
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ==================== PAGE CONTENT SLOT ==================== -->
            <main class="flex-1 px-4 py-6 md:px-6 xl:px-8 max-w-[1800px] w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- ==================== PERSISTENT FOOTER ==================== -->
            <footer class="border-t border-[#163b33] bg-[#051613] mt-auto">
                <div class="mx-auto flex max-w-[1800px] flex-col gap-3 px-4 py-4 text-xs text-[#7DB5A5] md:flex-row md:items-center md:justify-between md:px-6 xl:px-8">
                    <div class="flex items-center gap-2 font-medium text-[#D9FFF5]">
                        <span class="font-bold text-emerald-400">AkoHanda</span>
                        <span class="font-mono text-[#7DB5A5]">v2.4-PROD</span>
                        <span class="hidden sm:inline text-[#163b33]">|</span>
                        <span class="hidden sm:inline">MDRRMO Municipal Command System · Municipality of Arakan, Cotabato (Region XII)</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px]">
                        <span>MDRRMO Desk: <strong class="text-[#F5FFFC]">(064) 521-8899</strong></span>
                        <span>PNP Arakan: <strong class="text-[#F5FFFC]">0917-882-9111</strong></span>
                        <span>BFP Fire: <strong class="text-[#F5FFFC]">0998-123-4567</strong></span>
                        <span class="text-emerald-400 font-semibold">28 Barangays Active</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- ==================== SPOTLIGHT SEARCH MODAL (⌘K) ==================== -->
    <div
        x-show="spotlightOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4 bg-black/80 backdrop-blur-md"
        style="display: none;"
    >
        <div
            @click.outside="spotlightOpen = false"
            class="w-full max-w-2xl rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-2xl"
        >
            <div class="relative flex items-center border-b border-[#163b33] pb-3">
                <svg class="h-5 w-5 text-emerald-400 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input
                    x-model="spotlightQuery"
                    type="text"
                    placeholder="Search incidents, 28 barangays, radio call signs, or personnel..."
                    class="w-full bg-transparent text-sm text-[#F5FFFC] placeholder-[#7DB5A5] focus:outline-none"
                    autofocus
                >
                <button @click="spotlightOpen = false" class="text-xs font-mono text-[#7DB5A5] bg-[#102621] px-2 py-1 rounded border border-[#163b33]">ESC</button>
            </div>

            <div class="mt-4 max-h-96 overflow-y-auto space-y-4 text-xs">
                <!-- Incidents group -->
                <div>
                    <p class="text-[10px] font-mono font-semibold uppercase tracking-wider text-emerald-400">Live Arakan Incidents</p>
                    <div class="mt-2 space-y-1.5">
                        <a href="{{ route('admin.dashboard') }}#reports" class="flex items-center justify-between p-2 rounded-xl hover:bg-[#102621] text-[#F5FFFC] transition border border-transparent hover:border-[#163b33]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-amber-400">#REP-2024-0891</span>
                                <span>Fallen Bamboo Obstruction · Brgy. Greenfield Highway</span>
                            </span>
                            <span class="font-mono text-[10px] text-amber-300 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-800/40">DISPATCHED</span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}#reports" class="flex items-center justify-between p-2 rounded-xl hover:bg-[#102621] text-[#F5FFFC] transition border border-transparent hover:border-[#163b33]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-cyan-400">#REP-2024-0890</span>
                                <span>River Water Gauge Check · Brgy. Poblacion Bridge</span>
                            </span>
                            <span class="font-mono text-[10px] text-cyan-300 bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-800/40">ON SCENE</span>
                        </a>
                    </div>
                </div>

                <!-- 28 Arakan Barangays quick list -->
                <div>
                    <p class="text-[10px] font-mono font-semibold uppercase tracking-wider text-[#7DB5A5]">Arakan Barangay Jurisdictions (28 Total)</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($arakanBarangays as $bgy)
                            <a href="{{ route('admin.users.index') }}?barangay={{ urlencode($bgy) }}" class="rounded-lg border border-[#163b33] bg-[#102621] px-2 py-1 text-[11px] text-[#94a3b8] hover:border-emerald-500/40 hover:text-emerald-300 transition">
                                {{ $bgy }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Key Personnel & Radio call signs -->
                <div>
                    <p class="text-[10px] font-mono font-semibold uppercase tracking-wider text-[#7DB5A5]">Responders &amp; Radio Call Signs</p>
                    <div class="mt-2 space-y-1">
                        <div class="flex items-center justify-between p-2 rounded-xl bg-[#102621]/60 text-[#F5FFFC]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-emerald-400">CMD-001</span>
                                <span>Insp. Rodrigo Almodovar (MDRRMO Commander)</span>
                            </span>
                            <span class="font-mono text-[10px] text-emerald-400">HQ EOC</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-xl bg-[#102621]/60 text-[#F5FFFC]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-amber-400">ALPHA-LEAD</span>
                                <span>Officer Mark Alvarez (Station 04 Greenfield Ambulance)</span>
                            </span>
                            <span class="font-mono text-[10px] text-amber-300">148.525 MHz</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-xl bg-[#102621]/60 text-[#F5FFFC]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-red-400">BRAVO-PUMPER</span>
                                <span>Sgt. Elena Bautista (BFP Arakan Substation)</span>
                            </span>
                            <span class="font-mono text-[10px] text-red-300">Poblacion</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-xl bg-[#102621]/60 text-[#F5FFFC]">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-teal-400">MEOCAN-07</span>
                                <span>Tanod Danilo Ramos (Brgy. Meocan Quick Reaction)</span>
                            </span>
                            <span class="font-mono text-[10px] text-teal-300">Sector 2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Backdrop -->
    <div
        x-show="mobileSidebarOpen"
        x-transition.opacity
        @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-30 bg-[#051613]/80 backdrop-blur-sm lg:hidden"
        style="display: none;"
    ></div>
</div>

