@extends('layouts.dashboard')

@section('title', 'Citizen / User Dashboard · MDRRMO Arakan')

@section('content')
<x-command-shell active-role="citizen" active-route="citizen" title="Citizen / User Dashboard · MDRRMO Arakan">
    @php
        $arakanBarangays = [
            'Greenfield', 'Poblacion', 'Meocan', 'Doroluman', 'Gambodes', 'Badiangon',
            'Datu Ladayon', 'Kabalantian', 'Santo Niño', 'San Miguel', 'Maria Caridad',
            'Kulaman Valley', 'Malibatuan', 'Katipunan', 'Napalico', 'Salasang', 'Dallag',
            'Anapolon', 'Binoongan', 'Ganatan', 'Kinawayan', 'Libertad', 'Makalangot',
            'Naje', 'New Rosary', 'Sumalili', 'Tumanding', 'Lanao Kuran'
        ];

        $hazardCategories = [
            ['id' => 'road_obstruction', 'label' => 'Road Obstruction', 'icon' => '🚧', 'desc' => 'Debris, fallen rocks, or blocked roadway'],
            ['id' => 'flash_flood', 'label' => 'Flash Flood', 'icon' => '🌊', 'desc' => 'Rising waters, overflow, or canal breach'],
            ['id' => 'landslide', 'label' => 'Landslide', 'icon' => '⛰️', 'desc' => 'Soil erosion, slope failure, or mudflow'],
            ['id' => 'fallen_tree', 'label' => 'Fallen Tree', 'icon' => '🌲', 'desc' => 'Uprooted tree blocking road or roof'],
            ['id' => 'medical_emergency', 'label' => 'Medical Emergency', 'icon' => '🚑', 'desc' => 'Critical injury, trauma, or triage needs'],
            ['id' => 'fire', 'label' => 'Fire / Smoke', 'icon' => '🔥', 'desc' => 'Residential, brush, or forest fire'],
            ['id' => 'power_line', 'label' => 'Fallen Power Line', 'icon' => '⚡', 'desc' => 'Exposed live wire or transformer spark'],
            ['id' => 'river_swell', 'label' => 'River Swell', 'icon' => '🏞️', 'desc' => 'Kulaman/Tinanan river high watermark'],
            ['id' => 'vehicular_accident', 'label' => 'Vehicular Accident', 'icon' => '💥', 'desc' => 'Collision, motorcycle spill, or roll-over'],
            ['id' => 'other', 'label' => 'Other Hazard', 'icon' => '⚠️', 'desc' => 'General hazard requiring MDRRMO dispatch'],
        ];
    @endphp

    <div
        x-data="{
            reportWizardOpen: false,
            wizardStep: 1,
            selectedCategory: 'power_line',
            selectedBarangay: 'Greenfield',
            selectedPurok: 'Purok 3',
            landmark: 'Beside Purok 3 Basketball Court & Waiting Shed',
            gpsCoordinates: '7.3521° N, 125.1282° E',
            gpsLocked: true,
            severityLevel: 'High',
            roadImpassable: false,
            casualtiesDetected: false,
            liveWiresDetected: true,
            incidentDescription: 'High-voltage electric cable snapped during thunder storm and is dangling across the road near residential houses.',
            photoPreview: null,
            submitting: false,
            reportSubmittedSuccess: false,
            useCurrentLocation() {
                this.gpsCoordinates = '7.3521° N, 125.1282° E';
                this.selectedBarangay = 'Greenfield';
                this.gpsLocked = true;
                alert('GPS Locked: 7.3521° N, 125.1282° E\nReverse geocoded to Brgy. Greenfield, Arakan.');
            },
            previewPhoto(e) {
                const file = e.target.files[0];
                if (file) {
                    this.photoPreview = URL.createObjectURL(file);
                }
            },
            submitReport() {
                this.submitting = true;
                setTimeout(() => {
                    this.submitting = false;
                    this.reportWizardOpen = false;
                    this.reportSubmittedSuccess = true;
                    alert('SUCCESS!\n\nYour emergency hazard report was transmitted to the MDRRMO Arakan Command Desk.\nTicket #REP-2024-0893 has been provisioned.');
                }, 800);
            }
        }"
        class="space-y-6"
    >
        <!-- ==================== A. RESIDENT WELCOME & LOCAL READINESS ==================== -->
        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-2xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg border border-cyan-500/40 bg-cyan-950/60 px-2.5 py-0.5 text-[10px] font-mono font-bold uppercase text-cyan-300">
                            PhilSys Verified Resident
                        </span>
                        <span class="font-mono text-xs text-[#7DB5A5]">Brgy. Greenfield, Purok 3, Arakan</span>
                    </div>
                    <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight text-[#F5FFFC]">
                        Welcome, Juan Dela Cruz
                    </h1>
                    <p class="mt-1 text-xs md:text-sm text-[#7DB5A5]">
                        Arakan Resident Command Portal · Instant emergency assistance &amp; barangay disaster readiness
                    </p>
                </div>

                <!-- 24/7 Hotline Quick Dial Button -->
                <div class="flex items-center gap-3">
                    <a
                        href="tel:0645218899"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-500/50 bg-red-950/60 px-4 py-2.5 text-xs font-bold text-red-300 hover:bg-red-900/60 transition shadow-lg"
                    >
                        <svg class="h-4 w-4 text-red-400 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>MDRRMO Hotline: (064) 521-8899</span>
                    </a>
                </div>
            </div>

            <!-- Synoptic Hazard Alert Banner & Nearest Evacuation Center Row -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mt-5 pt-4 border-t border-[#163b33]">
                <!-- Weather & Upland River Alert Banner (7 cols) -->
                <div class="lg:col-span-7 rounded-xl border border-amber-500/40 bg-amber-950/25 p-4 flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/20 text-amber-300 text-lg">
                        ⚠️
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold font-mono uppercase text-amber-300">SYNOPTIC HAZARD ADVISORY · ARAKAN HIGHLANDS</span>
                            <span class="rounded bg-amber-500/20 px-1.5 py-0.2 text-[9px] font-mono text-amber-400">ALERT LVL 2</span>
                        </div>
                        <p class="mt-1 text-xs text-[#F5FFFC] leading-relaxed">
                            Mt. Sinaka Watershed gauge recording continuous upland rain (38mm/h). Kulaman &amp; Tinanan River water levels elevated. Residents near low-lying riverbanks advised to keep Go-Bags ready.
                        </p>
                    </div>
                </div>

                <!-- Nearest Evacuation Center (5 cols) -->
                <div class="lg:col-span-5 rounded-xl border border-emerald-500/40 bg-emerald-950/25 p-4 flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 text-lg">
                        🛡️
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-300">Greenfield Central Gymnasium</span>
                            <span class="font-mono text-[10px] text-emerald-400">450m away</span>
                        </div>
                        <p class="mt-1 text-xs text-[#D9FFF5]">
                            Designated Safe Shelter · 68% remaining capacity (420 family food packs &amp; RHU medics on-site).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== B. HIGH-VISIBILITY ACTION CARDS (4 CARDS) ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Action Card 1: 🚨 Report New Incident / Hazard (Highlighted Primary Emerald Glow) -->
            <button
                type="button"
                @click="wizardStep = 1; reportWizardOpen = true"
                class="group text-left rounded-2xl border-2 border-emerald-400 bg-gradient-to-br from-[#102621] to-[#0d1f1b] p-5 shadow-[0_0_25px_rgba(16,185,129,0.25)] hover:border-emerald-300 transition duration-200 relative overflow-hidden"
            >
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500 text-2xl shadow-lg">
                        🚨
                    </span>
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-[#F5FFFC] group-hover:text-emerald-300 transition">
                    Report New Incident / Hazard
                </h3>
                <p class="mt-1 text-xs text-[#7DB5A5]">
                    1-Tap GPS emergency reporting directly to MDRRMO Arakan Command Desk.
                </p>
                <div class="mt-3 flex items-center gap-1 font-mono text-xs font-bold text-emerald-400">
                    <span>Launch 4-Step Wizard</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </button>

            <!-- Action Card 2: 📋 Track My Submitted Reports -->
            <a
                href="#incident-tracker"
                class="group rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-lg hover:border-emerald-500/40 hover:bg-[#102621] transition"
            >
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] text-2xl">
                        📋
                    </span>
                    <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-mono text-amber-300 font-bold">
                        1 Active Ticket
                    </span>
                </div>
                <h3 class="mt-4 text-base font-bold text-[#F5FFFC] group-hover:text-emerald-300 transition">
                    Track My Submitted Reports
                </h3>
                <p class="mt-1 text-xs text-[#7DB5A5]">
                    Live status, dispatcher logs, and responder ETA on your open reports.
                </p>
                <div class="mt-3 flex items-center gap-1 font-mono text-xs font-semibold text-[#7DB5A5]">
                    <span>View Ticket Progress</span>
                    <span>→</span>
                </div>
            </a>

            <!-- Action Card 3: 📍 GPS Emergency Identifier -->
            <button
                type="button"
                @click="alert('YOUR CURRENT VERIFIED ARAKAN TELEMETRY:\n\nCoordinates: 7.3521° N, 125.1282° E\nBarangay: Greenfield (Purok 3)\nMunicipality: Arakan, Cotabato\nStation: Sector 04 Rapid Station')"
                class="group text-left rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-lg hover:border-cyan-500/40 hover:bg-[#102621] transition"
            >
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] text-2xl">
                        📍
                    </span>
                    <span class="font-mono text-[10px] text-cyan-400">GPS ACCURATE</span>
                </div>
                <h3 class="mt-4 text-base font-bold text-[#F5FFFC] group-hover:text-cyan-300 transition">
                    GPS Emergency Identifier
                </h3>
                <p class="mt-1 text-xs text-[#7DB5A5]">
                    Transmit exact coordinates: <strong class="text-cyan-400 font-mono">7.3521° N, 125.1282° E</strong>.
                </p>
                <div class="mt-3 flex items-center gap-1 font-mono text-xs font-semibold text-cyan-400">
                    <span>Transmit Coordinates</span>
                    <span>→</span>
                </div>
            </button>

            <!-- Action Card 4: 🛡️ Evacuation Routes & Shelters -->
            <button
                type="button"
                @click="alert('NEAREST DESIGNATED SHELTERS IN ARAKAN:\n\n1. Greenfield Central Gym (450m · 68% Open)\n2. Poblacion Municipal Hall Evacuation Wing (1.8 km · 85% Open)\n3. Meocan Elementary Covered Court (3.4 km · 90% Open)')"
                class="group text-left rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-lg hover:border-emerald-500/40 hover:bg-[#102621] transition"
            >
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] text-2xl">
                        🛡️
                    </span>
                    <span class="font-mono text-[10px] text-emerald-400">28 ZONES</span>
                </div>
                <h3 class="mt-4 text-base font-bold text-[#F5FFFC] group-hover:text-emerald-300 transition">
                    Evacuation Routes &amp; Shelters
                </h3>
                <p class="mt-1 text-xs text-[#7DB5A5]">
                    Access safe high-ground pathways and designated barangay gymnasiums.
                </p>
                <div class="mt-3 flex items-center gap-1 font-mono text-xs font-semibold text-emerald-400">
                    <span>View 28 Shelters</span>
                    <span>→</span>
                </div>
            </button>
        </div>

        <!-- ==================== C. LIVE INCIDENT TRACKER (STEP-BY-STEP STEPPER) ==================== -->
        <section id="incident-tracker" class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 lg:p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#163b33] pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-amber-400">TICKET #REP-2024-0892</span>
                        <span class="rounded bg-amber-500/20 border border-amber-500/40 px-2 py-0.5 text-[9px] font-mono font-bold text-amber-300">
                            IN PROGRESS · RESPONDER EN ROUTE
                        </span>
                    </div>
                    <h2 class="mt-1 text-lg md:text-xl font-bold text-[#F5FFFC]">
                        Fallen Power Line near Purok 3 Basketball Court, Brgy. Greenfield
                    </h2>
                </div>

                <div class="flex items-center gap-2 font-mono text-xs text-[#7DB5A5]">
                    <span>Submitted: Today, 08:14 AM</span>
                </div>
            </div>

            <!-- Stepper Component (5 Steps) -->
            <div class="py-2">
                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6 md:gap-2">
                    <!-- Progress line background -->
                    <div class="hidden md:block absolute top-5 left-8 right-8 h-1 bg-[#163b33] -z-0">
                        <div class="h-full bg-emerald-500 w-1/2"></div>
                    </div>

                    <!-- Step 1: Submitted -->
                    <div class="flex md:flex-col items-center gap-3 md:text-center z-10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[#051613] font-bold shadow-md">
                            ✓
                        </div>
                        <div>
                            <p class="font-bold text-xs text-emerald-400">1. Submitted</p>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">Today, 08:14 AM</p>
                            <span class="text-[9px] text-[#7DB5A5] hidden md:block">Verified receipt</span>
                        </div>
                    </div>

                    <!-- Step 2: Verified by MDRRMO Arakan -->
                    <div class="flex md:flex-col items-center gap-3 md:text-center z-10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[#051613] font-bold shadow-md">
                            ✓
                        </div>
                        <div>
                            <p class="font-bold text-xs text-emerald-400">2. Verified by MDRRMO</p>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">Today, 08:18 AM</p>
                            <span class="text-[9px] text-[#7DB5A5] hidden md:block">Insp. Almodovar (CMD-001)</span>
                        </div>
                    </div>

                    <!-- Step 3: Responder Dispatched (Active In Progress) -->
                    <div class="flex md:flex-col items-center gap-3 md:text-center z-10">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-amber-400 bg-amber-950 text-amber-300 font-bold shadow-[0_0_15px_rgba(245,158,11,0.4)] animate-pulse">
                            3
                        </div>
                        <div>
                            <p class="font-bold text-xs text-amber-300">3. Responder Dispatched</p>
                            <p class="text-[10px] font-mono text-amber-400">Unit Alpha · ETA 4 mins</p>
                            <span class="text-[9px] text-[#7DB5A5] hidden md:block">Paramedic Alvarez on-way</span>
                        </div>
                    </div>

                    <!-- Step 4: On-Scene Assistance -->
                    <div class="flex md:flex-col items-center gap-3 md:text-center z-10 opacity-60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#163b33] bg-[#102621] text-[#7DB5A5] font-bold">
                            4
                        </div>
                        <div>
                            <p class="font-bold text-xs text-[#7DB5A5]">4. On-Scene Assistance</p>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">COTELCO &amp; BFP</p>
                            <span class="text-[9px] text-[#7DB5A5] hidden md:block">Wire isolation crew</span>
                        </div>
                    </div>

                    <!-- Step 5: Resolved & Cleared -->
                    <div class="flex md:flex-col items-center gap-3 md:text-center z-10 opacity-60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#163b33] bg-[#102621] text-[#7DB5A5] font-bold">
                            5
                        </div>
                        <div>
                            <p class="font-bold text-xs text-[#7DB5A5]">5. Resolved &amp; Cleared</p>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">Final Verification</p>
                            <span class="text-[9px] text-[#7DB5A5] hidden md:block">After-action signoff</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Dispatch Details Strip -->
            <div class="p-4 rounded-xl border border-[#163b33] bg-[#102621] flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span class="text-[#D9FFF5]">
                        Paramedic Alvarez (ALPHA-LEAD) is en route with portable fire-blanket &amp; warning barricades.
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        href="tel:0645218899"
                        class="rounded-lg border border-[#163b33] bg-[#0d1f1b] px-3 py-1.5 font-mono text-[11px] text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                    >
                        Call Command Desk
                    </a>
                </div>
            </div>
        </section>

        <!-- ==================== D. 4-STEP INCIDENT REPORTING WIZARD MODAL ==================== -->
        <div
            x-show="reportWizardOpen"
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
                @click.outside="reportWizardOpen = false"
                class="w-full max-w-2xl rounded-2xl border-2 border-emerald-500/50 bg-[#0d1f1b] p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto"
            >
                <!-- Wizard Header -->
                <div class="flex items-center justify-between border-b border-[#163b33] pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold uppercase text-emerald-400" x-text="'STEP ' + wizardStep + ' OF 4'"></span>
                            <span class="rounded bg-emerald-500/20 px-2 py-0.2 text-[9px] font-mono text-emerald-400">MDRRMO ARAKAN DIRECT UPLINK</span>
                        </div>
                        <h2 class="mt-1 text-lg md:text-xl font-extrabold text-[#F5FFFC]">
                            Report Emergency Incident / Hazard
                        </h2>
                    </div>
                    <button @click="reportWizardOpen = false" class="text-sm font-mono text-[#7DB5A5] hover:text-[#F5FFFC]">✕ ESC</button>
                </div>

                <!-- Wizard Progress Bar -->
                <div class="grid grid-cols-4 gap-2">
                    <div class="h-1.5 rounded-full" :class="wizardStep >= 1 ? 'bg-emerald-500' : 'bg-[#163b33]'"></div>
                    <div class="h-1.5 rounded-full" :class="wizardStep >= 2 ? 'bg-emerald-500' : 'bg-[#163b33]'"></div>
                    <div class="h-1.5 rounded-full" :class="wizardStep >= 3 ? 'bg-emerald-500' : 'bg-[#163b33]'"></div>
                    <div class="h-1.5 rounded-full" :class="wizardStep >= 4 ? 'bg-emerald-500' : 'bg-[#163b33]'"></div>
                </div>

                <!-- ==================== STEP 1: HAZARD CATEGORY (10 TILES) ==================== -->
                <div x-show="wizardStep === 1" class="space-y-3">
                    <p class="text-xs text-[#7DB5A5]">Select the category that best describes the hazard:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        @foreach ($hazardCategories as $cat)
                            <button
                                type="button"
                                @click="selectedCategory = '{{ $cat['id'] }}'"
                                :class="selectedCategory === '{{ $cat['id'] }}' ? 'border-emerald-500 bg-emerald-950/60 ring-2 ring-emerald-500/40 text-emerald-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:border-emerald-500/30 hover:text-[#F5FFFC]'"
                                class="flex flex-col items-center text-center p-3 rounded-xl border transition group"
                            >
                                <span class="text-2xl mb-1.5 group-hover:scale-110 transition-transform">{{ $cat['icon'] }}</span>
                                <span class="text-xs leading-tight font-semibold">{{ $cat['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- ==================== STEP 2: GEOLOCATION & ARAKAN BARANGAY ==================== -->
                <div x-show="wizardStep === 2" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-[#7DB5A5]">Pin the exact location within Arakan jurisdiction:</span>
                        <button
                            type="button"
                            @click="useCurrentLocation()"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-cyan-500/50 bg-cyan-950/60 px-3 py-1 text-xs font-mono font-bold text-cyan-300 hover:bg-cyan-900/60 transition"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/><path d="M12 2v3"/><path d="M12 19v3"/><path d="M2 12h3"/><path d="M19 12h3"/>
                            </svg>
                            1-Click: Use My Current GPS Location
                        </button>
                    </div>

                    <div class="p-3.5 rounded-xl border border-cyan-500/30 bg-cyan-950/20 font-mono text-xs text-cyan-300 flex items-center justify-between">
                        <span>LOCKED COORDINATES: <strong x-text="gpsCoordinates"></strong></span>
                        <span class="text-[10px] text-emerald-400">✓ ACCURACY ±2m</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Arakan Barangay Jurisdiction (28 Total)</label>
                            <select
                                x-model="selectedBarangay"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                            >
                                @foreach ($arakanBarangays as $bgy)
                                    <option value="{{ $bgy }}">Brgy. {{ $bgy }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Purok / Sitio</label>
                            <input
                                type="text"
                                x-model="selectedPurok"
                                placeholder="e.g. Purok 3 or Sitio Kilometer 12"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Specific Landmark Description</label>
                        <input
                            type="text"
                            x-model="landmark"
                            placeholder="e.g. Near Greenfield Public Market, beside Purok 3 Waiting Shed"
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                        >
                    </div>
                </div>

                <!-- ==================== STEP 3: HAZARD SEVERITY & TOGGLES ==================== -->
                <div x-show="wizardStep === 3" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1.5">Hazard Severity Level</label>
                        <div class="grid grid-cols-4 gap-2">
                            <button
                                type="button"
                                @click="severityLevel = 'Low'"
                                :class="severityLevel === 'Low' ? 'border-emerald-500 bg-emerald-950/70 text-emerald-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8]'"
                                class="p-2 rounded-xl border text-xs text-center transition"
                            >
                                Low
                            </button>
                            <button
                                type="button"
                                @click="severityLevel = 'Moderate'"
                                :class="severityLevel === 'Moderate' ? 'border-amber-500 bg-amber-950/70 text-amber-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8]'"
                                class="p-2 rounded-xl border text-xs text-center transition"
                            >
                                Moderate
                            </button>
                            <button
                                type="button"
                                @click="severityLevel = 'High'"
                                :class="severityLevel === 'High' ? 'border-red-500 bg-red-950/70 text-red-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8]'"
                                class="p-2 rounded-xl border text-xs text-center transition"
                            >
                                High
                            </button>
                            <button
                                type="button"
                                @click="severityLevel = 'Critical'"
                                :class="severityLevel === 'Critical' ? 'border-red-600 bg-red-900 text-white font-bold ring-2 ring-red-500' : 'border-[#163b33] bg-[#102621] text-[#94a3b8]'"
                                class="p-2 rounded-xl border text-xs text-center transition"
                            >
                                Critical / SOS
                            </button>
                        </div>
                    </div>

                    <!-- Details Textarea -->
                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Details &amp; Current Situation</label>
                        <textarea
                            rows="3"
                            x-model="incidentDescription"
                            placeholder="Describe what is happening on-scene, number of affected households, or specific assistance needed..."
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] p-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <!-- Critical Toggles -->
                    <div class="space-y-2 pt-1">
                        <label class="flex items-center justify-between p-3 rounded-xl border border-[#163b33] bg-[#102621] cursor-pointer hover:border-amber-500/40 transition">
                            <span class="text-xs font-semibold text-[#F5FFFC]">Road completely impassable for vehicles</span>
                            <input type="checkbox" x-model="roadImpassable" class="h-4 w-4 rounded border-[#163b33] text-amber-500 focus:ring-0">
                        </label>
                        <label class="flex items-center justify-between p-3 rounded-xl border border-[#163b33] bg-[#102621] cursor-pointer hover:border-red-500/40 transition">
                            <span class="text-xs font-semibold text-[#F5FFFC]">Casualties or injured persons detected</span>
                            <input type="checkbox" x-model="casualtiesDetected" class="h-4 w-4 rounded border-[#163b33] text-red-500 focus:ring-0">
                        </label>
                        <label class="flex items-center justify-between p-3 rounded-xl border border-[#163b33] bg-[#102621] cursor-pointer hover:border-amber-500/40 transition">
                            <span class="text-xs font-semibold text-[#F5FFFC]">Live electrical wires / fallen cables detected</span>
                            <input type="checkbox" x-model="liveWiresDetected" class="h-4 w-4 rounded border-[#163b33] text-amber-500 focus:ring-0">
                        </label>
                    </div>
                </div>

                <!-- ==================== STEP 4: PHOTO PROOF & SUBMIT ==================== -->
                <div x-show="wizardStep === 4" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Attach Photo Proof (Camera or File)</label>
                        <div class="relative border-2 border-dashed border-[#163b33] rounded-2xl p-6 text-center hover:border-emerald-500/50 transition bg-[#102621]/40">
                            <input
                                type="file"
                                accept="image/*"
                                @change="previewPhoto($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            >
                            <template x-if="!photoPreview">
                                <div class="space-y-2">
                                    <div class="text-3xl">📷</div>
                                    <div class="text-xs text-[#D9FFF5] font-semibold">Tap to capture or upload camera image</div>
                                    <p class="text-[10px] text-[#7DB5A5] font-mono">Automatic watermark with Arakan GPS &amp; Timestamp</p>
                                </div>
                            </template>
                            <template x-if="photoPreview">
                                <div class="space-y-2">
                                    <img :src="photoPreview" class="mx-auto max-h-40 rounded-xl border border-emerald-500/40 object-cover" alt="Attached photo">
                                    <p class="text-[10px] font-mono text-emerald-400">✓ Photo attached &amp; compressed</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Summary confirmation card -->
                    <div class="p-4 rounded-xl border border-emerald-500/30 bg-emerald-950/20 text-xs space-y-1.5 font-mono">
                        <div class="flex justify-between text-[#7DB5A5]">
                            <span>REPORTER:</span>
                            <span class="text-[#F5FFFC]">Juan Dela Cruz (+63 917 555 4321)</span>
                        </div>
                        <div class="flex justify-between text-[#7DB5A5]">
                            <span>LOCATION:</span>
                            <span class="text-emerald-400">Brgy. <span x-text="selectedBarangay"></span> (<span x-text="selectedPurok"></span>)</span>
                        </div>
                        <div class="flex justify-between text-[#7DB5A5]">
                            <span>COORDINATES:</span>
                            <span class="text-cyan-300" x-text="gpsCoordinates"></span>
                        </div>
                        <div class="flex justify-between text-[#7DB5A5]">
                            <span>SEVERITY:</span>
                            <span class="text-amber-400 font-bold" x-text="severityLevel"></span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="pt-4 border-t border-[#163b33] flex items-center justify-between">
                    <div>
                        <button
                            type="button"
                            x-show="wizardStep > 1"
                            @click="wizardStep--"
                            class="rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2 text-xs text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                        >
                            ← Back
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="reportWizardOpen = false"
                            class="rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2 text-xs text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            x-show="wizardStep < 4"
                            @click="wizardStep++"
                            class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-[#051613] hover:bg-emerald-500 transition shadow-md"
                        >
                            Next Step →
                        </button>

                        <button
                            type="button"
                            x-show="wizardStep === 4"
                            @click="submitReport()"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-xs font-extrabold text-[#051613] hover:bg-emerald-400 transition shadow-[0_0_20px_rgba(16,185,129,0.35)]"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9 22 2z"/>
                            </svg>
                            <span x-text="submitting ? 'Transmitting to MDRRMO...' : 'Submit Emergency Report to MDRRMO Arakan Desk'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-command-shell>
@endsection