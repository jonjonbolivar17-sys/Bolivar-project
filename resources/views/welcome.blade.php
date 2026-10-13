<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#0b2926">
        <meta name="description" content="Report accidents, share a location when you choose, and follow incident report progress with AkoHanda.">
        <title>Accident Reporting System | AkoHanda</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="landing-page min-h-screen font-sans text-slate-900 antialiased selection:bg-emerald-200 selection:text-[#0b2926]">
        @php
            $currentUser = auth()->user();
            $getStartedUrl = $currentUser === null
                ? route('register')
                : ($currentUser->role === 'reporter' ? route('reporter.reports.create') : route('dashboard'));
            $getStartedLabel = $currentUser === null
                ? 'Get Started'
                : ($currentUser->role === 'reporter' ? 'Create a Report' : 'Go to Dashboard');
        @endphp

        <div
            x-data="{ mobileMenuOpen: false }"
            @keydown.escape.window="mobileMenuOpen = false"
            class="flex min-h-screen flex-col overflow-x-hidden"
        >
            <header class="relative z-20 border-b border-white/10 bg-[#0b2926] text-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="#home" class="flex min-w-0 items-center gap-3 rounded-lg focus-visible:outline-offset-4" aria-label="AkoHanda Accident Reporting System home">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-400 text-[#0b2926]" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-extrabold tracking-tight sm:text-base">AkoHanda</span>
                            <span class="block truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-emerald-100/70 sm:text-[11px]">Accident Reporting System</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-7 text-sm font-semibold text-emerald-50/80 lg:flex" aria-label="Main navigation">
                        <a href="#home" class="rounded py-2 transition hover:text-white">Home</a>
                        <a href="#about" class="rounded py-2 transition hover:text-white">About</a>
                        <a href="#features" class="rounded py-2 transition hover:text-white">Features</a>
                        <a href="#how-it-works" class="rounded py-2 transition hover:text-white">How It Works</a>
                    </nav>

                    <div class="hidden items-center gap-3 lg:flex">
                        @guest
                            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">Login</a>
                            <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Register</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Dashboard</a>
                        @endguest
                    </div>

                    <button
                        type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        :aria-expanded="mobileMenuOpen"
                        aria-controls="mobile-navigation"
                        aria-label="Toggle navigation menu"
                        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-white/20 text-white transition hover:bg-white/10 focus-visible:outline-offset-4 lg:hidden"
                    >
                        <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <nav
                    id="mobile-navigation"
                    x-show="mobileMenuOpen"
                    x-cloak
                    class="border-t border-white/10 bg-[#0b2926] px-4 py-4 sm:px-6 lg:hidden"
                    aria-label="Mobile navigation"
                >
                    <div class="mx-auto flex max-w-7xl flex-col gap-1">
                        <a @click="mobileMenuOpen = false" href="#home" class="rounded-lg px-3 py-3 text-sm font-semibold text-emerald-50 transition hover:bg-white/10">Home</a>
                        <a @click="mobileMenuOpen = false" href="#about" class="rounded-lg px-3 py-3 text-sm font-semibold text-emerald-50 transition hover:bg-white/10">About</a>
                        <a @click="mobileMenuOpen = false" href="#features" class="rounded-lg px-3 py-3 text-sm font-semibold text-emerald-50 transition hover:bg-white/10">Features</a>
                        <a @click="mobileMenuOpen = false" href="#how-it-works" class="rounded-lg px-3 py-3 text-sm font-semibold text-emerald-50 transition hover:bg-white/10">How It Works</a>
                        <div class="mt-2 flex gap-3 border-t border-white/10 pt-4">
                            @guest
                                <a @click="mobileMenuOpen = false" href="{{ route('login') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg border border-white/20 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">Login</a>
                                <a @click="mobileMenuOpen = false" href="{{ route('register') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Register</a>
                            @else
                                <a @click="mobileMenuOpen = false" href="{{ route('dashboard') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Dashboard</a>
                            @endguest
                        </div>
                    </div>
                </nav>
            </header>

            <main class="flex-1">
                <section id="home" class="scroll-mt-6">
                    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2 lg:gap-16 lg:px-8 lg:py-24">
                        <div class="max-w-2xl">
                            <p class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-bold uppercase tracking-[0.12em] text-emerald-900">
                                <span class="h-2 w-2 rounded-full bg-red-600" aria-hidden="true"></span>
                                Incident reporting and monitoring
                            </p>
                            <h1 class="mt-6 text-4xl font-extrabold leading-[1.12] tracking-tight text-[#102a27] sm:text-5xl lg:text-6xl">
                                Report Accidents. <span class="text-emerald-800">Record Locations.</span> Track Progress.
                            </h1>
                            <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                                Submit incident details, share GPS location when you grant permission, attach supporting photos, and follow your report’s status in one organized system.
                            </p>
                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ $getStartedUrl }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus-visible:outline-offset-4">
                                    {{ $getStartedLabel }}
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                    </svg>
                                </a>
                                <a href="#features" class="inline-flex min-h-12 items-center justify-center rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-[#163d38] transition hover:border-emerald-700 hover:bg-emerald-50 focus-visible:outline-offset-4">
                                    Explore Features
                                </a>
                            </div>
                            <p class="mt-4 text-sm text-slate-500">Location access is optional and requested only when needed for your report.</p>
                        </div>

                        <div class="relative mx-auto w-full max-w-xl" role="img" aria-label="Illustration of an incident report form beside a map location marker">
                            <div class="absolute -inset-4 rounded-[2rem] bg-emerald-100/70 blur-2xl" aria-hidden="true"></div>
                            <div class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-white p-4 shadow-[0_24px_70px_rgba(15,50,45,0.12)] sm:p-6">
                                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-700" aria-hidden="true">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900">Incident report</p>
                                            <p class="text-xs text-slate-500">Illustrative preview</p>
                                        </div>
                                    </div>
                                    <span class="rounded-full border border-slate-200 px-3 py-1 text-[11px] font-semibold text-slate-600">Report details</span>
                                </div>

                                <div class="grid gap-4 pt-4 sm:grid-cols-[1fr_1.1fr]">
                                    <div class="space-y-3">
                                        <div class="rounded-xl border border-slate-200 p-3">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <svg class="h-4 w-4 text-emerald-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                                </svg>
                                                <span class="text-xs font-bold">Incident details</span>
                                            </div>
                                            <div class="mt-3 h-2 w-4/5 rounded-full bg-slate-100"></div>
                                            <div class="mt-2 h-2 w-3/5 rounded-full bg-slate-100"></div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 p-3">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <svg class="h-4 w-4 text-emerald-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/>
                                                    <circle cx="12" cy="10" r="2.2"/>
                                                </svg>
                                                <span class="text-xs font-bold">Location</span>
                                            </div>
                                            <p class="mt-2 text-xs leading-5 text-slate-500">Share your location only if you choose to allow it.</p>
                                        </div>
                                        <div class="rounded-xl border border-dashed border-slate-300 p-3">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <svg class="h-4 w-4 text-emerald-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 0 1 2-2h2l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z"/>
                                                    <circle cx="12" cy="13" r="3"/>
                                                </svg>
                                                <span class="text-xs font-bold">Photo evidence</span>
                                            </div>
                                            <p class="mt-2 text-xs text-slate-500">Attach an image to support your report.</p>
                                        </div>
                                    </div>

                                    <div class="relative min-h-56 overflow-hidden rounded-2xl border border-emerald-100 bg-[#edf5f2]">
                                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 360 280" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                            <path d="M-20 55 380 225M-20 225 380 55M90-30l100 340M270-30 165 310" fill="none" stroke="#c8ddd6" stroke-width="22"/>
                                            <path d="M-20 55 380 225M-20 225 380 55M90-30l100 340M270-30 165 310" fill="none" stroke="#f8fbfa" stroke-width="12"/>
                                            <path d="M0 140h360M180 0v280" fill="none" stroke="#d8e8e3" stroke-dasharray="4 8" stroke-width="2"/>
                                        </svg>
                                        <div class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 items-center justify-center">
                                            <span class="absolute h-16 w-16 rounded-full bg-emerald-400/20" aria-hidden="true"></span>
                                            <span class="relative flex h-11 w-11 items-center justify-center rounded-full border-4 border-white bg-emerald-800 text-white shadow-lg" aria-hidden="true">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/>
                                                    <circle cx="12" cy="10" r="2.2"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <span class="absolute bottom-3 left-3 rounded-lg border border-white/80 bg-white/90 px-3 py-2 text-[11px] font-semibold text-slate-700 shadow-sm">Location preview · not live data</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="scroll-mt-6 border-y border-slate-200 bg-white py-16 sm:py-20">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">System features</p>
                            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-[#102a27] sm:text-4xl">Everything You Need to Report and Monitor Incidents</h2>
                            <p class="mt-4 text-base leading-7 text-slate-600">A clear path from submitting an incident to following its progress.</p>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-700" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">Online Accident Reporting</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Submit incident details through a structured online form.</p>
                            </article>

                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/>
                                        <circle cx="12" cy="10" r="2.2"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">GPS-Based Location</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Capture geographic coordinates when you grant location permission.</p>
                            </article>

                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-800" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 0 1 2-2h2l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z"/>
                                        <circle cx="12" cy="13" r="3"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">Photo and Evidence Upload</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Attach supporting images to provide more context about an incident.</p>
                            </article>

                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-800" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M7 15l4-4 3 3 5-6"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 8h3v3"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">Report Status Tracking</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Return to your account to follow the progress of submitted reports.</p>
                            </article>

                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-700" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18v13H3zM8 21h8m-4-4v4"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 13V9l3-2 3 2v4m1 0V8l3-2"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">Incident Monitoring</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Authorized personnel can review incident reports and update their status.</p>
                            </article>

                            <article class="rounded-2xl border border-slate-200 bg-white p-6">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v15H4zM8 9h8M8 13h8M8 17h5"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3"/>
                                    </svg>
                                </span>
                                <h3 class="mt-5 text-base font-extrabold text-slate-900">Centralized Report Management</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Help administrators organize users, incident categories, and report records.</p>
                            </article>
                        </div>
                    </div>
                </section>

                <section id="how-it-works" class="scroll-mt-6 py-16 sm:py-20">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">A simple process</p>
                            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-[#102a27] sm:text-4xl">How the Reporting Process Works</h2>
                            <p class="mt-4 text-base leading-7 text-slate-600">Provide the information you have, then return to your account to check for updates.</p>
                        </div>

                        <ol class="relative mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                            <li class="relative rounded-2xl border border-slate-200 bg-white p-5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2926] text-sm font-extrabold text-white">1</span>
                                <h3 class="mt-4 text-sm font-extrabold text-slate-900">Create an account or log in</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Access the reporting features with your account.</p>
                            </li>
                            <li class="relative rounded-2xl border border-slate-200 bg-white p-5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2926] text-sm font-extrabold text-white">2</span>
                                <h3 class="mt-4 text-sm font-extrabold text-slate-900">Complete the report</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Describe what happened using the report form.</p>
                            </li>
                            <li class="relative rounded-2xl border border-slate-200 bg-white p-5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2926] text-sm font-extrabold text-white">3</span>
                                <h3 class="mt-4 text-sm font-extrabold text-slate-900">Add location and evidence</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Provide location details and attach supporting images if available.</p>
                            </li>
                            <li class="relative rounded-2xl border border-slate-200 bg-white p-5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2926] text-sm font-extrabold text-white">4</span>
                                <h3 class="mt-4 text-sm font-extrabold text-slate-900">Submit your report</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Review your information and submit it for authorized review.</p>
                            </li>
                            <li class="relative rounded-2xl border border-slate-200 bg-white p-5 sm:col-span-2 lg:col-span-1">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2926] text-sm font-extrabold text-white">5</span>
                                <h3 class="mt-4 text-sm font-extrabold text-slate-900">Track report status</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Sign in to review the latest status shown for your report.</p>
                            </li>
                        </ol>
                    </div>
                </section>

                <section id="about" class="scroll-mt-6 border-y border-emerald-100 bg-[#edf5f2] py-16 sm:py-20">
                    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">About the system</p>
                            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-[#102a27] sm:text-4xl">A More Organized Way to Report Accidents</h2>
                            <p class="mt-5 text-base leading-7 text-slate-600">
                                AkoHanda brings accident and incident reports into one organized system. People can submit details and supporting information, while authorized personnel can review reports and monitor status changes.
                            </p>
                            <p class="mt-4 text-base leading-7 text-slate-600">
                                Keeping report details and location information together makes it easier to review incidents and maintain accessible records—without requiring location access unless you choose to share it.
                            </p>
                            <a href="#how-it-works" class="mt-6 inline-flex min-h-11 items-center gap-2 rounded-lg font-bold text-emerald-900 underline decoration-emerald-400 underline-offset-4 transition hover:text-emerald-700">
                                See how reporting works
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>

                        <div role="group" aria-label="Organized incident report information" class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-[0_18px_50px_rgba(15,50,45,0.08)] sm:p-7">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-800">One organized report</p>
                            <h3 class="mt-2 text-xl font-extrabold text-[#102a27]">Information ready for review</h3>
                            <div class="mt-6 divide-y divide-slate-100">
                                <div class="flex items-center gap-4 py-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7zM14 3v6h5M10 13h6m-6 4h6"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">Incident details</span>
                                </div>
                                <div class="flex items-center gap-4 py-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">Location information, when shared</span>
                                </div>
                                <div class="flex items-center gap-4 py-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-800" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 0 1 2-2h2l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z"/><circle cx="12" cy="13" r="3"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">Supporting evidence</span>
                                </div>
                                <div class="flex items-center gap-4 pt-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-800" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M7 15l4-4 3 3 5-6"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 8h3v3"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">Status updates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
                    <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 rounded-3xl bg-[#0b2926] px-6 py-9 text-white sm:px-10 sm:py-12 lg:flex-row lg:items-center lg:px-14">
                        <div class="max-w-2xl">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-200">Start when you’re ready</p>
                            <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Ready to Get Started?</h2>
                            <p class="mt-4 max-w-xl text-base leading-7 text-emerald-50/80">Submit incident information through a structured reporting system and keep track of its progress.</p>
                        </div>
                        <a href="{{ $getStartedUrl }}" class="inline-flex min-h-12 w-full shrink-0 items-center justify-center gap-2 rounded-lg bg-red-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-red-700 focus-visible:outline-offset-4 sm:w-auto">
                            {{ $getStartedLabel }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                            </svg>
                        </a>
                    </div>
                </section>
            </main>

            <footer class="bg-[#0b2926] text-emerald-50">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
                    <div class="md:col-span-2">
                        <a href="#home" class="inline-flex items-center gap-3 rounded-lg focus-visible:outline-offset-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-400 text-[#0b2926]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01"/></svg>
                            </span>
                            <span class="text-base font-extrabold">AkoHanda</span>
                        </a>
                        <p class="mt-4 max-w-sm text-sm leading-6 text-emerald-50/70">A web-based system for submitting accident reports and monitoring their progress.</p>
                    </div>

                    <div>
                        <h2 class="text-sm font-extrabold text-white">Explore</h2>
                        <ul class="mt-3 flex flex-col gap-2 text-sm text-emerald-50/75">
                            <li><a href="#home" class="rounded underline-offset-4 hover:text-white hover:underline">Home</a></li>
                            <li><a href="#about" class="rounded underline-offset-4 hover:text-white hover:underline">About</a></li>
                            <li><a href="#features" class="rounded underline-offset-4 hover:text-white hover:underline">Features</a></li>
                            <li><a href="#how-it-works" class="rounded underline-offset-4 hover:text-white hover:underline">How It Works</a></li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-sm font-extrabold text-white">Account</h2>
                        <ul class="mt-3 flex flex-col gap-2 text-sm text-emerald-50/75">
                            @guest
                                <li><a href="{{ route('login') }}" class="rounded underline-offset-4 hover:text-white hover:underline">Login</a></li>
                                <li><a href="{{ route('register') }}" class="rounded underline-offset-4 hover:text-white hover:underline">Register</a></li>
                            @else
                                <li><a href="{{ route('dashboard') }}" class="rounded underline-offset-4 hover:text-white hover:underline">Dashboard</a></li>
                            @endguest
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10">
                    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-emerald-50/60 sm:px-6 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                        <span>© {{ now()->year }} AkoHanda Accident Reporting System</span>
                        <span>Incident information organized for review and follow-up.</span>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
