<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AkoHanda') }} | Secure Gateway</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#051613] font-sans text-white antialiased">
        <div class="relative min-h-screen overflow-hidden px-4 py-5 sm:px-8 sm:py-8">
            <div class="pointer-events-none absolute -right-32 -top-40 h-136 w-136 rounded-full bg-emerald-400/5 blur-3xl"></div>
            <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Return to AkoHanda home">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-300/25 bg-emerald-400/10 text-sm font-extrabold text-emerald-300">AH</span>
                    <span>
                        <span class="block text-lg font-extrabold tracking-tight">AkoHanda <span class="text-emerald-300">OPS</span></span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">MDRRMO Arakan</span>
                    </span>
                </a>
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-400 transition hover:text-white">Back to public portal</a>
            </div>

            <main class="relative mx-auto grid max-w-6xl items-center gap-8 py-10 lg:min-h-[calc(100vh-120px)] lg:grid-cols-[0.95fr_1.05fr] lg:gap-16 lg:py-12">
                <section class="hidden lg:block">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/5 px-3 py-1.5 text-[10px] font-mono font-bold uppercase tracking-[0.16em] text-emerald-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        ALL SYSTEMS NOMINAL · 28 BARANGAYS ONLINE
                    </div>
                    <p class="mt-8 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">RA 10121 compliant secure gateway</p>
                    <h1 class="mt-4 max-w-xl text-4xl font-extrabold leading-tight tracking-tight xl:text-5xl">One secure gateway for Arakan's response network.</h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-slate-400">Sign in to AkoHanda OPS, the municipal disaster risk reduction and rapid emergency management system for Arakan, Cotabato.</p>

                    <div class="mt-10 grid max-w-lg grid-cols-2 gap-3">
                        <div class="border-l-2 border-emerald-400 bg-[#0d1f1b] p-4">
                            <p class="font-mono text-xl font-semibold text-emerald-300">24/7</p>
                            <p class="mt-1 text-xs text-slate-400">Municipal response desk</p>
                        </div>
                        <div class="border-l-2 border-cyan-400 bg-[#0d1f1b] p-4">
                            <p class="font-mono text-xl font-semibold text-cyan-300">28</p>
                            <p class="mt-1 text-xs text-slate-400">Barangays under jurisdiction</p>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-[#163b33] pt-5">
                        <p class="text-xs leading-5 text-slate-500">Your account's assigned role determines which command and field tools you can access.</p>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-xl rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-[0_24px_80px_rgba(0,0,0,0.28)] sm:p-8">
                    {{ $slot }}
                </section>
            </main>

            <footer class="relative mx-auto flex max-w-6xl flex-col gap-2 border-t border-[#163b33] pt-4 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <span>AkoHanda OPS · Municipality of Arakan, Cotabato</span>
                <a href="tel:+63645218899" class="font-semibold text-emerald-300 hover:text-emerald-200">MDRRMO emergency desk: (064) 521-8899</a>
            </footer>
            </div>
        </div>
    </body>
</html>
