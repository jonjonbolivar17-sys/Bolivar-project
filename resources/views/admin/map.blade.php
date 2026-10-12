@extends('layouts.dashboard')

@section('title', 'Command GIS Map · AkoHanda')

@section('content')
    <x-command-shell active-role="admin" active-route="admin.map" title="GIS Tactical Map · MDRRMO Arakan">
        <div class="space-y-6 p-4 md:p-6">
            <div class="rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-2xl">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-950/40 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-emerald-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            Tactical GIS View
                        </div>
                        <h1 class="mt-3 text-3xl font-black tracking-tight text-[#F5FFFC]">Municipal CAD Radar</h1>
                        <p class="mt-1 text-sm text-[#7DB5A5]">Live responder telemetry, geofences, and road corridors across Arakan.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" class="rounded-full border border-[#163b33] bg-[#102621] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94a3b8]">Satellite</button>
                        <button type="button" class="rounded-full border border-emerald-500/30 bg-emerald-950/40 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-emerald-300">CCTV Mesh</button>
                        <button type="button" class="rounded-full border border-[#163b33] bg-[#102621] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94a3b8]">Heatmap</button>
                        <button type="button" class="rounded-full border border-[#163b33] bg-[#102621] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94a3b8]">Geofence</button>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.8fr)_360px]">
                <section class="relative overflow-hidden rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-2xl">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(16,185,129,0.12),transparent_28%),radial-gradient(circle_at_75%_25%,rgba(59,130,246,0.12),transparent_32%),linear-gradient(135deg,#0d1f1b_0%,#102621_100%)]"></div>
                    <div class="relative h-[620px] overflow-hidden rounded-2xl border border-[#163b33] bg-[#081d1a]">
                        <div class="absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(20,83,45,0.15) 1px, transparent 1px), linear-gradient(90deg, rgba(20,83,45,0.15) 1px, transparent 1px); background-size: 56px 56px;"></div>
                        <div class="absolute left-10 top-12 h-40 w-40 rounded-full border border-emerald-500/30 bg-emerald-500/10"></div>
                        <div class="absolute right-16 top-16 h-52 w-52 rounded-full border border-cyan-500/25 bg-cyan-500/5"></div>
                        <div class="absolute bottom-16 left-16 h-40 w-40 rounded-full border border-amber-500/25 bg-amber-500/5"></div>

                        <div class="absolute left-10 top-6 rounded-xl border border-emerald-500/30 bg-[#071b18]/80 px-3 py-2 text-[10px] font-medium uppercase tracking-[0.2em] text-emerald-300">ARAKAN - SECTOR 2</div>
                        <div class="absolute left-1/2 top-1/3 h-24 w-24 -translate-x-1/2 rounded-full border-2 border-dashed border-amber-500/50 bg-amber-500/5"></div>

                        <div class="absolute left-[18%] top-[35%] flex -translate-x-1/2 -translate-y-1/2 items-center gap-2 rounded-full border border-emerald-500/30 bg-[#071b18]/80 px-3 py-1.5 text-xs font-semibold text-emerald-300">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            Alpha-4
                        </div>
                        <div class="absolute left-[52%] top-[58%] flex -translate-x-1/2 -translate-y-1/2 items-center gap-2 rounded-full border border-cyan-500/30 bg-[#071b18]/80 px-3 py-1.5 text-xs font-semibold text-cyan-300">
                            <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
                            Bravo-2
                        </div>
                        <div class="absolute right-[18%] top-[26%] flex -translate-x-1/2 -translate-y-1/2 items-center gap-2 rounded-full border border-red-500/30 bg-[#071b18]/80 px-3 py-1.5 text-xs font-semibold text-red-300">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                            Code 3
                        </div>

                        <div class="absolute bottom-8 left-8 right-8 rounded-2xl border border-[#163b33] bg-[#071b18]/80 p-3 backdrop-blur-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#7DB5A5]">
                                <span>Live Unit Telemetry</span>
                                <span class="font-mono text-emerald-300">RTK LOCK 99.98%</span>
                            </div>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-[#163b33] bg-[#0d1f1b] p-3">
                                    <div class="text-[10px] uppercase tracking-[0.18em] text-[#7DB5A5]">Alpha-4</div>
                                    <div class="mt-2 font-mono text-lg font-bold text-[#F5FFFC]">42 km/h</div>
                                    <div class="text-[10px] text-emerald-300">On route</div>
                                </div>
                                <div class="rounded-xl border border-[#163b33] bg-[#0d1f1b] p-3">
                                    <div class="text-[10px] uppercase tracking-[0.18em] text-[#7DB5A5]">Bravo-2</div>
                                    <div class="mt-2 font-mono text-lg font-bold text-[#F5FFFC]">38 km/h</div>
                                    <div class="text-[10px] text-cyan-300">Staged</div>
                                </div>
                                <div class="rounded-xl border border-[#163b33] bg-[#0d1f1b] p-3">
                                    <div class="text-[10px] uppercase tracking-[0.18em] text-[#7DB5A5]">Incident Radius</div>
                                    <div class="mt-2 font-mono text-lg font-bold text-[#F5FFFC]">320 m</div>
                                    <div class="text-[10px] text-amber-300">High risk</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-4">
                    <div class="rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-xl">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-[#F5FFFC]">Operational Stack</h2>
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-950/40 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.18em] text-emerald-300">Active</span>
                        </div>

                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2">
                                <span class="text-[#7DB5A5]">Road speed</span>
                                <span class="font-mono font-bold text-[#F5FFFC]">32 km/h</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2">
                                <span class="text-[#7DB5A5]">Weather advisory</span>
                                <span class="font-semibold text-amber-300">Rain-slick</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2">
                                <span class="text-[#7DB5A5]">Active incidents</span>
                                <span class="font-mono font-bold text-[#F5FFFC]">14</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2">
                                <span class="text-[#7DB5A5]">SLA response</span>
                                <span class="font-mono font-bold text-[#F5FFFC]">4m 32s</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-4 shadow-xl">
                        <h2 class="text-lg font-bold text-[#F5FFFC]">Current Alerts</h2>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="rounded-xl border border-red-500/30 bg-red-950/30 p-3 text-red-200">
                                <div class="font-semibold">Code 3 Traffic Collision</div>
                                <div class="mt-1 font-mono text-xs text-red-100">Brgy. Greenfield · 2 mins ago</div>
                            </div>
                            <div class="rounded-xl border border-amber-500/30 bg-amber-950/30 p-3 text-amber-200">
                                <div class="font-semibold">Flood watch</div>
                                <div class="mt-1 font-mono text-xs text-amber-100">Poblacion bridge · 14mm/h</div>
                            </div>
                            <div class="rounded-xl border border-cyan-500/30 bg-cyan-950/30 p-3 text-cyan-200">
                                <div class="font-semibold">Emergency access lane</div>
                                <div class="mt-1 font-mono text-xs text-cyan-100">North arterial · Open</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </x-command-shell>
@endsection
