@props([
    'variant' => 'light',
    'size' => 'md',
    'showText' => true
])

@php
    $imgHeight = match($size) {
        'xs' => 'h-7 sm:h-8',
        'sm' => 'h-8 sm:h-9',
        'md' => 'h-9 sm:h-10',
        'lg' => 'h-10 sm:h-12',
        default => 'h-9 sm:h-10'
    };
    $textSize = match($size) {
        'xs' => 'text-base sm:text-lg',
        'sm' => 'text-lg sm:text-xl',
        'md' => 'text-xl sm:text-2xl',
        'lg' => 'text-2xl sm:text-3xl',
        default => 'text-xl sm:text-2xl'
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none group']) }}>
    <img
        src="{{ asset('images/logo.png') }}"
        alt="VexaHost Logo"
        class="{{ $imgHeight }} w-auto object-contain group-hover:scale-105 transition-transform duration-300"
    >
    @if($showText)
        <span class="{{ $textSize }} font-extrabold tracking-tight text-zinc-900 dark:text-zinc-50">
            Vexa<span class="text-[#5c8a0f] dark:text-[#BAFF39]">Host</span>
        </span>
    @endif
</div>
