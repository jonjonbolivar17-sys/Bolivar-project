@extends('layouts.dashboard')

@section('title', 'Routing Command Dashboard · AkoHanda')

@section('content')
<script>
    window.location.href = "{{ match (auth()->user()?->role ?? 'reporter') {
        'admin' => route('admin.dashboard'),
        'staff' => route('staff.dashboard'),
        default => route('user.dashboard'),
    } }}";
</script>
<x-command-shell :active-role="auth()->user()?->role ?? 'admin'" active-route="dashboard" title="AkoHanda Command Portal">
    <div class="flex items-center justify-center min-h-[50vh]">
        <div class="p-8 rounded-2xl border border-[#163b33] bg-[#0d1f1b] text-center max-w-md shadow-2xl space-y-4">
            <div class="h-10 w-10 mx-auto rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                <span class="animate-spin text-lg">⏳</span>
            </div>
            <h2 class="text-lg font-bold text-[#F5FFFC]">Loading your dashboard...</h2>
            <p class="text-xs text-[#7DB5A5]">Redirecting based on your assigned municipal access tier.</p>
        </div>
    </div>
</x-command-shell>
@endsection
