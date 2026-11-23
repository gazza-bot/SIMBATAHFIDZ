@props([
    'juzStatus' => [],
    'percentages' => [],
    'variant' => 'status',
    'columns' => 6,
    'clickable' => false,
    'onJuzClick' => null,
    'attributes' => [],
])

<?php
$statusClasses = [
    'mutqin' => 'bg-success-500 text-white',
    'parsial' => 'bg-accent-500 text-neutral-900',
    'belum' => 'bg-neutral-200 text-neutral-500',
];

$percentClass = function (int $pct): string {
    if ($pct >= 85) {
        return 'bg-primary-600 text-white';
    }
    if ($pct >= 65) {
        return 'bg-primary-500 text-white';
    }
    if ($pct >= 45) {
        return 'bg-primary-300 text-primary-900';
    }
    if ($pct >= 25) {
        return 'bg-primary-200 text-primary-900';
    }
    if ($pct > 0) {
        return 'bg-primary-100 text-primary-800';
    }

    return 'bg-neutral-100 text-neutral-400';
};

$isPercent = $variant === 'percent';
$gridCols = in_array($columns, [3, 4, 5, 6], true) ? $columns : 6;
$juzStatus = $juzStatus ?: array_fill(1, 30, 'belum');
?>

<div class="space-y-4" {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
    {{-- Legend --}}
    @if ($isPercent)
        <div class="flex flex-wrap gap-4 text-xs text-neutral-600">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-primary-600"></span> Mutqin (85%+)
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-primary-300"></span> Parsial (1-84%)
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-neutral-200"></span> Belum mulai
            </span>
        </div>
    @else
        <div class="flex flex-wrap gap-3 text-sm">
            @foreach ($statusClasses as $key => $class)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $class }}">
                    <span class="w-3 h-3 rounded"></span>
                    {{ ucfirst($key) }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Grid --}}
    <div class="grid grid-cols-3 sm:grid-cols-{{ $gridCols }} gap-2" data-juz-grid>
        @for ($i = 1; $i <= 30; $i++)
            @php
                $pct = (int) ($percentages[$i] ?? 0);
                $status = $juzStatus[$i] ?? 'belum';
                $cellClass = $isPercent ? $percentClass($pct) : ($statusClasses[$status] ?? $statusClasses['belum']);
                $clickClass = $clickable ? 'cursor-pointer hover:scale-105 transition-transform duration-fast' : '';
                $focusClass = $clickable ? 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2' : '';
            @endphp

            @if ($clickable)
                <button
                    type="button"
                    class="aspect-square rounded-xl {{ $cellClass }} {{ $clickClass }} {{ $focusClass }} flex flex-col items-center justify-center text-center p-2 min-h-[56px]"
                    @if ($onJuzClick) onclick="{{ $onJuzClick }}({{ $i }})" @endif
                    aria-label="Juz {{ $i }}: {{ $isPercent ? $pct . '%' : ucfirst($status) }}"
                >
                    <span class="text-[11px] font-medium opacity-80">Juz {{ $i }}</span>
                    <span class="text-sm font-bold leading-tight">{{ $isPercent ? $pct . '%' : ucfirst($status) }}</span>
                </button>
            @else
                <div
                    class="aspect-square rounded-xl {{ $cellClass }} flex flex-col items-center justify-center text-center p-2 min-h-[56px]"
                    role="img"
                    aria-label="Juz {{ $i }}: {{ $isPercent ? $pct . '%' : ucfirst($status) }}"
                >
                    <span class="text-[11px] font-medium opacity-80">Juz {{ $i }}</span>
                    <span class="text-sm font-bold leading-tight">{{ $isPercent ? $pct . '%' : ucfirst($status) }}</span>
                </div>
            @endif
        @endfor
    </div>
</div>
