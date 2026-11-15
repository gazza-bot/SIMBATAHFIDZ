@props([
    'juzStatus' => [],
    'columns' => 6,
    'clickable' => false,
    'onJuzClick' => null,
    'attributes' => [],
])

<?php
$defaultStatus = [
    'mutqin' => 'bg-success-500 text-white',
    'ziyadah' => 'bg-info-500 text-white',
    'proses' => 'bg-accent-500 text-neutral-900',
    'belum' => 'bg-neutral-200 text-neutral-500',
];

$juzLabels = [
    1 => ['start' => 1, 'end' => 7], 2 => ['start' => 8, 'end' => 14],
    3 => ['start' => 15, 'end' => 21], 4 => ['start' => 22, 'end' => 28],
    5 => ['start' => 29, 'end' => 35], 6 => ['start' => 36, 'end' => 42],
    7 => ['start' => 43, 'end' => 49], 8 => ['start' => 50, 'end' => 56],
    9 => ['start' => 57, 'end' => 63], 10 => ['start' => 64, 'end' => 70],
    11 => ['start' => 71, 'end' => 77], 12 => ['start' => 78, 'end' => 84],
    13 => ['start' => 85, 'end' => 91], 14 => ['start' => 92, 'end' => 98],
    15 => ['start' => 99, 'end' => 105], 16 => ['start' => 106, 'end' => 112],
    17 => ['start' => 113, 'end' => 119], 18 => ['start' => 120, 'end' => 126],
    19 => ['start' => 127, 'end' => 133], 20 => ['start' => 134, 'end' => 140],
    21 => ['start' => 141, 'end' => 147], 22 => ['start' => 148, 'end' => 154],
    23 => ['start' => 155, 'end' => 161], 24 => ['start' => 162, 'end' => 168],
    25 => ['start' => 169, 'end' => 175], 26 => ['start' => 176, 'end' => 182],
    27 => ['start' => 183, 'end' => 189], 28 => ['start' => 190, 'end' => 196],
    29 => ['start' => 197, 'end' => 203], 30 => ['start' => 204, 'end' => 210],
];

$juzStatus = $juzStatus ?: array_fill(1, 30, 'belum');
?>

<div class="space-y-4" {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
    {{-- Legend --}}
    <div class="flex flex-wrap gap-3 text-sm">
        @foreach ($defaultStatus as $key => $class)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $class }}">
                <span class="w-3 h-3 rounded"></span>
                {{ ucfirst($key) }}
            </span>
        @endforeach
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-{{ $columns }} gap-2 sm:grid-cols-{{ $columns }} lg:grid-cols-10 xl:grid-cols-10">
        @for ($i = 1; $i <= 30; $i++)
            @php
                $status = $juzStatus[$i] ?? 'belum';
                $cellClass = $defaultStatus[$status] ?? $defaultStatus['belum'];
                $clickClass = $clickable ? 'cursor-pointer hover:scale-105 transition-transform duration-fast' : '';
            @endphp
            <button
                type="button"
                class="aspect-square rounded-lg {{ $cellClass }} {{ $clickClass }} flex flex-col items-center justify-center text-center p-2 min-h-[60px] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                @if ($clickable) wire:click="onJuzClick({{ $i }})" @else @if ($onJuzClick) onclick="{{ $onJuzClick }}({{ $i }})" @endif @endif
                aria-label="Juz {{ $i }}: {{ ucfirst($status) }}"
            >
                <span class="font-semibold text-lg leading-tight">Juz {{ $i }}</span>
                <span class="text-xs opacity-80">{{ $status }}</span>
            </button>
        @endfor
    </div>
</div>

@push('scripts')
<script>
    // Keyboard navigation for grid
    document.addEventListener('keydown', function(e) {
        const buttons = document.querySelectorAll('[data-juz-grid] button');
        const current = document.activeElement;
        const index = Array.from(buttons).indexOf(current);
        if (index === -1) return;
        
        const cols = {{ $columns }};
        let nextIndex = index;
        
        if (e.key === 'ArrowRight') nextIndex = Math.min(index + 1, buttons.length - 1);
        if (e.key === 'ArrowLeft') nextIndex = Math.max(index - 1, 0);
        if (e.key === 'ArrowDown') nextIndex = Math.min(index + cols, buttons.length - 1);
        if (e.key === 'ArrowUp') nextIndex = Math.max(index - cols, 0);
        if (e.key === 'Home') nextIndex = 0;
        if (e.key === 'End') nextIndex = buttons.length - 1;
        
        if (nextIndex !== index) {
            e.preventDefault();
            buttons[nextIndex].focus();
        }
    });
</script>
@endpush