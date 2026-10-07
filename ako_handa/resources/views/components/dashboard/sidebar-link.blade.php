@props([
    'label',
    'href' => '#',
    'active' => false,
    'icon' => null,
    'badge' => null,
])

<a
    href="{{ $href }}"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center px-2' : 'justify-between px-3'"
    class="group flex items-center gap-3 rounded-xl border py-2.5 text-sm font-medium transition duration-150 {{ $active ? 'border-[#00B894]/30 bg-[#00382D] text-[#F5FFFC] shadow-[inset_2px_0_0_#F5B700]' : 'border-transparent text-[#7DB5A5] hover:border-[#00B894]/30 hover:bg-[#00382D]/60 hover:text-[#F5FFFC]' }}"
>
    <span class="flex items-center gap-3" :class="sidebarState === 'rail' && !mobileSidebarOpen ? 'justify-center' : ''">
        <span class="flex h-5 w-5 items-center justify-center {{ $active ? 'text-[#F5B700]' : 'text-[#7DB5A5] group-hover:text-[#F5FFFC]' }}">
            @php echo $icon; @endphp
        </span>
        <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" x-transition.opacity>{{ $label }}</span>
    </span>

    @if ($badge)
        <span x-show="sidebarState !== 'rail' || mobileSidebarOpen" class="inline-flex min-w-6 items-center justify-center rounded-full bg-[#F5B700] px-1.5 py-0.5 text-[10px] font-bold text-[#001F18]">
            {{ $badge }}
        </span>
    @endif
</a>
