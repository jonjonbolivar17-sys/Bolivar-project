<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Account recovery</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('Choose a new password') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">{{ __('Create a new password to secure your account.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-4">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-200">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-200">{{ __('New password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-200">{{ __('Confirm new password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-300" />
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#315349] px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 focus-visible:outline-offset-4">{{ __('Back to sign in') }}</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-400 px-4 py-2 text-sm font-bold text-[#051613] transition hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200">{{ __('Reset password') }}</button>
        </div>
    </form>
</x-guest-layout>
