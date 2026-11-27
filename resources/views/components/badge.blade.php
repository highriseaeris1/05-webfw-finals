@props(['variant' => 'neutral'])

@php
    $variants = [
        'success' => 'bg-[#E3F4EA] text-[#1E7B45]',
        'danger' => 'bg-[#FDE7E7] text-[#B3261E]',
        'info' => 'bg-[#E5EEFB] text-[#1F4FA3]',
        'warning' => 'bg-[#FFF1D6] text-[#8A5A00]',
        'neutral' => 'bg-[#F4F6FA] text-[#6B7685]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium '.($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
