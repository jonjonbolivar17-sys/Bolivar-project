@props([
    'label',
    'value',
    'meta',
    'icon' => null,
    'tone' => 'emerald',
])

@php
    $toneClasses = match ($tone) {
        'warning' => 'border-[#F5B700]/25 bg-[#05342A] text-[#F5B700]',
        'success' => 'border-[#19D3AE]/20 bg-[#052D25] text-[#19D3AE]',
        'teal' => 'border-[#00B894]/20 bg-[#00382D]/80 text-[#00B894]',
        default => 'border-[#00B894]/20 bg-[#00382D]/80 text-[#19D3AE]',
    };
@endphp

<div class="rounded-2xl border bg-[#00382D]/80 p-5 shadow-[0_0_0_1px_rgba(0,184,148,0.04)] {{ $toneClasses }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#7DB5A5]">{{ $label }}</p>
            <p class="mt-4 text-3xl font-bold tracking-tight text-[#F5FFFC]">{{ $value }}</p>
        </div>

        <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#00B894]/20 bg-[#001F18]/60">
            <span class="h-5 w-5">@php echo $icon; @endphp</span>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-2 text-sm text-[#7DB5A5]">
        <span class="inline-flex items-center gap-1 text-[#19D3AE]">
            {{ $meta }}
        </span>
    </div>
</div>
