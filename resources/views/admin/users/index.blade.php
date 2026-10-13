@extends('layouts.dashboard')

@section('title', 'User Management · AkoHanda')

@section('content')
    <x-command-shell title="User Management">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">User management</h1>
                    <p class="mt-2 text-sm text-slate-600">Manage accounts and their access to the reporting system.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-800 focus-visible:outline-offset-2">
                    Add user
                </a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_14rem_auto_auto] sm:items-end">
                <div>
                    <label for="search" class="block text-sm font-bold text-slate-900">Search users</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Name or email address" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                </div>
                <div>
                    <label for="role" class="block text-sm font-bold text-slate-900">Role</label>
                    <select id="role" name="role" class="mt-1 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                        <option value="">All roles</option>
                        @foreach (['admin' => 'Administrator', 'staff' => 'Staff', 'reporter' => 'Reporter'] as $roleValue => $roleLabel)
                            <option value="{{ $roleValue }}" @selected(request('role') === $roleValue)>{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-800 focus-visible:outline-offset-2">
                    Apply filters
                </button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-offset-2">
                    Reset
                </a>
            </form>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                @if ($users->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <h2 class="font-bold text-slate-900">No users found</h2>
                        <p class="mt-2 text-sm text-slate-600">Try changing your search or role filter.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Name</th>
                                    <th scope="col" class="px-5 py-3">Email</th>
                                    <th scope="col" class="px-5 py-3">Role</th>
                                    <th scope="col" class="px-5 py-3">Joined</th>
                                    <th scope="col" class="px-5 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-slate-700">{{ $user->role }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="font-bold text-emerald-900 hover:underline focus-visible:outline-offset-2">Edit</a>
                                                @if ($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="font-bold text-red-800 hover:underline focus-visible:outline-offset-2">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
