@extends('layouts.dashboard')

@section('title', 'Incident Categories · AkoHanda')

@section('content')
    <x-command-shell title="Incident Categories">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">Incident categories</h1>
                    <p class="mt-2 text-sm text-slate-600">Manage the categories reporters can select when submitting a report.</p>
                </div>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Add category</a>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                @if ($categories->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-slate-600">No incident categories are set up yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
                                <tr><th class="px-5 py-3">Category</th><th class="px-5 py-3">Reports</th><th class="px-5 py-3">Availability</th><th class="px-5 py-3 text-right">Actions</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="px-5 py-4"><p class="font-bold text-slate-900">{{ $category->name }}</p><p class="mt-1 text-xs text-slate-600">{{ $category->description ?: 'No description provided' }}</p></td>
                                        <td class="px-5 py-4 text-slate-700">{{ $category->incident_reports_count }}</td>
                                        <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $category->is_active ? 'bg-emerald-50 text-emerald-900' : 'bg-slate-100 text-slate-700' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="font-bold text-emerald-900 hover:underline">Edit</a>
                                                @if ($category->incident_reports_count === 0)
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="font-bold text-red-800 hover:underline">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-5 py-4">{{ $categories->links() }}</div>
                @endif
            </section>
        </div>
    </x-command-shell>
@endsection
