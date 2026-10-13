<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Email verification</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('Verify your email address') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">{{ __('Check your inbox and follow the verification link we sent. If you did not receive it, you can request another email below.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div role="status" class="mb-4 rounded-lg border border-emerald-400/20 bg-emerald-400/5 px-3 py-2 text-sm font-medium text-emerald-200">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-emerald-400 px-4 py-2 text-sm font-bold text-[#051613] transition hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200 sm:w-auto">{{ __('Resend verification email') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#315349] px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 focus-visible:outline-offset-4">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
