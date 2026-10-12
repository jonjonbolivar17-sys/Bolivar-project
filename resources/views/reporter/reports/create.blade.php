@extends('layouts.dashboard')

@section('title', 'Report Incident · AkoHanda')

@section('content')
    <x-command-shell active-role="citizen" active-route="citizen" title="Citizen Incident Intake · MDRRMO Arakan">
        <div class="space-y-6 p-4 md:p-6">
            <div class="rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-2xl">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-red-500/30 bg-red-950/30 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-red-300">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                            Immediate Safety
                        </span>
                        <h1 class="mt-3 text-3xl font-black tracking-tight text-[#F5FFFC]">Report an Accident / Hazard</h1>
                        <p class="mt-2 text-sm text-[#7DB5A5]">Follow the 5 W’s to send emergency context to the ARAKAN dispatch desk.</p>
                    </div>

                    <a href="tel:0645218899" class="inline-flex items-center gap-2 rounded-xl border border-red-500/40 bg-red-950/60 px-4 py-2.5 text-sm font-semibold text-red-200 transition hover:bg-red-900/60">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Call 911 Hotline
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-xl">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-emerald-300">Step progress</p>
                        <h2 class="mt-2 text-xl font-bold text-[#F5FFFC]">Five W’s Intake Protocol</h2>
                    </div>
                    <div class="font-mono text-xs text-[#7DB5A5]">STEP 1 OF 5</div>
                </div>

                <div class="mb-6 grid gap-3 md:grid-cols-5">
                    @foreach (['What', 'Where', 'When', 'Who', 'Witness'] as $step)
                        <div class="rounded-2xl border {{ $loop->first ? 'border-emerald-500/40 bg-emerald-950/30 text-emerald-300' : 'border-[#163b33] bg-[#102621] text-[#7DB5A5]' }} p-3">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.2em]">{{ $step }}</div>
                        </div>
                    @endforeach
                </div>

                <form class="space-y-6">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">Incident classification</label>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach (['Two-Vehicle Collision', 'Motorcycle', 'Pedestrian', 'Hit & Run', 'Fender Bender'] as $type)
                                        <button type="button" class="rounded-full border border-[#163b33] bg-[#102621] px-3 py-2 text-xs font-medium text-[#F5FFFC] {{ $loop->first ? 'border-emerald-500/40 bg-emerald-950/30 text-emerald-300' : '' }}">
                                            {{ $type }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="severity" class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">Severity</label>
                                <input id="severity" type="range" min="1" max="3" value="2" class="mt-3 w-full accent-emerald-500">
                                <div class="mt-2 flex justify-between text-[10px] uppercase tracking-[0.18em] text-[#7DB5A5]">
                                    <span>Minor</span>
                                    <span>Moderate</span>
                                    <span>Severe</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">Location</label>
                                <div class="mt-3 rounded-2xl border border-[#163b33] bg-[#102621] p-3">
                                    <div class="font-mono text-sm text-emerald-300">14.583921° N, 121.057744° E</div>
                                    <div class="mt-2 text-sm text-[#F5FFFC]">Brgy. Greenfield, Purok 3, Arakan</div>
                                </div>
                            </div>

                            <div>
                                <label for="when" class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">When did this happen?</label>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" class="rounded-full border border-emerald-500/40 bg-emerald-950/30 px-3 py-2 text-xs font-semibold text-emerald-300">Happened Just Now</button>
                                    <input id="when" type="datetime-local" class="w-full rounded-xl border border-[#163b33] bg-[#102621] px-3 py-2 text-sm text-[#F5FFFC] placeholder:text-[#7DB5A5]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label for="context" class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">Who and context</label>
                            <textarea id="context" rows="6" class="mt-3 w-full rounded-2xl border border-[#163b33] bg-[#102621] px-4 py-3 text-sm text-[#F5FFFC] placeholder:text-[#7DB5A5]" placeholder="Describe the parties involved, injuries, road conditions, and any immediate hazards."></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">Evidence upload</label>
                            <div class="mt-3 rounded-2xl border border-dashed border-[#163b33] bg-[#102621] p-4">
                                <div class="flex h-32 items-center justify-center rounded-xl border border-[#163b33] bg-[#081d1a] text-sm text-[#7DB5A5]">
                                    Drag & drop photos or dashcam footage here
                                </div>
                            </div>
                            <div class="mt-4 rounded-xl border border-[#163b33] bg-[#0d1f1b] p-3 text-xs text-[#7DB5A5]">
                                EXIF metadata and cloud staging will be extracted automatically after upload.
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-[#163b33] pt-5 sm:flex-row sm:justify-between">
                        <a href="{{ route('reporter.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2.5 text-sm font-semibold text-[#F5FFFC]">Back to dashboard</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-emerald-500/40 bg-emerald-500 px-5 py-2.5 text-sm font-bold text-[#051613] shadow-[0_0_20px_rgba(16,185,129,0.25)]">Submit report</button>
                    </div>
                </form>
            </div>
        </div>
    </x-command-shell>
@endsection
