@extends('layouts.dashboard')

@section('title', 'Add User · AkoHanda')

@section('content')
    <x-command-shell title="Add User">
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-emerald-900 hover:underline">Back to user management</a>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950">Add a user</h1>
                <p class="mt-2 text-sm text-slate-600">Create an account and assign the access role it needs.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-900">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name" autofocus class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-900">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="role" class="block text-sm font-bold text-slate-900">Role</label>
                    <select id="role" name="role" required class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                        <option value="">Choose a role</option>
                        <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                        <option value="staff" @selected(old('role') === 'staff')>Staff</option>
                        <option value="reporter" @selected(old('role') === 'reporter')>Reporter</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-900">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-900">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-5 py-2 text-sm font-bold text-white transition hover:bg-emerald-800">Create user</button>
                </div>
            </form>
        </div>
    </x-command-shell>
@endsection
