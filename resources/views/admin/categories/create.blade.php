@extends('layouts.dashboard')

@section('title', 'Add Incident Category · AkoHanda')

@section('content')
    <x-command-shell title="Add Incident Category">
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-emerald-900 hover:underline">← Back to categories</a>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950">Add category</h1>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-900">Category name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" class="mt-2 min-h-11 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label for="description" class="block text-sm font-bold text-slate-900">Description <span class="font-normal text-slate-500">(optional)</span></label>
                    <textarea id="description" name="description" rows="4" maxlength="2000" class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-800 focus:ring-emerald-800">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Create category</button>
                </div>
            </form>
        </div>
    </x-command-shell>
@endsection
