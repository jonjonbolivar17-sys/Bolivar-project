@props([
    'tone' => 'neutral',
    'label' => 'Status',
])

@php
    $classes = match ($tone) {
        'warning' => 'border border-[#F5B700]/25 bg-[#F5B700]/10 text-[#F6D36A]',
        'teal' => 'border border-[#19D3AE]/25 bg-[#19D3AE]/10 text-[#8EF2D0]',
        'green' => 'border border-[#19D3AE]/30 bg-[#19D3AE]/10 text-[#9EF2D4]',
        'cyan' => 'border border-[#00C8FF]/25 bg-[#00C8FF]/10 text-[#A8F0FF]',
        default => 'border border-[#00B894]/20 bg-[#00382D]/80 text-[#D9FFF5]',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] {{ $classes }}">
    {{ $label }}
</span>
