@extends('layouts.dashboard')

@section('title', 'Staff / Responder Field Dashboard · MDRRMO Arakan')

@section('content')
<x-command-shell active-role="staff" active-route="staff" title="Staff / Responder Field Dashboard · MDRRMO Arakan">
    @php
        $assignedTickets = [
            [
                'id' => 'REP-2024-0891',
                'title' => 'Fallen Bamboo & Road Obstruction',
                'location' => 'Arakan–Greenfield Highway, 300m N of Greenfield ES',
                'barangay' => 'Greenfield',
                'reporter' => 'Juan Dela Cruz',
                'phone' => '+63 917 555 4321',
                'time' => '12m ago (08:42 AM)',
                'severity' => 'Moderate Flash Obstruction',
                'status' => 'Dispatched',
                'status_tone' => 'amber',
                'notes' => 'Large clumps of bamboo fell during heavy rain; 1 lane blocked. Motorcycles passing through shoulder.',
                'eta' => '6 mins · 2.4 km',
            ],
            [
                'id' => 'REP-2024-0890',
                'title' => 'River Water Gauge Check',
                'location' => 'Brgy. Poblacion Bridge, Kulaman River',
                'barangay' => 'Poblacion',
                'reporter' => 'MDRRMO Hydrology Sensor',
                'phone' => '(064) 521-8899',
                'time' => '35m ago (08:19 AM)',
                'severity' => 'Alert Level 2 (Orange)',
                'status' => 'On-Scene',
                'status_tone' => 'cyan',
                'notes' => 'Kulaman River water level elevated to 3.8m. Debris clearance crew on standby at northern pylon.',
                'eta' => 'On Location',
            ],
            [
                'id' => 'REP-2024-0887',
                'title' => 'Slope Stability & Landslide Watch',
                'location' => 'Sitio Kilometer 12, Upland Ridge',
                'barangay' => 'Meocan',
                'reporter' => 'Tanod Danilo Ramos',
                'phone' => '+63 928 776 5431',
                'time' => '1h 10m ago (07:44 AM)',
                'severity' => 'High Soil Saturation',
                'status' => 'On-Scene',
                'status_tone' => 'cyan',
                'notes' => 'Minor gravel runoff detected along slope curve. Warning signage posted; monitoring crack monitors.',
                'eta' => 'On Location',
            ],
            [
                'id' => 'REP-2024-0885',
                'title' => 'Medical Evacuation & Upland Transport',
                'location' => 'Purok 4 Health Outpost',
                'barangay' => 'Kulaman Valley',
                'reporter' => 'Nurse Beltran',
                'phone' => '+63 921 665 9912',
                'time' => '1h 45m ago (07:09 AM)',
                'severity' => 'Urgent Medical Transfer',
                'status' => 'Awaiting BFP Extraction',
                'status_tone' => 'red',
                'notes' => 'Elderly patient with respiratory distress requiring transport to Cotabato Provincial Hospital.',
                'eta' => 'BFP Bravo Unit En Route',
            ],
        ];
    @endphp

    <div
        x-data="{
            operationalStatus: 'Available',
            onSceneCheckedIn: false,
            sosTriggered: false,
            evidenceModalOpen: false,
            gpsNavigating: false,
            activeTicket: '{{ $assignedTickets[0]['id'] }}',
            selectedTicketForEvidence: '{{ $assignedTickets[0]['id'] }}',
            evidencePreview: null,
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.evidencePreview = URL.createObjectURL(file);
                }
            },
            triggerSos() {
                this.sosTriggered = true;
                setTimeout(() => {
                    alert('CRITICAL SOS BEACON BROADCASTED!\n\nCoordinates: 7.3512° N, 125.0489° E\nUnit: ALPHA-LEAD (Paramedic Alvarez)\nRepeater Node 1 & BFP Substation notified.');
                }, 200);
            }
        }"
        class="space-y-6"
    >
        <!-- ==================== A. TACTICAL HEADER & TELEMETRY ==================== -->
        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-2xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg border border-amber-500/40 bg-amber-950/60 px-2.5 py-0.5 text-[10px] font-mono font-bold uppercase text-amber-300">
                            TACTICAL FIELD TERMINAL · SECTOR 2
                        </span>
                        <span class="font-mono text-xs text-[#7DB5A5]">NODE: DRRMO-ARK-01</span>
                    </div>
                    <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight text-[#F5FFFC]">
                        Field Responder Command Desk
                    </h1>
                    <p class="mt-1 text-xs md:text-sm text-[#7DB5A5]">
                        Assigned Unit: <strong class="text-amber-300 font-mono">ALPHA-LEAD (Paramedic Alvarez)</strong> · Station 04 Greenfield Rapid Ambulance
                    </p>
                </div>

                <!-- Live Operational Status Selector -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#7DB5A5] mr-1">DUTY STATUS:</span>

                    <button
                        type="button"
                        @click="operationalStatus = 'Available'"
                        :class="operationalStatus === 'Available' ? 'border-emerald-500 bg-emerald-950/80 text-emerald-300 ring-2 ring-emerald-500/30 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                        class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs transition"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Available
                    </button>

                    <button
                        type="button"
                        @click="operationalStatus = 'On Duty'"
                        :class="operationalStatus === 'On Duty' ? 'border-emerald-500 bg-emerald-950/80 text-emerald-300 ring-2 ring-emerald-500/30 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                        class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs transition"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        On Duty
                    </button>

                    <button
                        type="button"
                        @click="operationalStatus = 'En Route'"
                        :class="operationalStatus === 'En Route' ? 'border-amber-500 bg-amber-950/80 text-amber-300 ring-2 ring-amber-500/30 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                        class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs transition"
                    >
                        <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                        En Route
                    </button>

                    <button
                        type="button"
                        @click="operationalStatus = 'On Scene'"
                        :class="operationalStatus === 'On Scene' ? 'border-cyan-500 bg-cyan-950/80 text-cyan-300 ring-2 ring-cyan-500/30 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                        class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs transition"
                    >
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        On Scene
                    </button>

                    <button
                        type="button"
                        @click="operationalStatus = 'Off Duty'"
                        :class="operationalStatus === 'Off Duty' ? 'border-zinc-500 bg-zinc-900 text-zinc-300 ring-2 ring-zinc-500/30 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                        class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs transition"
                    >
                        <span class="h-2 w-2 rounded-full bg-zinc-500"></span>
                        Off Duty
                    </button>
                </div>
            </div>

            <!-- Live Unit Telemetry Banner & Quick Action Buttons -->
            <div class="mt-4 pt-4 border-t border-[#163b33] flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-xs text-[#7DB5A5]">
                    <span class="flex items-center gap-1.5 text-amber-300 font-bold">
                        <svg class="h-3.5 w-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"/><path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"/><circle cx="12" cy="12" r="2"/><path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"/><path d="M19.1 4.9C23 8.8 23 15.2 19.1 19.1"/>
                        </svg>
                        Radio: ALPHA-LEAD
                    </span>
                    <span class="text-[#163b33]">|</span>
                    <span>Freq: <strong class="text-[#F5FFFC]">148.525 MHz</strong> (VHF Simplex)</span>
                    <span class="text-[#163b33]">|</span>
                    <span>GPS: <strong class="text-cyan-400">7.3512° N, 125.0489° E</strong> (Arakan Sector 2)</span>
                    <span class="text-[#163b33]">|</span>
                    <span class="text-emerald-400 font-semibold">Repeater Node 1 Sync: 100%</span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Signal Beacon SOS -->
                    <button
                        type="button"
                        @click="triggerSos()"
                        :class="sosTriggered ? 'bg-red-600 animate-pulse text-white' : 'border border-red-500/50 bg-red-950/60 text-red-300 hover:bg-red-900/60'"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-lg"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Signal Beacon (SOS)
                    </button>

                    <!-- Attach Field Photo / Proof -->
                    <button
                        type="button"
                        @click="evidenceModalOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/40 bg-emerald-950/50 px-3.5 py-2 text-xs font-semibold text-emerald-300 hover:bg-emerald-900/50 transition"
                    >
                        <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                        </svg>
                        Attach Field Photo / Proof
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== B. FIELD RESPONSE KPI CARDS ==================== -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- KPI 1: My Assigned Incidents -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-amber-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">MY ASSIGNED INCIDENTS</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-500/20 bg-amber-950/50 text-amber-400 font-mono text-xs font-bold">
                        #8
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-[#F5FFFC]">8</span>
                    <span class="rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-red-400">3 Immediate Action</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">1 Critical Priority · 2 Routine Monitoring</p>
            </div>

            <!-- KPI 2: Turn-by-Turn ETA to Active Target -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-cyan-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">ETA TO ACTIVE TARGET</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-cyan-500/20 bg-cyan-950/50 text-cyan-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-cyan-400">6 mins</span>
                    <span class="font-mono text-xs text-[#7DB5A5]">· 2.4 km</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">Via Greenfield Provincial Road · Route Optimal</p>
            </div>

            <!-- KPI 3: Arakan Rain & Vulnerability Index -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-amber-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">ARAKAN RAIN &amp; HAZARD INDEX</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-500/20 bg-amber-950/50 text-amber-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M16 14v6"/><path d="M8 14v6"/><path d="M12 16v6"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-2xl font-extrabold tracking-tight text-amber-300">Moderate Flood</span>
                    <span class="rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-mono text-amber-400">38mm/h</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">Mt. Sinaka Watershed Gauge: 38mm/h Continuous</p>
            </div>

            <!-- KPI 4: Radio Mesh Quality -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-emerald-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">RADIO MESH QUALITY</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-950/50 text-emerald-400 font-mono text-xs font-bold">
                        100%
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-emerald-400">100% VHF</span>
                    <span class="rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-mono text-emerald-400">0.02s Ping</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">Relay Synced via Arakan Repeater Node 1 (Greenfield)</p>
            </div>
        </div>

        <!-- ==================== C. ACTIVE PRIORITY DISPATCH CARD (HERO FOCUS) ==================== -->
        <div class="rounded-2xl border-2 border-amber-500/50 bg-[#0d1f1b] p-5 lg:p-6 shadow-[0_0_30px_rgba(245,158,11,0.08)] relative overflow-hidden">
            <!-- Decorative corner badge -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-[#163b33] pb-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-[#051613]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-amber-400">#REP-2024-0891</span>
                            <span class="rounded bg-red-500/20 border border-red-500/40 px-2 py-0.5 text-[9px] font-mono font-bold text-red-300">CRITICAL PRIORITY · CODE 2</span>
                        </div>
                        <h2 class="mt-1 text-xl md:text-2xl font-black tracking-tight text-[#F5FFFC]">
                            Fallen Bamboo &amp; Road Obstruction on Arakan–Greenfield Highway
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-[#7DB5A5]">Dispatched by Insp. Almodovar (CMD-001)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-5">
                <!-- Left 7 cols: Resident Notes & Telemetry Details -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Resident Reporter Card -->
                    <div class="p-4 rounded-xl border border-[#163b33] bg-[#102621]">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-cyan-950/70 border border-cyan-500/30 flex items-center justify-center text-xs font-bold text-cyan-300 font-mono">
                                    JD
                                </div>
                                <div>
                                    <div class="font-bold text-[#F5FFFC] text-sm">Juan Dela Cruz</div>
                                    <div class="text-[11px] text-[#7DB5A5]">Resident / Purok 3 Leader · Brgy. Greenfield, Arakan</div>
                                </div>
                            </div>

                            <a
                                href="tel:+639175554321"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/40 bg-emerald-950/60 px-3 py-1.5 text-xs font-bold text-emerald-300 hover:bg-emerald-900/60 transition"
                            >
                                <svg class="h-3.5 w-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                1-Tap Direct Call
                            </a>
                        </div>

                        <div class="mt-3 p-3 rounded-lg bg-[#0d1f1b] border border-[#163b33] text-xs text-[#D9FFF5] leading-relaxed">
                            <span class="text-amber-400 font-mono font-bold text-[10px] uppercase block mb-1">FIELD INCIDENT REPORT NOTES:</span>
                            "Large clumps of bamboo fell during heavy rain; 1 lane blocked 300m north of Greenfield Elementary School. Motorcycles passing through mud shoulder. BFP chainsaw crew requested."
                        </div>
                    </div>

                    <!-- Sector Target Details & Breadcrumb Progress -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 font-mono text-xs">
                        <div class="p-3 rounded-xl border border-[#163b33] bg-[#102621]">
                            <span class="text-[10px] text-[#7DB5A5] uppercase">Target Coordinates</span>
                            <div class="mt-1 font-bold text-cyan-300">7.3512° N, 125.0489° E</div>
                        </div>
                        <div class="p-3 rounded-xl border border-[#163b33] bg-[#102621]">
                            <span class="text-[10px] text-[#7DB5A5] uppercase">Sector Jurisdiction</span>
                            <div class="mt-1 font-bold text-emerald-400">Greenfield Station 04</div>
                        </div>
                        <div class="p-3 rounded-xl border border-[#163b33] bg-[#102621] col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-[#7DB5A5] uppercase">VHF Relay</span>
                            <div class="mt-1 font-bold text-amber-300">Repeater 1 Synced</div>
                        </div>
                    </div>

                    <!-- Action CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button
                            type="button"
                            @click="gpsNavigating = true; alert('Turn-by-Turn GPS navigation engaged via Greenfield Provincial Road.\nDestination: Arakan–Greenfield Highway Waypoint 4')"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs md:text-sm font-bold text-[#051613] hover:bg-emerald-500 transition shadow-[0_0_20px_rgba(16,185,129,0.3)]"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                            </svg>
                            <span x-text="gpsNavigating ? 'GPS Nav Active (2.4 km)' : 'Start Turn-by-Turn GPS'">Start Turn-by-Turn GPS</span>
                        </button>

                        <button
                            type="button"
                            @click="onSceneCheckedIn = true; operationalStatus = 'On Scene'; alert('STATUS TRANSMITTED: Unit ALPHA-LEAD has arrived on-scene at Brgy. Greenfield Highway!')"
                            :class="onSceneCheckedIn ? 'bg-cyan-600 text-[#051613] font-bold' : 'border border-cyan-500/50 bg-cyan-950/60 text-cyan-300 hover:bg-cyan-900/60'"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-3 text-xs md:text-sm font-bold transition shadow-md"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            <span x-text="onSceneCheckedIn ? '✓ Arrival Confirmed On-Scene' : 'Check In: On-Scene Arrival'">Check In: On-Scene Arrival</span>
                        </button>

                        <button
                            type="button"
                            @click="selectedTicketForEvidence = 'REP-2024-0891'; evidenceModalOpen = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#163b33] bg-[#102621] px-4 py-3 text-xs md:text-sm font-semibold text-[#D9FFF5] hover:border-emerald-500/40 transition"
                        >
                            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                            </svg>
                            Upload Resolution Evidence
                        </button>
                    </div>
                </div>

                <!-- Right 5 cols: Tactical Minimap Preview with GPS Breadcrumbs -->
                <div class="lg:col-span-5">
                    <div class="h-full min-h-[260px] rounded-2xl border border-[#163b33] bg-[#051613] p-4 flex flex-col justify-between relative overflow-hidden">
                        <!-- Radar grid background -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 20px 20px;"></div>

                        <div class="relative z-10 flex items-center justify-between text-[10px] font-mono">
                            <span class="rounded bg-emerald-950/80 border border-emerald-500/40 px-2 py-0.5 text-emerald-300 font-bold">
                                TACTICAL VECTOR · ARAKAN SECTOR 2
                            </span>
                            <span class="text-cyan-400">148.525 MHz</span>
                        </div>

                        <!-- Stylized Tactical SVG Breadcrumbs Map -->
                        <div class="relative z-10 my-4 flex items-center justify-center">
                            <svg viewBox="0 0 320 180" class="w-full h-auto max-h-[160px] text-emerald-400">
                                <!-- Sector grid -->
                                <circle cx="160" cy="90" r="70" fill="none" stroke="#163b33" stroke-dasharray="3 3"/>
                                <circle cx="160" cy="90" r="40" fill="none" stroke="#163b33"/>
                                <line x1="160" y1="10" x2="160" y2="170" stroke="#12382f" stroke-dasharray="2 2"/>
                                <line x1="20" y1="90" x2="300" y2="90" stroke="#12382f" stroke-dasharray="2 2"/>

                                <!-- Route Vector Breadcrumbs -->
                                <path d="M 50 140 Q 110 110, 160 85 T 260 40" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="140" r="6" fill="#10b981"/>
                                <circle cx="110" cy="110" r="3" fill="#14b8a6"/>
                                <circle cx="160" cy="85" r="3" fill="#14b8a6"/>
                                <circle cx="210" cy="62" r="3" fill="#14b8a6"/>

                                <!-- Origin: Station 04 Greenfield -->
                                <text x="50" y="160" fill="#7DB5A5" font-size="9" font-family="monospace" text-anchor="middle">Station 04 (Start)</text>

                                <!-- Destination Waypoint (Blinking Red) -->
                                <circle cx="260" cy="40" r="8" fill="#f59e0b" fill-opacity="0.3">
                                    <animate attributeName="r" values="6;12;6" dur="2s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="260" cy="40" r="5" fill="#ef4444"/>
                                <text x="260" y="25" fill="#f59e0b" font-size="9" font-family="monospace" font-weight="bold" text-anchor="middle">INCIDENT #0891</text>
                            </svg>
                        </div>

                        <div class="relative z-10 flex items-center justify-between text-[10px] font-mono text-[#7DB5A5] pt-2 border-t border-[#163b33]">
                            <span>Greenfield Prov. Road · 2.4 km</span>
                            <span class="text-emerald-400">GPS ACCURACY: ±1.2m</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== D. FIELD INCIDENT QUEUE & EVIDENCE UPLOADS ==================== -->
        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#163b33] bg-[#102621]/40">
                <div>
                    <h3 class="text-sm font-bold text-[#F5FFFC]">Assigned Incident Queue &amp; Dispatch Backlog</h3>
                    <p class="text-xs text-[#7DB5A5]">Real-time operational tickets assigned to Unit ALPHA-LEAD and Arakan Tanod Patrols</p>
                </div>
                <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-mono text-amber-300 font-bold">
                    4 QUEUED
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#163b33] text-[10px] font-mono uppercase tracking-wider text-[#7DB5A5] bg-[#091b17]">
                            <th class="py-3 px-4 font-semibold">Incident ID &amp; Type</th>
                            <th class="py-3 px-4 font-semibold">Location &amp; Barangay</th>
                            <th class="py-3 px-4 font-semibold">Reporter &amp; Hotlines</th>
                            <th class="py-3 px-4 font-semibold">Severity &amp; Status</th>
                            <th class="py-3 px-4 font-semibold">ETA / Progress</th>
                            <th class="py-3 px-4 text-right font-semibold">Field Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#163b33]/60">
                        @foreach ($assignedTickets as $ticket)
                            <tr class="hover:bg-[#102621]/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-amber-300">#{{ $ticket['id'] }}</div>
                                    <div class="text-[#F5FFFC] font-semibold mt-0.5">{{ $ticket['title'] }}</div>
                                    <div class="text-[10px] font-mono text-[#7DB5A5]">{{ $ticket['time'] }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-[#F5FFFC] font-medium">{{ $ticket['location'] }}</div>
                                    <div class="text-emerald-400 font-mono text-[10px]">Brgy. {{ $ticket['barangay'] }}, Arakan</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono">
                                    <div class="text-[#F5FFFC]">{{ $ticket['reporter'] }}</div>
                                    <div class="text-[#7DB5A5] text-[10px]">{{ $ticket['phone'] }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-xs font-semibold text-[#D9FFF5]">{{ $ticket['severity'] }}</div>
                                    <div class="mt-1">
                                        @if ($ticket['status_tone'] === 'amber')
                                            <span class="inline-flex items-center gap-1 rounded-full border border-amber-500/40 bg-amber-950/60 px-2 py-0.5 text-[10px] font-mono font-bold text-amber-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                {{ $ticket['status'] }}
                                            </span>
                                        @elseif ($ticket['status_tone'] === 'cyan')
                                            <span class="inline-flex items-center gap-1 rounded-full border border-cyan-500/40 bg-cyan-950/60 px-2 py-0.5 text-[10px] font-mono font-bold text-cyan-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                                                {{ $ticket['status'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full border border-red-500/40 bg-red-950/60 px-2 py-0.5 text-[10px] font-mono font-bold text-red-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                                {{ $ticket['status'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs">
                                    <span class="text-emerald-400 font-bold">{{ $ticket['eta'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click="selectedTicketForEvidence = '{{ $ticket['id'] }}'; evidenceModalOpen = true"
                                            class="rounded-lg border border-emerald-500/40 bg-emerald-950/60 px-2.5 py-1 text-[11px] font-bold text-emerald-300 hover:bg-emerald-900/60 transition"
                                        >
                                            Upload Evidence
                                        </button>
                                        <button
                                            type="button"
                                            @click="alert('Ticket #{{ $ticket['id'] }} marked cleared & after-action report filed to MDRRMO Arakan Command Desk.')"
                                            class="rounded-lg border border-[#163b33] bg-[#102621] px-2.5 py-1 text-[11px] font-semibold text-[#D9FFF5] hover:border-emerald-500/40 hover:text-emerald-300 transition"
                                        >
                                            Resolve
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== EVIDENCE UPLOAD MODAL / DRAWER ==================== -->
        <div
            x-show="evidenceModalOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md"
            style="display: none;"
        >
            <div
                @click.outside="evidenceModalOpen = false"
                class="w-full max-w-xl rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-6 shadow-2xl space-y-4"
            >
                <div class="flex items-center justify-between border-b border-[#163b33] pb-3">
                    <div>
                        <h3 class="text-base font-bold text-[#F5FFFC]" x-text="'Attach Field Photo Proof: #' + selectedTicketForEvidence"></h3>
                        <p class="text-xs font-mono text-emerald-400">MDRRMO ARAKAN INCIDENT RESOLUTION VERIFICATION</p>
                    </div>
                    <button @click="evidenceModalOpen = false" class="text-sm font-mono text-[#7DB5A5] hover:text-[#F5FFFC]">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Geostamp ticker -->
                    <div class="p-3 rounded-xl border border-cyan-500/30 bg-cyan-950/20 font-mono text-[11px] text-cyan-300 flex items-center justify-between">
                        <span>📍 GPS STAMP: 7.3512° N, 125.0489° E</span>
                        <span>ALT: 412m · SEC-2</span>
                    </div>

                    <!-- Photo Upload / Camera Trigger -->
                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Capture or Upload On-Scene Photo Proof</label>
                        <div class="relative border-2 border-dashed border-[#163b33] rounded-2xl p-6 text-center hover:border-emerald-500/50 transition bg-[#102621]/40">
                            <input
                                type="file"
                                accept="image/*"
                                @change="previewImage($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            >
                            <template x-if="!evidencePreview">
                                <div class="space-y-2">
                                    <svg class="mx-auto h-8 w-8 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                                    </svg>
                                    <div class="text-xs text-[#D9FFF5] font-semibold">Tap to launch camera or select timestamped photo</div>
                                    <p class="text-[10px] text-[#7DB5A5] font-mono">PNG, JPG up to 15MB with embedded EXIF geolocation</p>
                                </div>
                            </template>
                            <template x-if="evidencePreview">
                                <div class="space-y-2">
                                    <img :src="evidencePreview" class="mx-auto max-h-48 rounded-xl border border-emerald-500/40 object-cover" alt="Evidence Preview">
                                    <p class="text-[10px] font-mono text-emerald-400">✓ Image attached · Geotag verified</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Officer clearance notes -->
                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">On-Scene Clearance Notes &amp; Actions Taken</label>
                        <textarea
                            rows="3"
                            placeholder="e.g. Bamboo obstruction sawed and cleared off the highway shoulder. Flow of vehicles restored in both directions."
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] p-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#163b33] flex justify-end gap-2">
                    <button @click="evidenceModalOpen = false" class="rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2 text-xs text-[#7DB5A5] hover:text-[#F5FFFC]">Cancel</button>
                    <button
                        @click="alert('Geolocated evidence and timestamp logged to MDRRMO Arakan Command Desk. Incident cleared!'); evidenceModalOpen = false"
                        class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-[#051613] hover:bg-emerald-500 transition shadow-lg"
                    >
                        Verify Clearance &amp; Mark Resolved
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-command-shell>
@endsection