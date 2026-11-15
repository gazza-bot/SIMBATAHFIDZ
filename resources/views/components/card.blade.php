@props([
    'variant' => 'default',
    'padding' => 'default',
    'hover' => false,
    'attributes' => [],
])

<?php
$variants = [
    'default' => 'bg-white border border-neutral-200 shadow-level-1',
    'elevated' => 'bg-white border border-neutral-200 shadow-level-2',
    'subtle' => 'bg-neutral-50 border border-neutral-100',
    'interactive' => 'bg-white border border-neutral-200 shadow-level-2 hover:shadow-level-3 transition-shadow duration-normal ease-standard cursor-pointer',
];

$paddings = [
    'none' => '',
    'sm' => 'p-3',
    'default' => 'p-6',
    'lg' => 'p-8',
    'mobile' => 'p-4 sm:p-6',
];

$variantClass = $variants[$variant] ?? $variants['default'];
$paddingClass = $paddings[$padding] ?? $paddings['default'];
$hoverClass = $hover && $variant !== 'interactive' ? 'hover:shadow-level-3 transition-shadow duration-normal ease-standard' : '';
?>

<div
    class="rounded-2xl {{ $variantClass }} {{ $paddingClass }} {{ $hoverClass }}"
    {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
>
    {{ $slot }}
</div>