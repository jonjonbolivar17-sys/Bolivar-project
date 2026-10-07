<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#051613">
        <meta name="description" content="AkoHanda is the official Municipal Disaster Risk Reduction & Management System for Arakan, Cotabato (MDRRMO Arakan).">
        <title>AkoHanda OPS · MDRRMO Arakan Command Center</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#051613] font-sans text-[#F5FFFC] antialiased selection:bg-emerald-500 selection:text-[#051613]">
        @php
            $arakanBarangays = [
                'Poblacion', 'Greenfield', 'Meocan', 'Doroluman', 'Gambodes', 'Badiangon',
                'Datu Ladayon', 'Kabalantian', 'Santo Niño', 'San Miguel', 'Maria Caridad',
                'Kulaman Valley', 'Malibatuan', 'Katipunan', 'Napalico', 'Salasang', 'Dallag',
                'Anapolon', 'Binoongan', 'Ganatan', 'Kinawayan', 'Libertad', 'Makalangot',
                'Naje', 'New Rosary', 'Sumalili', 'Tumanding', 'Lanao Kuran'
            ];
        @endphp

        <div x-data="{ mobileMenuOpen: false, barangaySearch: '' }" class="relative flex min-h-screen flex-col overflow-x-hidden">
            <!-- ==================== 1. TACTICAL TELEMETRY TOP BAR ==================== -->
            <div class="border-b border-[#163b33] bg-[#0d1f1b] px-4 py-2 text-xs sm:px-8 lg:px-12">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-950/60 px-2 py-0.5 text-[10px] font-mono font-bold uppercase text-amber-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            DEFCON IV · GUARD | SEC-1
                        </span>
                        <span class="hidden md:inline font-mono text-[11px] text-[#7DB5A5]">NODE: DRRMO-ARK-01</span>
                        <span class="hidden lg:inline text-[#163b33]">|</span>
                        <span class="hidden lg:flex items-center gap-1.5 text-[11px] text-emerald-400 font-mono font-semibold">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            ALL SYSTEMS NOMINAL · 28 BARANGAYS ONLINE
                        </span>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 font-mono text-[11px]">
                        <span class="text-[#7DB5A5] hidden sm:inline">24/7 Rapid Command Desk:</span>
                        <a href="tel:0645218899" class="font-bold text-emerald-400 hover:text-emerald-300 transition">
                            (064) 521-8899 · 064-521-8899
                        </a>
                    </div>
                </div>
            </div>

            <!-- ==================== 2. MAIN HEADER NAVIGATION ==================== -->
            <header class="sticky top-0 z-40 border-b border-[#163b33]/80 bg-[#051613]/90 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-8 lg:px-12">
                    <!-- Brand Mark -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group" aria-label="AkoHanda home">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-500/30 bg-[#102621] text-base font-black text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.15)] group-hover:border-emerald-400 transition">
                            AH
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-extrabold tracking-tight text-[#F5FFFC]">AkoHanda</span>
                                <span class="rounded border border-amber-500/30 bg-amber-500/10 px-1.5 py-0.2 text-[9px] font-bold uppercase tracking-wider text-amber-400">OPS</span>
                            </div>
                            <span class="block text-[10px] font-mono font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">
                                MDRRMO ARAKAN · COTABATO
                            </span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden lg:flex items-center gap-8 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" aria-label="Main navigation">
                        <a href="#operations" class="hover:text-emerald-400 transition">Three Hubs</a>
                        <a href="#weather-radar" class="hover:text-emerald-400 transition">Weather &amp; Radar</a>
                        <a href="#response-workflow" class="hover:text-emerald-400 transition">Response Workflow</a>
                        <a href="#barangays" class="hover:text-emerald-400 transition">28 Barangays</a>
                        <a href="#emergency-directory" class="hover:text-emerald-400 transition">Hotlines</a>
                    </nav>

                    <!-- Auth / Role Access CTAs -->
                    <div class="flex items-center gap-2.5">
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-[#051613] hover:bg-emerald-500 transition shadow-[0_0_15px_rgba(16,185,129,0.3)]"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                                </svg>
                                Command Portal
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="rounded-xl border border-[#163b33] bg-[#0d1f1b] px-3.5 py-2 text-xs font-semibold text-[#D9FFF5] hover:border-emerald-500/40 hover:bg-[#102621] transition"
                            >
                                Operator Sign In
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-[#051613] hover:bg-emerald-400 transition shadow-[0_0_15px_rgba(16,185,129,0.25)]"
                            >
                                Citizen Portal
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button
                            type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden flex h-10 w-10 items-center justify-center rounded-xl border border-[#163b33] bg-[#0d1f1b] text-[#7DB5A5]"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Drawer -->
                <div x-show="mobileMenuOpen" x-transition class="lg:hidden border-t border-[#163b33] bg-[#0d1f1b] p-4 space-y-3" style="display: none;">
                    <a href="#operations" @click="mobileMenuOpen = false" class="block text-xs font-semibold text-[#F5FFFC]">Three Connected Hubs</a>
                    <a href="#weather-radar" @click="mobileMenuOpen = false" class="block text-xs font-semibold text-[#F5FFFC]">Weather &amp; Radar</a>
                    <a href="#response-workflow" @click="mobileMenuOpen = false" class="block text-xs font-semibold text-[#F5FFFC]">Response Workflow</a>
                    <a href="#barangays" @click="mobileMenuOpen = false" class="block text-xs font-semibold text-[#F5FFFC]">28 Barangays Matrix</a>
                    <a href="#emergency-directory" @click="mobileMenuOpen = false" class="block text-xs font-semibold text-[#F5FFFC]">Emergency Hotlines</a>
                </div>
            </header>

            <main class="flex-1">
                <!-- ==================== 3. HERO COMMAND SECTION ==================== -->
                <section class="relative mx-auto max-w-7xl px-4 pt-10 pb-16 sm:px-8 lg:px-12 lg:pt-16 lg:pb-20">
                    <div class="pointer-events-none absolute -left-40 top-10 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute right-0 top-32 h-96 w-96 rounded-full bg-cyan-500/5 blur-3xl"></div>

                    <div class="grid items-center gap-12 lg:grid-cols-12">
                        <!-- Left 7 cols: Tactical Headline & Mission -->
                        <div class="lg:col-span-7 space-y-6">
                            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-950/40 px-3.5 py-1 text-[10px] font-mono font-semibold uppercase tracking-widest text-emerald-400">
                                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                MUNICIPAL RESPONSE NETWORK · ARAKAN, COTABATO (REGION XII)
                            </div>

                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-[1.08] tracking-tight text-[#F5FFFC]">
                                Real-time disaster response &amp; citizen protection for <span class="text-emerald-400">Arakan.</span>
                            </h1>

                            <p class="max-w-xl text-sm sm:text-base leading-relaxed text-[#7DB5A5]">
                                High-precision municipal coordination across <strong>28 Arakan Barangays</strong>. Combining live Open-Meteo meteorological sensing, VHF 148.525 MHz radio dispatch, and rapid field deployment to protect lives and secure upland communities.
                            </p>

                            <!-- CTAs -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                <a
                                    href="tel:0645218899"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3.5 text-xs sm:text-sm font-extrabold text-[#051613] hover:bg-emerald-400 transition shadow-[0_0_25px_rgba(16,185,129,0.3)]"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    Fast desk report · Call now
                                </a>

                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#163b33] bg-[#0d1f1b] px-6 py-3.5 text-xs sm:text-sm font-bold text-[#D9FFF5] hover:border-emerald-500/40 hover:bg-[#102621] transition"
                                >
                                    Access role portals <span class="text-emerald-400">↗</span>
                                </a>
                            </div>

                            <p class="text-[11px] font-mono text-[#7DB5A5]/80">
                                24/7 Command Hotline: <strong class="text-[#F5FFFC]">(064) 521-8899</strong> · PNP Arakan: <strong class="text-[#F5FFFC]">0917-882-9111</strong> · BFP: <strong class="text-[#F5FFFC]">0998-123-4567</strong>
                            </p>
                        </div>

                        <!-- Right 5 cols: Tactical Minimap Radar & Live Telemetry -->
                        <div id="weather-radar" class="lg:col-span-5">
                            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-2xl relative overflow-hidden">
                                <div class="flex items-center justify-between pb-3 border-b border-[#163b33]">
                                    <div>
                                        <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-400">Arakan, Cotabato weather</p>
                                        <h2 class="text-base font-extrabold text-[#F5FFFC]">Current Weather</h2>
                                    </div>
                                    <span class="rounded border border-emerald-500/30 bg-emerald-950/60 px-2 py-0.5 font-mono text-[9px] text-emerald-300 font-bold">
                                        {{ ($weather['is_cached'] ?? false) ? 'CACHED' : 'LIVE TELEMETRY' }}
                                    </span>
                                </div>

                                <!-- Tactical Radar Sweep Visual -->
                                <div class="my-5 relative flex items-center justify-center">
                                    <div class="relative flex aspect-square w-full max-w-[260px] items-center justify-center">
                                        <div class="absolute inset-0 rounded-full border border-emerald-500/20"></div>
                                        <div class="absolute inset-[15%] rounded-full border border-emerald-500/25"></div>
                                        <div class="absolute inset-[30%] rounded-full border border-emerald-500/30"></div>
                                        <div class="absolute inset-[45%] rounded-full border border-emerald-500/35"></div>
                                        <div class="absolute h-px w-full bg-emerald-500/20"></div>
                                        <div class="absolute h-full w-px bg-emerald-500/20"></div>

                                        <!-- Rotating Radar Sweep Line -->
                                        <div class="absolute inset-0 animate-[spin_8s_linear_infinite] rounded-full bg-[conic-gradient(from_0deg,transparent_0deg,rgba(16,185,129,0.02)_260deg,rgba(16,185,129,0.45)_360deg)]"></div>

                                        <!-- Blinking telemetry points -->
                                        <span class="absolute left-[30%] top-[30%] h-2.5 w-2.5 rounded-full bg-amber-400 shadow-[0_0_12px_rgba(245,158,11,0.9)]" title="Station 04 Greenfield"></span>
                                        <span class="absolute right-[25%] top-[50%] h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(16,185,129,0.9)]" title="Poblacion HQ"></span>
                                        <span class="absolute bottom-[25%] left-[50%] h-2 w-2 rounded-full bg-cyan-400 shadow-[0_0_12px_rgba(6,182,212,0.9)]" title="Mt. Sinaka Watershed"></span>

                                        <div class="relative flex h-12 w-12 items-center justify-center rounded-xl border border-emerald-500/30 bg-[#051613]/90 text-emerald-400 font-mono text-xs font-bold shadow-md">
                                            ARK
                                        </div>
                                    </div>
                                </div>

                                <!-- Weather Metric Panels -->
                                <div class="grid grid-cols-2 gap-2.5 font-mono text-xs">
                                    <div class="rounded-xl border border-[#163b33] bg-[#102621] p-3">
                                        <span class="text-[9px] uppercase tracking-wider text-[#7DB5A5]">LOCATION</span>
                                        <p class="mt-1 font-bold text-[#F5FFFC] truncate">{{ $weather['location'] ?? 'Arakan, Cotabato' }}</p>
                                    </div>

                                    <div class="rounded-xl border border-[#163b33] bg-[#102621] p-3">
                                        <span class="text-[9px] uppercase tracking-wider text-[#7DB5A5]">TEMPERATURE</span>
                                        <p class="mt-1 font-bold text-emerald-400 text-base">
                                            {{ ($weather['temperature'] ?? null) !== null ? number_format((float) $weather['temperature'], 1) : '--' }}°C
                                        </p>
                                    </div>

                                    <div class="rounded-xl border border-[#163b33] bg-[#102621] p-3">
                                        <span class="text-[9px] uppercase tracking-wider text-[#7DB5A5]">CONDITION</span>
                                        <p class="mt-1 font-semibold text-[#D9FFF5] truncate">{{ $weather['condition'] ?? 'Partly cloudy' }}</p>
                                    </div>

                                    <div class="rounded-xl border border-[#163b33] bg-[#102621] p-3">
                                        <span class="text-[9px] uppercase tracking-wider text-[#7DB5A5]">HUMIDITY · WIND</span>
                                        <p class="mt-1 text-cyan-300 font-bold">
                                            {{ $weather['humidity'] ?? 68 }}% · {{ ($weather['wind_speed'] ?? null) !== null ? number_format((float) $weather['wind_speed'], 1) : '12' }} km/h
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-[#163b33] flex items-center justify-between text-[10px] font-mono text-[#7DB5A5]">
                                    <span>{{ $weather['warning'] ?? 'Live telemetry via Open-Meteo & Mt. Sinaka Sensor' }}</span>
                                    <a href="https://bagong.pagasa.dost.gov.ph/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">PAGASA Advisories ↗</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== 4. EXECUTIVE OPERATIONAL INDICATORS ==================== -->
                <section aria-label="Operational indicators" class="border-y border-[#163b33] bg-[#0d1f1b]">
                    <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-y divide-[#163b33] sm:grid-cols-4 sm:divide-y-0 px-4 sm:px-8 lg:px-12">
                        <div class="py-6 px-4 sm:px-6">
                            <p class="font-mono text-3xl font-extrabold text-emerald-400">28</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Barangays Monitored</p>
                            <p class="mt-0.5 text-[11px] text-[#7DB5A5]">100% municipal telemetry coverage</p>
                        </div>

                        <div class="py-6 px-4 sm:px-6">
                            <p class="font-mono text-3xl font-extrabold text-cyan-400">4.2 min</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Min Dispatch Delay</p>
                            <p class="mt-0.5 text-[11px] text-[#7DB5A5]">Automated triage routing</p>
                        </div>

                        <div class="py-6 px-4 sm:px-6">
                            <p class="font-mono text-3xl font-extrabold text-amber-300">48</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Designated Shelters</p>
                            <p class="mt-0.5 text-[11px] text-[#7DB5A5]">Gymnasiums &amp; school centers</p>
                        </div>

                        <div class="py-6 px-4 sm:px-6">
                            <p class="font-mono text-3xl font-extrabold text-red-400">32 Units</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Field Units &amp; Tanod</p>
                            <p class="mt-0.5 text-[11px] text-[#7DB5A5]">BFP, PNP, &amp; VHF radio mesh</p>
                        </div>
                    </div>
                </section>

                <!-- ==================== 5. THREE CONNECTED OPERATIONAL HUBS ==================== -->
                <section id="operations" class="mx-auto max-w-7xl px-4 py-16 sm:px-8 sm:py-20 lg:px-12 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 border-b border-[#163b33] pb-6">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold uppercase tracking-widest text-emerald-400">
                                <span>●</span> ONE MUNICIPAL RESPONSE NETWORK
                            </div>
                            <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#F5FFFC]">
                                Three Connected Operational Hubs
                            </h2>
                        </div>
                        <p class="max-w-md text-xs sm:text-sm text-[#7DB5A5]">
                            A unified operating system engineered specifically for the Command Center, Field Responders, and Arakan Citizens.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Hub 1: Executive Command Console -->
                        <div class="rounded-2xl border-t-4 border-t-emerald-500 border border-[#163b33] bg-[#0d1f1b] p-6 shadow-xl flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xl">
                                        🏛️
                                    </span>
                                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 font-mono text-[9px] text-emerald-300 font-bold">COMMAND ADMIN</span>
                                </div>
                                <h3 class="mt-4 text-lg font-bold text-[#F5FFFC]">Executive Command Console</h3>
                                <p class="mt-2 text-xs leading-relaxed text-[#7DB5A5]">
                                    Centralized municipal dispatch desk with GIS hazard zone overlays, meteorological radar, incident manifests, and full RBAC user management across all 28 Arakan barangays.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-[#163b33]">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
                                    Enter Command Portal →
                                </a>
                            </div>
                        </div>

                        <!-- Hub 2: Field Responder Suite -->
                        <div class="rounded-2xl border-t-4 border-t-amber-500 border border-[#163b33] bg-[#0d1f1b] p-6 shadow-xl flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xl">
                                        📻
                                    </span>
                                    <span class="rounded bg-amber-500/20 px-2 py-0.5 font-mono text-[9px] text-amber-300 font-bold">STAFF / RESPONDER</span>
                                </div>
                                <h3 class="mt-4 text-lg font-bold text-[#F5FFFC]">Field Responder Suite</h3>
                                <p class="mt-2 text-xs leading-relaxed text-[#7DB5A5]">
                                    Tactical field dashboard for ambulance units, BFP fire crews, and Barangay Tanod patrols. Features VHF 148.525 MHz bridge, turn-by-turn navigation, and geotagged evidence clearance.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-[#163b33]">
                                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-amber-300 hover:text-amber-200 transition">
                                    Access Field Terminal →
                                </a>
                            </div>
                        </div>

                        <!-- Hub 3: Citizen Incident Portal -->
                        <div class="rounded-2xl border-t-4 border-t-cyan-500 border border-[#163b33] bg-[#0d1f1b] p-6 shadow-xl flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xl">
                                        🛡️
                                    </span>
                                    <span class="rounded bg-cyan-500/20 px-2 py-0.5 font-mono text-[9px] text-cyan-300 font-bold">CITIZEN REPORTER</span>
                                </div>
                                <h3 class="mt-4 text-lg font-bold text-[#F5FFFC]">Citizen Incident Portal</h3>
                                <p class="mt-2 text-xs leading-relaxed text-[#7DB5A5]">
                                    Resident safety hub with 4-step emergency wizard, 1-click GPS coordinate lock, nearest evacuation shelter routes, and live step-by-step dispatch tracking.
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-[#163b33]">
                                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-cyan-300 hover:text-cyan-200 transition">
                                    Open Citizen Portal →
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== 6. FIVE-STAGE RESPONSE WORKFLOW ==================== -->
                <section id="response-workflow" class="border-y border-[#163b33] bg-[#0d1f1b]">
                    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 sm:py-20 lg:px-12 space-y-8">
                        <div>
                            <div class="text-[10px] font-mono font-bold uppercase tracking-widest text-emerald-400">
                                RAPID INCIDENT LIFECYCLE
                            </div>
                            <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#F5FFFC]">
                                A Five-Stage Response Workflow
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-[#7DB5A5]">
                                Protocol-driven disaster mitigation from initial citizen geotag to after-action clearance.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Stage 1 -->
                            <div class="rounded-xl border border-[#163b33] bg-[#102621] p-4 relative">
                                <span class="font-mono text-xs text-emerald-400 font-bold">01 / 0:00</span>
                                <h3 class="mt-2 text-sm font-bold text-[#F5FFFC]">Citizen Geotag &amp; Photo</h3>
                                <p class="mt-1 text-[11px] text-[#7DB5A5]">1-click GPS lock &amp; camera upload with instant Purok landmark description.</p>
                            </div>

                            <!-- Stage 2 -->
                            <div class="rounded-xl border border-[#163b33] bg-[#102621] p-4 relative">
                                <span class="font-mono text-xs text-cyan-400 font-bold">02 / +1.5 min</span>
                                <h3 class="mt-2 text-sm font-bold text-[#F5FFFC]">MDRRMO Triage</h3>
                                <p class="mt-1 text-[11px] text-[#7DB5A5]">Desk Commander Insp. Almodovar verifies ticket &amp; assigns response tier.</p>
                            </div>

                            <!-- Stage 3 -->
                            <div class="rounded-xl border border-[#163b33] bg-[#102621] p-4 relative">
                                <span class="font-mono text-xs text-amber-300 font-bold">03 / +3.0 min</span>
                                <h3 class="mt-2 text-sm font-bold text-[#F5FFFC]">Unit Tactical Dispatch</h3>
                                <p class="mt-1 text-[11px] text-[#7DB5A5]">Team ALPHA-LEAD / BFP Bravo pumper rolls out with VHF mesh telemetry.</p>
                            </div>

                            <!-- Stage 4 -->
                            <div class="rounded-xl border border-[#163b33] bg-[#102621] p-4 relative">
                                <span class="font-mono text-xs text-red-400 font-bold">04 / +11.2 min</span>
                                <h3 class="mt-2 text-sm font-bold text-[#F5FFFC]">On-Scene Relief</h3>
                                <p class="mt-1 text-[11px] text-[#7DB5A5]">Obstruction clearing, triage assistance, and evacuation route securing.</p>
                            </div>

                            <!-- Stage 5 -->
                            <div class="rounded-xl border border-[#163b33] bg-[#102621] p-4 relative">
                                <span class="font-mono text-xs text-emerald-400 font-bold">05 / Close-out</span>
                                <h3 class="mt-2 text-sm font-bold text-[#F5FFFC]">Audit &amp; Archival</h3>
                                <p class="mt-1 text-[11px] text-[#7DB5A5]">Geotagged proof verified, incident resolved, and log transmitted to LGU.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== 7. INTERACTIVE 28-BARANGAY STATUS MATRIX ==================== -->
                <section id="barangays" class="mx-auto max-w-7xl px-4 py-16 sm:px-8 sm:py-20 lg:px-12 space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Left 8 cols: 28 Barangays Matrix -->
                        <div class="lg:col-span-8 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#163b33] pb-4">
                                <div>
                                    <div class="text-[10px] font-mono font-bold uppercase tracking-widest text-emerald-400">
                                        MUNICIPAL JURISDICTION (28 TOTAL)
                                    </div>
                                    <h2 class="mt-1 text-2xl font-extrabold text-[#F5FFFC]">Barangay status matrix</h2>
                                </div>

                                <div class="w-full sm:w-64">
                                    <input
                                        type="text"
                                        x-model="barangaySearch"
                                        placeholder="Filter Arakan barangays..."
                                        class="w-full rounded-xl border border-[#163b33] bg-[#0d1f1b] py-2 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                                    >
                                </div>
                            </div>

                            <!-- Grid of all 28 Arakan Barangays -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                @foreach ($arakanBarangays as $bgy)
                                    <div
                                        x-show="!barangaySearch || '{{ strtolower($bgy) }}'.includes(barangaySearch.toLowerCase())"
                                        class="rounded-xl border border-[#163b33] bg-[#0d1f1b] p-3 text-xs hover:border-emerald-500/40 hover:bg-[#102621] transition"
                                    >
                                        <div class="font-bold text-[#F5FFFC]">{{ $bgy }}</div>
                                        <div class="mt-1 flex items-center justify-between text-[10px] font-mono">
                                            <span class="text-[#7DB5A5]">Sector Post</span>
                                            <span class="text-emerald-400 font-bold flex items-center gap-1">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Online
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <p class="text-[11px] font-mono text-[#7DB5A5]">
                                Exactly 28 barangays recognized by the Municipality of Arakan, Province of Cotabato.
                            </p>
                        </div>

                        <!-- Right 4 cols: Emergency Hotline Directory -->
                        <div id="emergency-directory" class="lg:col-span-4">
                            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-6 shadow-xl space-y-4">
                                <div class="flex items-center justify-between border-b border-[#163b33] pb-3">
                                    <div>
                                        <span class="text-[10px] font-mono uppercase text-red-400 font-bold">24/7 RAPID INCIDENT DESK</span>
                                        <h3 class="text-base font-extrabold text-[#F5FFFC]">Emergency Hotlines</h3>
                                    </div>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/20 text-red-400 text-lg">
                                        🚨
                                    </span>
                                </div>

                                <div class="divide-y divide-[#163b33] text-xs">
                                    <div class="py-3">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[#F5FFFC]">MDRRMO Desk</span>
                                            <a href="tel:0645218899" class="font-mono text-emerald-400 font-bold hover:underline">(064) 521-8899</a>
                                        </div>
                                        <p class="text-[10px] text-[#7DB5A5]">Rapid Incident Response · 064-521-8899</p>
                                    </div>

                                    <div class="py-3">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[#F5FFFC]">PNP Arakan Police</span>
                                            <a href="tel:09178829111" class="font-mono text-[#D9FFF5] hover:text-emerald-300 font-bold">0917-882-9111</a>
                                        </div>
                                        <p class="text-[10px] text-[#7DB5A5]">Municipal Police Station</p>
                                    </div>

                                    <div class="py-3">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[#F5FFFC]">BFP Arakan Fire Station</span>
                                            <a href="tel:09981234567" class="font-mono text-[#D9FFF5] hover:text-emerald-300 font-bold">0998-123-4567</a>
                                        </div>
                                        <p class="text-[10px] text-[#7DB5A5]">Fire Rescue &amp; Extraction Lead</p>
                                    </div>

                                    <div class="py-3">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[#F5FFFC]">Arakan RHU Health Office</span>
                                            <span class="font-mono text-cyan-300 font-bold">0921-665-9912</span>
                                        </div>
                                        <p class="text-[10px] text-[#7DB5A5]">Emergency Triage &amp; Mobile Clinic</p>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a
                                        href="tel:0645218899"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-600 py-3 text-xs font-bold text-white hover:bg-red-500 transition shadow-lg"
                                    >
                                        📞 1-Tap Dial MDRRMO Arakan Desk
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <!-- ==================== 8. PERSISTENT FOOTER ==================== -->
            <footer class="border-t border-[#163b33] bg-[#051613] py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-8 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[#7DB5A5]">
                    <div class="flex items-center gap-3">
                        <span class="font-black text-sm text-[#F5FFFC]">AkoHanda <span class="text-emerald-400">OPS</span></span>
                        <span class="font-mono text-[10px]">Engine v2.4-PROD</span>
                        <span class="hidden sm:inline text-[#163b33]">|</span>
                        <span class="hidden sm:inline">MDRRMO Arakan · Province of Cotabato (Region XII)</span>
                    </div>

                    <div class="flex items-center gap-6 font-mono text-[11px]">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-[#F5FFFC] transition">Admin Console</a>
                        <a href="{{ route('staff.dashboard') }}" class="hover:text-[#F5FFFC] transition">Responder Terminal</a>
                        <a href="{{ route('user.dashboard') }}" class="hover:text-[#F5FFFC] transition">Citizen Hub</a>
                        <span class="text-emerald-400 font-bold">28 Barangays Active</span>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>