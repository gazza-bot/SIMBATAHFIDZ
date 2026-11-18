@props([
    'kelompoks' => [],
    'selected' => null,
    'error' => null,
    'label' => 'Santri',
    'required' => true,
])

<?php
$inputId = 'santri_id';
$errorId = $error ? $inputId . '-error' : null;
$describedBy = $errorId ? $errorId : null;

$hasSantris = $kelompoks->contains(fn ($k) => $k->santris->count() > 0);
?>

<div class="w-full">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-neutral-700 mb-1.5">
        {{ $label }}
        @if ($required)
            <span class="text-error-500 ml-1" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($hasSantris)
        <select
            name="santri_id"
            id="{{ $inputId }}"
            class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard min-h-11 {{ $error ? 'border-error-500 focus:border-error-500 focus:ring-error-500' : 'border-neutral-200' }}"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($error) aria-invalid="true" @endif
            @if ($required) required @endif
        >
            <option value="">Pilih Santri</option>
            @foreach ($kelompoks as $kelompok)
                @if ($kelompok->santris->count() > 0)
                    <optgroup label="{{ $kelompok->nama }}">
                        @foreach ($kelompok->santris as $santri)
                            <option value="{{ $santri->id }}" {{ $selected == $santri->id ? 'selected' : '' }}>
                                {{ $santri->nama }} ({{ $santri->nis }})
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            @endforeach
        </select>
    @else
        <div class="w-full rounded-lg border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-500" role="status" aria-live="polite">
            Belum ada santri aktif di kelompok Anda. Hubungi admin.
        </div>
        <input type="hidden" name="santri_id" id="{{ $inputId }}" value="">
    @endif

    @if ($error)
        <p id="{{ $errorId }}" class="mt-1.5 text-sm text-error-600" role="alert">{{ $error }}</p>
    @endif
</div>