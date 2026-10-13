<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Confirm identity</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('Confirm your password') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">{{ __('Please confirm your password before continuing to this secure area.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" autofocus class="block min-h-12 w-full rounded-lg border border-[#163b33] bg-[#051613] px-3.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/15">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-400 px-5 py-2 text-sm font-bold text-[#051613] transition hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200">{{ __('Confirm password') }}</button>
        </div>
    </form>
</x-guest-layout>
