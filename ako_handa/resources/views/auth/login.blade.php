<x-guest-layout>
    <div x-data="{ accessLevel: 'citizen', passwordVisible: false }">
        <x-auth-session-status class="mb-5 rounded-lg border border-emerald-400/20 bg-emerald-400/5 px-3 py-2 text-sm text-emerald-200" :status="session('status')" />

        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-300">Secure gateway</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight">Access command portal</h2>
                <p class="mt-1 text-sm text-slate-400">Sign in with your registered account.</p>
            </div>
            <span class="rounded-md border border-emerald-400/20 bg-emerald-400/5 px-2 py-1 font-mono text-[10px] text-emerald-200">SEC-1</span>
        </div>

        <div class="mb-6 grid grid-cols-2 border-b border-[#163b33]" role="tablist" aria-label="Authentication options">
            <a href="{{ route('login') }}" role="tab" aria-selected="true" class="border-b-2 border-emerald-400 px-2 py-3 text-center text-sm font-semibold text-white">Sign in <span class="hidden text-slate-400 sm:inline">/ Mag-Login</span></a>
            <a href="{{ route('register') }}" role="tab" aria-selected="false" class="border-b-2 border-transparent px-2 py-3 text-center text-sm font-semibold text-slate-400 transition hover:text-white">Create account <span class="hidden sm:inline">/ Mag-Sign Up</span></a>
        </div>

        <div class="mb-5">
            <p class="mb-2 text-xs font-semibold text-slate-300">Select portal view</p>
            <div class="grid grid-cols-3 gap-2" role="group" aria-label="Portal view">
                <button type="button" @click="accessLevel = 'citizen'" :aria-pressed="accessLevel === 'citizen'" :class="accessLevel === 'citizen' ? 'border-emerald-400/50 bg-emerald-400/10 text-emerald-200' : 'border-[#163b33] text-slate-400 hover:text-white'" class="min-h-10 rounded-lg border px-2 text-[11px] font-semibold transition sm:text-xs">Citizen</button>
                <button type="button" @click="accessLevel = 'responder'" :aria-pressed="accessLevel === 'responder'" :class="accessLevel === 'responder' ? 'border-cyan-400/50 bg-cyan-400/10 text-cyan-200' : 'border-[#163b33] text-slate-400 hover:text-white'" class="min-h-10 rounded-lg border px-2 text-[11px] font-semibold transition sm:text-xs">Responder</button>
                <button type="button" @click="accessLevel = 'admin'" :aria-pressed="accessLevel === 'admin'" :class="accessLevel === 'admin' ? 'border-amber-400/50 bg-amber-400/10 text-amber-200' : 'border-[#163b33] text-slate-400 hover:text-white'" class="min-h-10 rounded-lg border px-2 text-[11px] font-semibold transition sm:text-xs">Command admin</button>
            </div>
            <p class="mt-2 text-[11px] leading-5 text-slate-500">Portal selection does not change your account permissions.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-xs font-semibold text-slate-300">MDRRMO email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.gov.ph" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white placeholder:text-slate-600 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
                <p class="mt-1.5 text-[11px] text-slate-500">Use the email address registered to your account.</p>
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <label for="password" class="text-xs font-semibold text-slate-300">Password / Incident PIN</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-emerald-300 hover:text-emerald-200">Forgot PIN?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="passwordVisible ? 'text' : 'password'" required autocomplete="current-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 pr-12 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                    <button type="button" @click="passwordVisible = !passwordVisible" :aria-label="passwordVisible ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-white">
                        <svg x-show="!passwordVisible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="passwordVisible" style="display: none" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7s-3.6 7-10 7a10 10 0 0 1-3-.5"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
            </div>

            <label for="remember_me" class="flex cursor-pointer items-center gap-2 text-xs text-slate-400">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[#163b33] bg-[#051613] text-emerald-500 focus:ring-emerald-400/30">
                Remember this device
            </label>

            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-emerald-500 px-4 text-sm font-bold text-[#051613] transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-[#0d1f1b]">Access command portal / Mag-Sign In</button>
        </form>

        <div class="mt-6 rounded-xl border border-red-400/20 bg-[#2e1214]/50 p-4">
            <p class="text-sm font-bold text-red-200">Immediate life threat or hazard?</p>
            <p class="mt-1 text-xs leading-5 text-slate-400">No login required to call emergency services. Give your location and describe the situation.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="tel:+63645218899" class="rounded-md bg-red-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-red-400">Fast desk · MDRRMO</a>
                <a href="tel:+639178829111" class="rounded-md border border-white/10 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-white/5">PNP</a>
                <a href="tel:+639981234567" class="rounded-md border border-white/10 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-white/5">BFP Fire</a>
            </div>
        </div>
    </div>
</x-guest-layout>
