@props([
    'label',
    'value',
    'description',
    'highlight' => null,
])

<div class="rounded-2xl border border-[#00B894]/15 bg-[#001F18]/60 p-4 shadow-[0_0_0_1px_rgba(0,184,148,0.04)]">
    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-[#7DB5A5]">{{ $label }}</p>
    <p class="mt-3 text-2xl font-bold text-[#F5FFFC]">{{ $value }}</p>
    <p class="mt-2 text-sm text-[#7DB5A5]">{{ $description }}</p>

    @if ($highlight)
        <div class="mt-3 inline-flex rounded-full border border-[#19D3AE]/20 bg-[#00382D]/60 px-2 py-1 text-[10px] font-medium uppercase tracking-[0.16em] text-[#19D3AE]">
            {{ $highlight }}
        </div>
    @endif
</div>
