@props([
    'variant' => 'default',
    'size' => 'md',
    'dot' => false,
    'icon' => null,
    'dismissible' => false,
    'onDismiss' => null,
    'attributes' => [],
])

<?php
$variants = [
    'default' => 'bg-neutral-100 text-neutral-700 border-neutral-200',
    'primary' => 'bg-primary-50 text-primary-700 border-primary-200',
    'secondary' => 'bg-secondary-50 text-secondary-900 border-secondary-200',
    'success' => 'bg-success-50 text-success-700 border-success-200',
    'info' => 'bg-info-50 text-info-700 border-info-200',
    'warning' => 'bg-warning-50 text-warning-700 border-warning-200',
    'error' => 'bg-error-50 text-error-700 border-error-200',
    // Mutu variants
    'mumtaz' => 'bg-green-50 text-green-700 border-green-200',
    'jayyid_jiddan' => 'bg-blue-50 text-blue-700 border-blue-200',
    'jayyid' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'dhaif' => 'bg-red-50 text-red-700 border-red-200',
    // Status variants
    'hadir' => 'bg-success-50 text-success-700 border-success-200',
    'izin' => 'bg-info-50 text-info-700 border-info-200',
    'sakit' => 'bg-warning-50 text-warning-700 border-warning-200',
    'alpa' => 'bg-error-50 text-error-700 border-error-200',
];

$sizes = [
    'xs' => 'px-2 py-0.5 text-xs gap-1',
    'sm' => 'px-2.5 py-1 text-xs gap-1.5',
    'md' => 'px-3 py-1 text-sm gap-1.5',
    'lg' => 'px-4 py-1.5 text-base gap-2',
];

$variantClass = $variants[$variant] ?? $variants['default'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
?>

<span
    class="inline-flex items-center font-medium rounded-lg border {{ $variantClass }} {{ $sizeClass }}"
    {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    @endif

    @if ($icon)
        <span class="flex-shrink-0" aria-hidden="true">{{ $icon }}</span>
    @endif

    {{ $slot }}

    @if ($dismissible)
        <button
            type="button"
            class="ml-1.5 -mr-1 p-0.5 rounded hover:bg-black/10 focus:outline-none focus:ring-2 focus:ring-current focus:ring-offset-2"
            @if ($onDismiss) wire:click="{{ $onDismiss }}" @else onclick="this.parentElement.remove()"
            aria-label="Tutup"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</span>