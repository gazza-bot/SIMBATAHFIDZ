@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'iconLeft' => null,
    'iconRight' => null,
    'fullWidth' => false,
    'attributes' => [],
])

<?php
$variants = [
    'primary' => 'bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 focus:ring-primary-500',
    'secondary' => 'bg-secondary-500 text-white hover:bg-secondary-600 active:bg-secondary-900 focus:ring-secondary-500',
    'outline' => 'border-2 border-primary-500 text-primary-500 hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-500',
    'ghost' => 'text-primary-500 hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-500',
    'destructive' => 'bg-error-500 text-white hover:bg-error-600 active:bg-error-700 focus:ring-error-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-4.5 py-2 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2.5',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$disabledClass = $disabled || $loading ? 'opacity-50 cursor-not-allowed' : '';
$widthClass = $fullWidth ? 'w-full' : '';
?>

<button
    type="{{ $type }}"
    class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-fast ease-standard focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $variantClass }} {{ $sizeClass }} {{ $disabledClass }} {{ $widthClass }}"
    {{ $disabled || $loading ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
>
    @if ($loading)
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif ($iconLeft)
        {{-- Icon Left Slot --}}
        <span class="flex-shrink-0" aria-hidden="true">{!! $iconLeft !!}</span>
    @endif

    {{ $slot }}

    @if ($iconRight && !$loading)
        {{-- Icon Right Slot --}}
        <span class="flex-shrink-0" aria-hidden="true">{!! $iconRight !!}</span>
    @endif
</button>