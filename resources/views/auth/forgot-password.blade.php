<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Account recovery</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('Reset your password') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">{{ __('Enter your email address and we will send you a link to choose a new password.') }}</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-lg border border-emerald-400/20 bg-emerald-400/5 px-3 py-2 text-sm text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#315349] px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 focus-visible:outline-offset-4">{{ __('Back to sign in') }}</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-400 px-4 py-2 text-sm font-bold text-[#051613] transition hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200">{{ __('Send reset link') }}</button>
        </div>
    </form>
</x-guest-layout>
