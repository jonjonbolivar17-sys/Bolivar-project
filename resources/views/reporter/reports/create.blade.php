@extends('layouts.dashboard')

@section('title', 'Submit Accident Report · AkoHanda')

@section('content')
    <x-command-shell title="Submit Accident Report">
        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <a href="{{ route('reporter.dashboard') }}" class="text-sm font-semibold text-emerald-900 hover:underline">← Back to dashboard</a>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950">Submit an accident report</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Describe the incident as clearly as you can. Location sharing is optional; you can enter a location manually instead.</p>
            </div>

            @if ($categories->isEmpty())
                <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                    Incident categories are not available yet. Please contact an administrator before submitting a report.
                </div>
            @else
                <form method="POST" action="{{ route('reporter.reports.store') }}" enctype="multipart/form-data" x-data="{ submitting: false, gpsState: 'idle', gpsMessage: '', latitude: @js(old('latitude', '')), longitude: @js(old('longitude', '')) }" @submit="submitting = true" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    @csrf

                    <div>
                        <label for="incident_category_id" class="block text-sm font-bold text-slate-900">Incident category <span class="text-red-700" aria-hidden="true">*</span></label>
                        <select id="incident_category_id" name="incident_category_id" required class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800">
                            <option value="">Choose a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('incident_category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('incident_category_id')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="occurred_at" class="block text-sm font-bold text-slate-900">Date and time of incident <span class="text-red-700" aria-hidden="true">*</span></label>
                            <input id="occurred_at" name="occurred_at" type="datetime-local" max="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('occurred_at') }}" required class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800">
                            <x-input-error :messages="$errors->get('occurred_at')" class="mt-2" />
                        </div>
                        <div>
                            <label for="severity" class="block text-sm font-bold text-slate-900">Severity <span class="text-red-700" aria-hidden="true">*</span></label>
                            <select id="severity" name="severity" required class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800">
                                @foreach (['low' => 'Low', 'moderate' => 'Moderate', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('severity', 'moderate') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('severity')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-900">What happened? <span class="text-red-700" aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" required minlength="10" maxlength="10000" aria-describedby="description-help" class="mt-2 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="Include relevant details such as what happened and any immediate hazards.">{{ old('description') }}</textarea>
                        <p id="description-help" class="mt-1 text-xs text-slate-500">10–10,000 characters. Do not include information you do not want shared with authorized reviewers.</p>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <label for="persons_involved" class="block text-sm font-bold text-slate-900">People involved <span class="font-normal text-slate-500">(optional)</span></label>
                        <textarea id="persons_involved" name="persons_involved" rows="3" maxlength="5000" class="mt-2 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="Names or a brief description, if known.">{{ old('persons_involved') }}</textarea>
                        <x-input-error :messages="$errors->get('persons_involved')" class="mt-2" />
                    </div>

                    <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4 sm:p-5">
                        <legend class="px-1 text-sm font-bold text-slate-900">Incident location</legend>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm leading-6 text-slate-600">Choose GPS only if you want to share your current device location. You can provide an address without using GPS.</p>
                            <button type="button" @click="if (!navigator.geolocation) { gpsState = 'error'; gpsMessage = 'Location is not supported by this browser. You can enter the location manually.'; return; } gpsState = 'loading'; gpsMessage = 'Requesting location permission…'; navigator.geolocation.getCurrentPosition(position => { latitude = position.coords.latitude.toFixed(7); longitude = position.coords.longitude.toFixed(7); gpsState = 'success'; gpsMessage = 'Coordinates captured from your device.'; }, error => { gpsState = 'error'; gpsMessage = error.code === 1 ? 'Permission was denied. You can enter the location manually.' : error.code === 2 ? 'Your location is unavailable. You can enter it manually.' : 'Location request timed out. You can enter it manually.'; }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });" :disabled="gpsState === 'loading'" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg border border-emerald-800 px-4 py-2 text-sm font-bold text-emerald-950 transition hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60">
                                <span x-show="gpsState !== 'loading'">Use my location</span>
                                <span x-show="gpsState === 'loading'" x-cloak>Getting location…</span>
                            </button>
                        </div>
                        <p x-show="gpsMessage" x-cloak aria-live="polite" class="text-sm" :class="gpsState === 'error' ? 'text-red-800' : 'text-emerald-900'" x-text="gpsMessage"></p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="location" class="block text-sm font-bold text-slate-900">Address or place description <span class="text-red-700" aria-hidden="true">*</span></label>
                                <input id="location" name="location" type="text" value="{{ old('location') }}" required maxlength="255" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="Road, purok, landmark, or nearby location">
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div>
                                <label for="barangay" class="block text-sm font-bold text-slate-900">Barangay <span class="font-normal text-slate-500">(optional)</span></label>
                                <input id="barangay" name="barangay" type="text" value="{{ old('barangay') }}" maxlength="255" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="Barangay name">
                                <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
                            </div>
                            <div>
                                <label for="latitude" class="block text-sm font-bold text-slate-900">Latitude <span class="font-normal text-slate-500">(optional)</span></label>
                                <input id="latitude" name="latitude" type="number" step="any" min="-90" max="90" x-model="latitude" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="GPS latitude or manual value">
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-bold text-slate-900">Longitude <span class="font-normal text-slate-500">(optional)</span></label>
                                <input id="longitude" name="longitude" type="number" step="any" min="-180" max="180" x-model="longitude" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:border-emerald-800 focus:ring-emerald-800" placeholder="GPS longitude or manual value">
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <div>
                        <label for="evidence" class="block text-sm font-bold text-slate-900">Photo evidence <span class="font-normal text-slate-500">(optional)</span></label>
                        <input id="evidence" name="evidence" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block min-h-11 w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:min-h-11 file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-bold file:text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">JPEG, PNG, or WebP image up to 10 MB. Evidence is stored privately and only served to authorized viewers.</p>
                        <x-input-error :messages="$errors->get('evidence')" class="mt-2" />
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('reporter.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                        <button type="submit" :disabled="submitting" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-800 disabled:cursor-wait disabled:opacity-60">
                            <span x-show="!submitting">Submit report</span>
                            <span x-show="submitting" x-cloak>Submitting…</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </x-command-shell>
@endsection
