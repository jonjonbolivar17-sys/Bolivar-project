@props([
    'tone' => 'neutral',
    'label' => null,
    'status' => null,
])

@php
    $displayLabel = $label ?? ($status ? ucwords(str_replace('_', ' ', $status)) : 'Status');
    $statusTone = match ($status) {
        'pending', 'under_review' => 'warning',
        'assigned', 'in_progress' => 'cyan',
        'resolved' => 'green',
        'cancelled' => 'red',
        default => $tone,
    };
    $classes = match ($statusTone) {
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        'teal', 'green' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'cyan' => 'border-sky-200 bg-sky-50 text-sky-900',
        'red' => 'border-red-200 bg-red-50 text-red-900',
        default => 'border-slate-200 bg-slate-100 text-slate-800',
    };
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-2.5 py-1 text-xs font-bold {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $displayLabel }}
</span>
