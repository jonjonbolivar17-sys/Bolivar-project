<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AkoHanda | Accident Reporting System</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#051613] font-sans text-white antialiased">
        <div class="relative min-h-screen overflow-hidden px-4 py-5 sm:px-8 sm:py-8">
            <div class="pointer-events-none absolute -right-32 -top-40 h-136 w-136 rounded-full bg-emerald-400/5 blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Return to AkoHanda home">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-300/25 bg-emerald-400/10 text-sm font-extrabold text-emerald-300">AH</span>
                    <span>
                        <span class="block text-lg font-extrabold tracking-tight">AkoHanda</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Accident Reporting System</span>
                    </span>
                </a>
                <a href="{{ route('home') }}" class="rounded text-sm font-medium text-slate-300 transition hover:text-white focus-visible:outline-offset-4">Back to home</a>
            </div>

            <main class="relative mx-auto grid max-w-6xl items-center gap-8 py-10 lg:min-h-[calc(100vh-120px)] lg:grid-cols-[0.95fr_1.05fr] lg:gap-16 lg:py-12">
                <section class="hidden lg:block" aria-labelledby="account-access-heading">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-300">Incident reporting and monitoring</p>
                    <h1 id="account-access-heading" class="mt-5 max-w-xl text-4xl font-extrabold leading-tight tracking-tight xl:text-5xl">A clear place to report incidents and follow progress.</h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-slate-300">Sign in or create an account to use the reporting features available to you.</p>

                    <div class="mt-8 max-w-lg rounded-xl border border-[#163b33] bg-[#0d1f1b] p-4">
                        <p class="text-sm font-semibold text-white">Your account, your access</p>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Available pages and actions depend on the role assigned to your account.</p>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-xl rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-5 shadow-[0_24px_80px_rgba(0,0,0,0.28)] sm:p-8">
                    {{ $slot }}
                </section>
            </main>

            <footer class="relative mx-auto flex max-w-6xl flex-col gap-2 border-t border-[#163b33] pt-4 text-[11px] text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                <span>AkoHanda · Accident Reporting System</span>
                <a href="{{ route('home') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Return to home</a>
            </footer>
        </div>
    </body>
</html>
