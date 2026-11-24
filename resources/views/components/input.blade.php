@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'label' => null,
    'placeholder' => '',
    'value' => null,
    'error' => null,
    'success' => false,
    'disabled' => false,
    'readonly' => false,
    'required' => false,
    'help' => null,
    'iconLeft' => null,
    'iconRight' => null,
    'attributes' => [],
    'multiple' => false,
])

<?php
$inputId = $id ?? $name ?? 'input-' . uniqid();
$errorId = $error ? $inputId . '-error' : null;
$helpId = $help ? $inputId . '-help' : null;
$describedBy = implode(' ', array_filter([$errorId, $helpId]));

$baseClasses = 'w-full rounded-lg border bg-white text-neutral-900 placeholder-neutral-500 transition-all duration-fast ease-standard';
$stateClasses = $error
    ? 'border-error-500 focus:border-error-500 focus:ring-error-500'
    : ($success ? 'border-success-500 focus:border-success-500 focus:ring-success-500' : 'border-neutral-200 focus:border-primary-500 focus:ring-primary-500');
$disabledClasses = $disabled ? 'bg-neutral-50 cursor-not-allowed opacity-75' : '';
$readonlyClasses = $readonly ? 'bg-neutral-50' : '';
$paddingClasses = 'px-3.5 py-2.5';
$iconLeftClasses = $iconLeft ? 'pl-10' : '';
$iconRightClasses = $iconRight ? 'pr-10' : '';
?>

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-neutral-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-error-500 ml-1" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($type === 'select')
            <select
                id="{{ $inputId }}"
                name="{{ $name }}"
                @if ($multiple) multiple @endif
                {{ $attributes->merge(['class' => $baseClasses . ' cursor-pointer']) }}
                @if ($disabled) disabled @endif
                @if ($required) required @endif
            >
                @if ($placeholder !== '')
                    <option value="">{{ $placeholder }}</option>
                @endif
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                class="{{ $baseClasses }} {{ $stateClasses }} {{ $disabledClasses }} {{ $readonlyClasses }} {{ $paddingClasses }} {{ $iconLeftClasses }} {{ $iconRightClasses }} focus:outline-none focus:ring-2 focus:ring-offset-0"
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if ($error) aria-invalid="true" @endif
                @if ($disabled) disabled @endif
                @if ($readonly) readonly @endif
                @if ($required) required @endif
                {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}
            />

            @if ($iconRight)
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-neutral-400" aria-hidden="true">
                    {!! $iconRight !!}
                </div>
            @endif

            @if ($success && !$error)
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-success-500" aria-hidden="true">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
            @endif
        @endif
    </div>

    @if ($error)
        <p id="{{ $errorId }}" class="mt-1.5 text-sm text-error-600" role="alert">{{ $error }}</p>
    @endif

    @if ($help)
        <p id="{{ $helpId }}" class="mt-1.5 text-sm text-neutral-500">{{ $help }}</p>
    @endif
</div>