@props([
    'href' => null,
    'onclick' => null,
    'wireClick' => null,
    'danger' => false,
    'icon' => null,
    'disabled' => false,
    'attributes' => [],
])

@php
$baseClasses = 'flex items-center gap-2 w-full px-3 py-2 text-sm transition-colors';
$colorClasses = $danger
    ? 'text-error-600 hover:bg-error-50 focus:bg-error-50'
    : 'text-neutral-700 hover:bg-neutral-50 focus:bg-neutral-50';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        class="{{ $baseClasses }} {{ $colorClasses }} {{ $disabledClasses }}"
        role="menuitem"
        @if ($disabled) aria-disabled="true" @endif
        {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
    >
        @if ($icon)
            <span class="flex-shrink-0" aria-hidden="true">{{ $icon }}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="button"
        class="{{ $baseClasses }} {{ $colorClasses }} {{ $disabledClasses }}"
        role="menuitem"
        @if ($onclick) onclick="{{ $onclick }}" @endif
        @if ($wireClick) wire:click="{{ $wireClick }}" @endif
        @if ($disabled) disabled aria-disabled="true" @endif
        {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
    >
        @if ($icon)
            <span class="flex-shrink-0" aria-hidden="true">{{ $icon }}</span>
        @endif
        {{ $slot }}
    </button>
@endif