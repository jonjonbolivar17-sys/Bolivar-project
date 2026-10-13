<x-guest-layout>
    <div x-data="{ passwordVisible: false }">
        <x-auth-session-status class="mb-5 rounded-lg border border-emerald-400/20 bg-emerald-400/5 px-3 py-2 text-sm text-emerald-200" :status="session('status')" />

        <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Account access</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight">Sign in to AkoHanda</h2>
            <p class="mt-1 text-sm text-slate-300">Use the email address and password registered to your account.</p>
        </div>

        <nav class="mb-6 grid grid-cols-2 border-b border-[#163b33]" aria-label="Account">
            <a href="{{ route('login') }}" aria-current="page" class="border-b-2 border-emerald-400 px-2 py-3 text-center text-sm font-semibold text-white">Sign in</a>
            <a href="{{ route('register') }}" class="rounded border-b-2 border-transparent px-2 py-3 text-center text-sm font-semibold text-slate-300 transition hover:text-white focus-visible:outline-offset-4">Create account</a>
        </nav>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-200">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <label for="password" class="text-sm font-semibold text-slate-200">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="rounded text-xs font-medium text-emerald-300 hover:text-emerald-200 focus-visible:outline-offset-4">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="passwordVisible ? 'text' : 'password'" required autocomplete="current-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 pr-12 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                    <button type="button" @click="passwordVisible = !passwordVisible" :aria-label="passwordVisible ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded text-slate-300 transition hover:text-white focus-visible:outline-offset-[-3px]">
                        <svg x-show="!passwordVisible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="passwordVisible" x-cloak aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7s-3.6 7-10 7a10 10 0 0 1-3-.5"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
            </div>

            <label for="remember_me" class="flex min-h-11 cursor-pointer items-center gap-2 text-sm text-slate-300">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[#163b33] bg-[#051613] text-emerald-500 focus:ring-emerald-400/30">
                Remember me
            </label>

            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-emerald-400 px-4 text-sm font-bold text-[#051613] transition hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200">Sign in</button>
        </form>
    </div>
</x-guest-layout>
