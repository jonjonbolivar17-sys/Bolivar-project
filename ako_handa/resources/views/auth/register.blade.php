<x-guest-layout>
    <div>
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-300">Citizen onboarding</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight">Create an AkoHanda account</h2>
        <p class="mt-1 text-sm text-slate-400">Register to access the citizen portal.</p>

        <div class="mb-6 mt-6 grid grid-cols-2 border-b border-[#163b33]" role="tablist" aria-label="Authentication options">
            <a href="{{ route('login') }}" role="tab" aria-selected="false" class="border-b-2 border-transparent px-2 py-3 text-center text-sm font-semibold text-slate-400 transition hover:text-white">Sign in <span class="hidden sm:inline">/ Mag-Login</span></a>
            <a href="{{ route('register') }}" role="tab" aria-selected="true" class="border-b-2 border-emerald-400 px-2 py-3 text-center text-sm font-semibold text-white">Create account <span class="hidden text-slate-400 sm:inline">/ Mag-Sign Up</span></a>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="mb-1.5 block text-xs font-semibold text-slate-300">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white placeholder:text-slate-600 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs font-semibold text-slate-300">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white placeholder:text-slate-600 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-semibold text-slate-300">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold text-slate-300">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-300" />
            </div>

            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-emerald-500 px-4 text-sm font-bold text-[#051613] transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-[#0d1f1b]">Create citizen account</button>
        </form>

        <p class="mt-5 text-center text-xs text-slate-500">Already registered? <a href="{{ route('login') }}" class="font-semibold text-emerald-300 hover:text-emerald-200">Sign in</a></p>
    </div>
</x-guest-layout>
