@extends('layouts.dashboard')

@section('title', 'Profile & Settings · AkoHanda')

@section('content')
    <x-command-shell title="Profile & Settings">
        <div class="mx-auto max-w-4xl space-y-6">
            <header>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-800">Account</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Profile &amp; Settings</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Update your account details, change your password, or manage your account.</p>
            </header>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @include('profile.partials.update-password-form')
            </section>

            <section class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-7">
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </x-command-shell>
@endsection
