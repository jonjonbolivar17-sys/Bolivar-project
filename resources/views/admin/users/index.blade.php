@extends('layouts.dashboard')

@section('title', 'User Management & Access Control · MDRRMO Arakan')

@section('content')
<x-command-shell active-role="admin" active-route="users" title="User Management & RBAC Console · MDRRMO Arakan">
    @php
        $arakanBarangays = $barangays ?? [
            'Poblacion', 'Greenfield', 'Meocan', 'Doroluman', 'Gambodes', 'Badiangon',
            'Datu Ladayon', 'Kabalantian', 'Santo Niño', 'San Miguel', 'Maria Caridad',
            'Kulaman Valley', 'Malibatuan', 'Katipunan', 'Napalico', 'Salasang', 'Dallag',
            'Anapolon', 'Binoongan', 'Ganatan', 'Kinawayan', 'Libertad', 'Makalangot',
            'Naje', 'New Rosary', 'Sumalili', 'Tumanding', 'Lanao Kuran'
        ];

        // Seeded directory with official Arakan personnel & citizens
        $directory = [
            [
                'id' => 1,
                'name' => 'Insp. Rodrigo Almodovar',
                'title' => 'MDRRMO Incident Commander',
                'email' => 'r.almodovar@arakan.gov.ph',
                'phone' => '+63 917 521 8801',
                'role' => 'admin',
                'badge' => 'CMD-001',
                'jurisdiction' => 'MDRRMO HQ Arakan EOC',
                'barangay' => 'Poblacion',
                'auth_type' => '2FA Active YubiKey',
                'last_active' => 'Active Now · Terminal 1',
                'state' => 'On Duty - Available',
                'state_type' => 'operational',
            ],
            [
                'id' => 2,
                'name' => 'Officer Mark Alvarez',
                'title' => 'Team Alpha Lead / Paramedic',
                'email' => 'm.alvarez@mdrrmo.arakan.ph',
                'phone' => '+63 919 443 1202',
                'role' => 'responder',
                'badge' => 'ALPHA-LEAD',
                'jurisdiction' => 'Brgy. Greenfield Station 04 Rapid Ambulance Unit 1',
                'barangay' => 'Greenfield',
                'auth_type' => '2FA Active SMS App',
                'last_active' => '4m ago · VHF Handheld',
                'state' => 'En Route / Dispatched',
                'state_type' => 'warning',
            ],
            [
                'id' => 3,
                'name' => 'Sgt. Elena Bautista',
                'title' => 'BFP Fire & Extraction Lead',
                'email' => 'e.bautista@bfp.arakan.ph',
                'phone' => '+63 998 123 4567',
                'role' => 'responder',
                'badge' => 'BRAVO-PUMPER',
                'jurisdiction' => 'BFP Arakan Substation Poblacion',
                'barangay' => 'Poblacion',
                'auth_type' => '2FA Active YubiKey',
                'last_active' => '12m ago · Substation Console',
                'state' => 'On Duty - Available',
                'state_type' => 'operational',
            ],
            [
                'id' => 4,
                'name' => 'Tanod Danilo Ramos',
                'title' => 'Quick Reaction Tanod Patrol',
                'email' => 'd.ramos.tanod@meocan.net',
                'phone' => '+63 928 776 5431',
                'role' => 'tanod',
                'badge' => 'MEOCAN-07',
                'jurisdiction' => 'Brgy. Meocan Landslide Monitoring Post',
                'barangay' => 'Meocan',
                'auth_type' => 'PIN Auth Verified',
                'last_active' => '22m ago · Checkpoint Radio',
                'state' => 'On Patrol',
                'state_type' => 'patrol',
            ],
            [
                'id' => 5,
                'name' => 'Juan Dela Cruz',
                'title' => 'Verified Citizen / Purok Leader',
                'email' => 'j.delacruz@resident.arakan.ph',
                'phone' => '+63 917 555 4321',
                'role' => 'citizen',
                'badge' => 'PhilSys #9812-4410',
                'jurisdiction' => 'Brgy. Greenfield, Purok 3',
                'barangay' => 'Greenfield',
                'auth_type' => 'PIN Auth Verified',
                'last_active' => '8m ago · Resident Mobile App',
                'state' => 'Verified Resident',
                'state_type' => 'resident',
            ],
            [
                'id' => 6,
                'name' => 'Althea Mae Dimaculangan',
                'title' => 'Barangay Health Worker / Resident',
                'email' => 'a.dimaculangan@resident.arakan.ph',
                'phone' => '+63 930 119 8834',
                'role' => 'citizen',
                'badge' => 'Barangay Endorsed',
                'jurisdiction' => 'Brgy. Gambodes Sector 2',
                'barangay' => 'Gambodes',
                'auth_type' => 'PIN Auth Verified',
                'last_active' => '1h ago · Citizen Portal',
                'state' => 'Verified Resident',
                'state_type' => 'resident',
            ],
            [
                'id' => 7,
                'name' => 'Engr. Carlo Mendoza',
                'title' => 'LGU Municipal Engineering / Hazard Mapper',
                'email' => 'c.mendoza@arakan.gov.ph',
                'phone' => '+63 917 889 0041',
                'role' => 'admin',
                'badge' => 'ENG-003',
                'jurisdiction' => 'Arakan LGU Engineering Office',
                'barangay' => 'Poblacion',
                'auth_type' => '2FA Active YubiKey',
                'last_active' => '35m ago · GIS Terminal',
                'state' => 'On Duty - Available',
                'state_type' => 'operational',
            ],
            [
                'id' => 8,
                'name' => 'Nurse Katrina Beltran',
                'title' => 'RHU Arakan Emergency Triage',
                'email' => 'k.beltran@rhua.arakan.gov.ph',
                'phone' => '+63 921 665 9912',
                'role' => 'responder',
                'badge' => 'MED-ALPHA',
                'jurisdiction' => 'Arakan RHU Mobile Clinic Greenfield',
                'barangay' => 'Greenfield',
                'auth_type' => '2FA Active SMS App',
                'last_active' => '18m ago · Field Tablet',
                'state' => 'On Duty - Available',
                'state_type' => 'operational',
            ],
            [
                'id' => 9,
                'name' => 'Tanod Roberto Balingit',
                'title' => 'River Crossing Watchman',
                'email' => 'r.balingit@doroluman.net',
                'phone' => '+63 938 102 3341',
                'role' => 'tanod',
                'badge' => 'DORO-02',
                'jurisdiction' => 'Brgy. Doroluman River Crossing Post',
                'barangay' => 'Doroluman',
                'auth_type' => 'PIN Auth Verified',
                'last_active' => '50m ago · Handheld Radio',
                'state' => 'On Patrol',
                'state_type' => 'patrol',
            ],
            [
                'id' => 10,
                'name' => 'Michelle Tan',
                'title' => 'Citizen Applicant',
                'email' => 'm.tan.pending@gmail.com',
                'phone' => '+63 945 991 2233',
                'role' => 'pending',
                'badge' => 'Pending Clearance',
                'jurisdiction' => 'Brgy. Kabalantian Purok 1',
                'barangay' => 'Kabalantian',
                'auth_type' => 'Pending Verification',
                'last_active' => '2h ago · Web Self-Reg',
                'state' => 'Under Review',
                'state_type' => 'critical',
            ],
        ];
    @endphp

    <div
        x-data="{
            searchQuery: '{{ request('search', '') }}',
            selectedBarangay: '{{ request('barangay', '') }}',
            selectedRole: '{{ request('role', 'all') }}',
            provisionModalOpen: false,
            actionModalOpen: false,
            selectedUser: null,
            openUserAction(user) {
                this.selectedUser = user;
                this.actionModalOpen = true;
            },
            resetForm() {
                this.searchQuery = '';
                this.selectedBarangay = '';
                this.selectedRole = 'all';
            }
        }"
        class="space-y-6"
    >
        <!-- Top Title & Quick Actions Row -->
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between border-b border-[#163b33] pb-5">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-950/40 px-3 py-1 text-[10px] font-mono font-semibold uppercase tracking-widest text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    ARAKAN MUNICIPAL RBAC CONSOLE · LGU REGION XII
                </div>
                <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight text-[#F5FFFC]">
                    User Management &amp; Access Control
                </h1>
                <p class="mt-1 text-xs md:text-sm text-[#7DB5A5]">
                    Manage access credentials, VHF radio call signs, and sector jurisdictions across all 28 Barangays of Arakan, Cotabato.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    @click="alert('Exporting encrypted CSV audit log of all 1,842 Arakan accounts...')"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#163b33] bg-[#0d1f1b] px-3.5 py-2 text-xs font-semibold text-[#D9FFF5] hover:border-emerald-500/40 hover:bg-[#102621] transition"
                >
                    <svg class="h-4 w-4 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15"/>
                    </svg>
                    Export User Audit (CSV)
                </button>

                <button
                    type="button"
                    @click="alert('Bulk Role Re-assignment console loaded.')"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#163b33] bg-[#0d1f1b] px-3.5 py-2 text-xs font-semibold text-[#D9FFF5] hover:border-emerald-500/40 hover:bg-[#102621] transition"
                >
                    <svg class="h-4 w-4 text-[#7DB5A5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Bulk Role Assignment
                </button>

                <button
                    type="button"
                    @click="provisionModalOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-emerald-400 bg-emerald-600 px-4 py-2 text-xs font-bold text-[#051613] shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:bg-emerald-500 transition"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    + Provision New Account
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/50 bg-emerald-950/60 p-3.5 text-xs text-emerald-300 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="font-mono text-[10px] text-emerald-400">STATUS: PROVISIONED</span>
            </div>
        @endif

        <!-- ==================== A. EXECUTIVE KPI TELEMETRY ROW (4 CARDS) ==================== -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Card 1: Total System Users -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-emerald-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">TOTAL SYSTEM USERS</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-950/50 text-emerald-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-[#F5FFFC]">1,842</span>
                    <span class="rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-emerald-400">+48 this week</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">Registered residents across 28 Arakan barangays</p>
            </div>

            <!-- Card 2: Active Field Responders -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-amber-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">ACTIVE FIELD RESPONDERS</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-500/20 bg-amber-950/50 text-amber-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-[#F5FFFC]">32 Units</span>
                    <span class="rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-emerald-400">28 On-Duty</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">8 Active Patrol Units · BFP &amp; Tanod Mesh linked</p>
            </div>

            <!-- Card 3: Verified Citizen Reporters -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-cyan-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">VERIFIED CITIZEN REPORTERS</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-cyan-500/20 bg-cyan-950/50 text-cyan-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-[#F5FFFC]">1,786</span>
                    <span class="rounded bg-cyan-500/20 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-cyan-400">97.4% PhilSys</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">18 Pending clearance · Barangay endorsed</p>
            </div>

            <!-- Card 4: MDRRMO Command Staff -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-lg transition hover:border-emerald-500/30">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#7DB5A5]">MDRRMO COMMAND STAFF</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-950/50 text-emerald-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="font-mono text-3xl font-extrabold tracking-tight text-[#F5FFFC]">24 Officers</span>
                    <span class="rounded bg-emerald-500/20 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-emerald-400">100% 2FA PIN</span>
                </div>
                <p class="mt-2 text-xs text-[#7DB5A5]">Hardware Key (YubiKey) verified command access</p>
            </div>
        </div>

        <!-- ==================== B. FILTER & SEARCH CONTROLS ==================== -->
        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-4 space-y-4 shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <!-- Search input -->
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#7DB5A5]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </span>
                    <input
                        x-model="searchQuery"
                        type="text"
                        placeholder="Search by name, badge ID (CMD-001, ALPHA-LEAD), +63 mobile, or @arakan.gov.ph email..."
                        class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 pl-10 pr-4 text-xs md:text-sm text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/30"
                    >
                </div>

                <!-- 28 Arakan Barangays filter dropdown -->
                <div class="w-full lg:w-72">
                    <select
                        x-model="selectedBarangay"
                        class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                    >
                        <option value="">All 28 Barangays of Arakan</option>
                        @foreach ($arakanBarangays as $bgy)
                            <option value="{{ $bgy }}">Brgy. {{ $bgy }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset button -->
                <button
                    type="button"
                    @click="resetForm()"
                    x-show="searchQuery || selectedBarangay || selectedRole !== 'all'"
                    class="rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2.5 text-xs text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                >
                    Reset Filters
                </button>
            </div>

            <!-- Filter Pills Row -->
            <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-[#163b33]/60">
                <span class="text-[10px] font-mono uppercase tracking-wider text-[#7DB5A5] mr-1">ROLE FILTER:</span>

                <button
                    type="button"
                    @click="selectedRole = 'all'"
                    :class="selectedRole === 'all' ? 'border-emerald-500 bg-emerald-950/60 text-emerald-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                    class="rounded-lg border px-3 py-1.5 text-xs transition"
                >
                    All Accounts <span class="font-mono text-[10px] opacity-80">(1,842)</span>
                </button>

                <button
                    type="button"
                    @click="selectedRole = 'admin'"
                    :class="selectedRole === 'admin' ? 'border-emerald-500 bg-emerald-950/60 text-emerald-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                    class="rounded-lg border px-3 py-1.5 text-xs transition"
                >
                    Command Admin <span class="font-mono text-[10px] opacity-80">(24)</span>
                </button>

                <button
                    type="button"
                    @click="selectedRole = 'responder'"
                    :class="selectedRole === 'responder' ? 'border-amber-500 bg-amber-950/60 text-amber-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                    class="rounded-lg border px-3 py-1.5 text-xs transition"
                >
                    Field Responders / Tanod <span class="font-mono text-[10px] opacity-80">(32)</span>
                </button>

                <button
                    type="button"
                    @click="selectedRole = 'citizen'"
                    :class="selectedRole === 'citizen' ? 'border-cyan-500 bg-cyan-950/60 text-cyan-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                    class="rounded-lg border px-3 py-1.5 text-xs transition"
                >
                    Citizen Reporters <span class="font-mono text-[10px] opacity-80">(1,786)</span>
                </button>

                <button
                    type="button"
                    @click="selectedRole = 'pending'"
                    :class="selectedRole === 'pending' ? 'border-red-500 bg-red-950/60 text-red-300 font-bold' : 'border-[#163b33] bg-[#102621] text-[#94a3b8] hover:text-[#F5FFFC]'"
                    class="rounded-lg border px-3 py-1.5 text-xs transition"
                >
                    Pending Verification <span class="font-mono text-[10px] opacity-80">(18)</span>
                </button>
            </div>
        </div>

        <!-- ==================== C. PERSONNEL & CITIZEN DIRECTORY DATA TABLE ==================== -->
        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#163b33] bg-[#102621]/40">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#F5FFFC]">Directory Roster &amp; Operational Postings</span>
                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-mono text-emerald-400">Live Arakan Registry</span>
                </div>
                <div class="text-[11px] font-mono text-[#7DB5A5]">
                    Showing verified records for <strong class="text-[#F5FFFC]">MDRRMO Arakan</strong>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#163b33] text-[10px] font-mono uppercase tracking-wider text-[#7DB5A5] bg-[#091b17]">
                            <th class="py-3 px-4 font-semibold">User / Officer Details</th>
                            <th class="py-3 px-4 font-semibold">Role &amp; Access Level</th>
                            <th class="py-3 px-4 font-semibold">Jurisdiction &amp; Post</th>
                            <th class="py-3 px-4 font-semibold">Security &amp; Auth Status</th>
                            <th class="py-3 px-4 font-semibold">Duty / Operational State</th>
                            <th class="py-3 px-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#163b33]/60">
                        @foreach ($directory as $person)
                            @php
                                $roleMatches = match ($person['role']) {
                                    'admin' => "selectedRole === 'all' || selectedRole === 'admin'",
                                    'responder', 'tanod' => "selectedRole === 'all' || selectedRole === 'responder'",
                                    'citizen' => "selectedRole === 'all' || selectedRole === 'citizen'",
                                    'pending' => "selectedRole === 'all' || selectedRole === 'pending'",
                                    default => "true"
                                };
                            @endphp
                            <tr
                                x-show="({{ $roleMatches }}) && (!selectedBarangay || '{{ $person['barangay'] }}' === selectedBarangay) && (!searchQuery || '{{ strtolower($person['name'] . ' ' . $person['email'] . ' ' . $person['badge'] . ' ' . $person['jurisdiction'] . ' ' . $person['phone']) }}'.includes(searchQuery.toLowerCase()))"
                                class="hover:bg-[#102621]/80 transition duration-150"
                            >
                                <!-- Column 1: User / Officer Details -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] font-mono font-bold text-xs text-emerald-400">
                                            {{ strtoupper(substr($person['name'], 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-[#F5FFFC] text-sm">{{ $person['name'] }}</div>
                                            <div class="text-[11px] text-[#7DB5A5]">{{ $person['title'] }}</div>
                                            <div class="mt-0.5 flex items-center gap-2 font-mono text-[10px] text-[#94a3b8]">
                                                <span>{{ $person['phone'] }}</span>
                                                <span class="text-[#163b33]">·</span>
                                                <span class="text-emerald-400/90">{{ $person['email'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 2: Role & Access Level -->
                                <td class="py-3.5 px-4">
                                    @if ($person['role'] === 'admin')
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/40 bg-emerald-950/60 px-2 py-1 text-[11px] font-semibold text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                            Command Admin
                                        </div>
                                        <div class="mt-1 font-mono text-[10px] text-emerald-400 font-bold">
                                            Badge: {{ $person['badge'] }}
                                        </div>
                                    @elseif ($person['role'] === 'responder')
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-amber-500/40 bg-amber-950/60 px-2 py-1 text-[11px] font-semibold text-amber-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                            Field Responder
                                        </div>
                                        <div class="mt-1 font-mono text-[10px] text-amber-300 font-bold">
                                            Radio: {{ $person['badge'] }}
                                        </div>
                                    @elseif ($person['role'] === 'tanod')
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-teal-500/40 bg-teal-950/60 px-2 py-1 text-[11px] font-semibold text-teal-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-teal-400"></span>
                                            Field Tanod
                                        </div>
                                        <div class="mt-1 font-mono text-[10px] text-teal-300 font-bold">
                                            Call: {{ $person['badge'] }}
                                        </div>
                                    @elseif ($person['role'] === 'citizen')
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-500/40 bg-cyan-950/60 px-2 py-1 text-[11px] font-semibold text-cyan-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                                            Citizen Reporter
                                        </div>
                                        <div class="mt-1 font-mono text-[10px] text-cyan-300">
                                            {{ $person['badge'] }}
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/40 bg-red-950/60 px-2 py-1 text-[11px] font-semibold text-red-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                                            Pending Clearance
                                        </div>
                                        <div class="mt-1 font-mono text-[10px] text-red-400">
                                            Review Required
                                        </div>
                                    @endif
                                </td>

                                <!-- Column 3: Jurisdiction & Post -->
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-[#F5FFFC]">{{ $person['jurisdiction'] }}</div>
                                    <div class="mt-0.5 flex items-center gap-1 font-mono text-[10px] text-emerald-400">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <span>Brgy. {{ $person['barangay'] }}, Arakan</span>
                                    </div>
                                </td>

                                <!-- Column 4: Security & Auth Status -->
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    <div class="flex items-center gap-1.5">
                                        @if (str_contains($person['auth_type'], 'YubiKey'))
                                            <span class="text-emerald-400 font-bold">🔐 {{ $person['auth_type'] }}</span>
                                        @elseif (str_contains($person['auth_type'], 'SMS'))
                                            <span class="text-amber-300">📱 {{ $person['auth_type'] }}</span>
                                        @elseif (str_contains($person['auth_type'], 'PIN'))
                                            <span class="text-cyan-300">🔑 {{ $person['auth_type'] }}</span>
                                        @else
                                            <span class="text-red-400">⏳ {{ $person['auth_type'] }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-0.5 text-[10px] text-[#7DB5A5]">{{ $person['last_active'] }}</div>
                                </td>

                                <!-- Column 5: Duty / Operational State -->
                                <td class="py-3.5 px-4">
                                    @if ($person['state_type'] === 'operational')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/50 bg-emerald-950/70 px-2.5 py-0.5 text-[10px] font-mono font-bold text-emerald-300">
                                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            {{ $person['state'] }}
                                        </span>
                                    @elseif ($person['state_type'] === 'warning')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/50 bg-amber-950/70 px-2.5 py-0.5 text-[10px] font-mono font-bold text-amber-300">
                                            <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                                            {{ $person['state'] }}
                                        </span>
                                    @elseif ($person['state_type'] === 'patrol')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-teal-500/50 bg-teal-950/70 px-2.5 py-0.5 text-[10px] font-mono font-bold text-teal-300">
                                            <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                                            {{ $person['state'] }}
                                        </span>
                                    @elseif ($person['state_type'] === 'resident')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-500/50 bg-cyan-950/70 px-2.5 py-0.5 text-[10px] font-mono font-bold text-cyan-300">
                                            <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                                            {{ $person['state'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/50 bg-red-950/70 px-2.5 py-0.5 text-[10px] font-mono font-bold text-red-300">
                                            <span class="h-2 w-2 rounded-full bg-red-400 animate-ping"></span>
                                            {{ $person['state'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Column 6: Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click="openUserAction({{ json_encode($person) }})"
                                            class="rounded-lg border border-[#163b33] bg-[#102621] px-2.5 py-1 text-[11px] font-semibold text-[#D9FFF5] hover:border-emerald-500/40 hover:text-emerald-300 transition"
                                            title="Manage privileges"
                                        >
                                            Edit Privileges
                                        </button>
                                        <button
                                            type="button"
                                            @click="alert('Incident Crypto PIN reset link transmitted to {{ $person['name'] }}.')"
                                            class="rounded-lg border border-[#163b33] bg-[#102621] px-2 py-1 text-[11px] font-mono text-[#7DB5A5] hover:border-amber-500/40 hover:text-amber-300 transition"
                                            title="Reset Incident PIN"
                                        >
                                            Reset PIN
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Pagination & Count Footer -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3.5 border-t border-[#163b33] bg-[#091b17] text-xs text-[#7DB5A5]">
                <div class="font-mono text-[11px]">
                    Showing <strong class="text-[#F5FFFC]">10 of 1,842</strong> active accounts across <strong class="text-emerald-400">28 Barangays</strong>
                </div>
                <div class="flex items-center gap-1.5 font-mono text-xs">
                    <button class="px-2.5 py-1 rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:text-[#F5FFFC]">Previous</button>
                    <button class="px-2.5 py-1 rounded-lg bg-emerald-600 text-[#051613] font-bold">1</button>
                    <button class="px-2.5 py-1 rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:text-[#F5FFFC]">2</button>
                    <button class="px-2.5 py-1 rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:text-[#F5FFFC]">3</button>
                    <span class="px-1 text-[#7DB5A5]">...</span>
                    <button class="px-2.5 py-1 rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:text-[#F5FFFC]">184</button>
                    <button class="px-2.5 py-1 rounded-lg border border-[#163b33] bg-[#102621] text-[#7DB5A5] hover:text-[#F5FFFC]">Next</button>
                </div>
            </div>
        </div>

        <!-- ==================== D. SECURITY & PROVISIONING GRID (BOTTOM SECTION) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-4">
            <!-- 1. RBAC Role Permissions Matrix -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#163b33]">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-950/50 text-emerald-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#F5FFFC]">RBAC Role Permissions Matrix</h3>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">MDRRMO ARAKAN ENCRYPTION &amp; DISPATCH POLICY</p>
                        </div>
                    </div>
                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[9px] font-mono text-emerald-400 font-bold">SEC-1 AUDITED</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#163b33] text-[10px] font-mono uppercase text-[#7DB5A5]">
                                <th class="pb-2 font-semibold">Functional Capability</th>
                                <th class="pb-2 text-center font-semibold text-emerald-400">Command Admin</th>
                                <th class="pb-2 text-center font-semibold text-amber-400">Responder / Tanod</th>
                                <th class="pb-2 text-center font-semibold text-cyan-400">Citizen Reporter</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#163b33]/60 text-[11px]">
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">Full Incident Dispatch &amp; Override</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Full</td>
                                <td class="py-2.5 text-center text-[#7DB5A5]">On-Scene Only</td>
                                <td class="py-2.5 text-center text-[#7DB5A5]">—</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">Tactical GIS Hazard Overlays &amp; Weather</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Full</td>
                                <td class="py-2.5 text-center text-amber-400">✓ Sector Map</td>
                                <td class="py-2.5 text-center text-cyan-400">Public Advisory</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">VHF Radio Mesh &amp; Repeater Bridge</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Broadcast</td>
                                <td class="py-2.5 text-center text-amber-400">✓ Two-Way Link</td>
                                <td class="py-2.5 text-center text-[#7DB5A5]">—</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">Geolocated Photo Evidence Clearance</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Approve/Audit</td>
                                <td class="py-2.5 text-center text-amber-400">✓ Upload Proof</td>
                                <td class="py-2.5 text-center text-cyan-400">Initial Submission</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">Emergency Shelter &amp; Evacuation Transmit</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Broadcast All</td>
                                <td class="py-2.5 text-center text-amber-400">✓ Logistics Sync</td>
                                <td class="py-2.5 text-center text-cyan-400">Route &amp; Cap. View</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-[#F5FFFC]">Hardware Key (YubiKey) &amp; PIN Management</td>
                                <td class="py-2.5 text-center text-emerald-400 font-bold">✓ Root Admin</td>
                                <td class="py-2.5 text-center text-[#7DB5A5]">—</td>
                                <td class="py-2.5 text-center text-[#7DB5A5]">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Quick Provisioning Console -->
            <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#163b33]">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-950/50 text-emerald-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#F5FFFC]">Quick Provisioning Console</h3>
                            <p class="text-[10px] font-mono text-[#7DB5A5]">ISSUE FIELD CREDENTIALS &amp; RADIO CALL SIGNS</p>
                        </div>
                    </div>
                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[9px] font-mono text-emerald-400 font-bold">NODE: DRRMO-ARK-01</span>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Full Personnel Name</label>
                            <input
                                type="text"
                                name="name"
                                required
                                placeholder="e.g. Officer Juan Perez"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Official LGU / MDRRMO Email</label>
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="e.g. j.perez@mdrrmo.arakan.ph"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Role &amp; Privilege</label>
                            <select
                                name="role"
                                required
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-2.5 text-xs text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                            >
                                <option value="staff">Field Staff / Responder</option>
                                <option value="admin">Command Center Admin</option>
                                <option value="reporter">Citizen Reporter</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Arakan Barangay Jurisdiction</label>
                            <select
                                name="barangay"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-2.5 text-xs text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                            >
                                @foreach ($arakanBarangays as $bgy)
                                    <option value="{{ $bgy }}">Brgy. {{ $bgy }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Radio ID / Badge</label>
                            <input
                                type="text"
                                placeholder="e.g. ALPHA-03"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none font-mono"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Initial Password / Master PIN</label>
                            <input
                                type="password"
                                name="password"
                                required
                                value="ArakanSecure2026!"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] focus:border-emerald-500 focus:outline-none font-mono"
                            >
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">Confirm Master PIN</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                                value="ArakanSecure2026!"
                                class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2 px-3 text-xs text-[#F5FFFC] focus:border-emerald-500 focus:outline-none font-mono"
                            >
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-[10px] font-mono text-[#7DB5A5]">Generates 2FA hardware enrollment code automatically</span>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-[#051613] hover:bg-emerald-500 transition shadow-md"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            </svg>
                            Issue &amp; Provision Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================== USER ACTIONS MODAL (SLIDE-OVER / DIALOG) ==================== -->
        <div
            x-show="actionModalOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.outside="actionModalOpen = false"
                class="w-full max-w-lg rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-6 shadow-2xl space-y-4"
            >
                <div class="flex items-center justify-between border-b border-[#163b33] pb-3">
                    <div>
                        <h3 class="text-base font-bold text-[#F5FFFC]" x-text="'Privilege Control: ' + (selectedUser ? selectedUser.name : '')"></h3>
                        <p class="text-xs font-mono text-emerald-400" x-text="selectedUser ? selectedUser.jurisdiction : ''"></p>
                    </div>
                    <button @click="actionModalOpen = false" class="text-sm font-mono text-[#7DB5A5] hover:text-[#F5FFFC]">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl border border-[#163b33] bg-[#102621]">
                        <span class="text-[10px] font-mono text-[#7DB5A5] uppercase">Assigned Barangay Jurisdiction</span>
                        <select class="mt-1 w-full rounded-lg border border-[#163b33] bg-[#0d1f1b] py-2 px-3 text-xs text-[#F5FFFC]">
                            @foreach ($arakanBarangays as $bgy)
                                <option value="{{ $bgy }}">Brgy. {{ $bgy }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 rounded-xl border border-[#163b33] bg-[#102621]">
                        <span class="text-[10px] font-mono text-[#7DB5A5] uppercase">Tactical Role Elevation</span>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-[#163b33] bg-[#0d1f1b] cursor-pointer">
                                <input type="radio" name="modal_role" value="admin">
                                <span class="font-bold text-emerald-400">Admin</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-[#163b33] bg-[#0d1f1b] cursor-pointer">
                                <input type="radio" name="modal_role" value="responder" checked>
                                <span class="font-bold text-amber-300">Responder</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-[#163b33] bg-[#0d1f1b] cursor-pointer">
                                <input type="radio" name="modal_role" value="citizen">
                                <span class="font-bold text-cyan-300">Citizen</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <button
                            type="button"
                            @click="alert('Cryptographic incident PIN rotated.'); actionModalOpen = false"
                            class="rounded-xl border border-amber-500/40 bg-amber-950/40 py-2.5 px-3 text-xs font-semibold text-amber-300 hover:bg-amber-900/40 transition"
                        >
                            Rotate Crypto PIN
                        </button>
                        <button
                            type="button"
                            @click="alert('Hardware Key (YubiKey) authorization validated.'); actionModalOpen = false"
                            class="rounded-xl border border-emerald-500/40 bg-emerald-950/40 py-2.5 px-3 text-xs font-semibold text-emerald-300 hover:bg-emerald-900/40 transition"
                        >
                            Audit 2FA Key
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#163b33] flex justify-end gap-2">
                    <button @click="actionModalOpen = false" class="rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2 text-xs text-[#7DB5A5] hover:text-[#F5FFFC]">Cancel</button>
                    <button @click="alert('Privilege updates saved to Arakan MDRRMO directory.'); actionModalOpen = false" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-[#051613] hover:bg-emerald-500">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</x-command-shell>
@endsection