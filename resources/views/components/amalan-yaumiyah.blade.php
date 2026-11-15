@props([
    'items' => [],
    'completed' => 0,
    'total' => 4,
    'interactive' => false,
    'attributes' => [],
])

<?php
$defaultItems = [
    ['key' => 'shalat', 'label' => 'Shalat 5 Waktu', 'icon' => '🕌', 'description' => 'Subuh, Dzuhur, Ashar, Maghrib, Isya'],
    ['key' => 'dzikir', 'label' => 'Dzikir Pagi-Petang', 'icon' => '📿', 'description' => 'Setelah shalat fardhu'],
    ['key' => 'qiyam', 'label' => 'Qiyamul Lail', 'icon' => '🌙', 'description' => 'Tahajud & Witir'],
    ['key' => 'tilawah', 'label' => 'Tilawah Mandiri', 'icon' => '📖', 'description' => 'Membaca Al-Qur\'an'],
];

$items = $items ?: $defaultItems;
$completed = $completed ?? 0;
?>

<div class="space-y-3" {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-neutral-900">Amalan Yaumiyah</h3>
        <x-badge variant="primary" size="sm" dot>{{ $completed }}/{{ $total }} Tuntas</x-badge>
    </div>

    {{-- Items --}}
    @foreach ($items as $index => $item)
        @php
            $isCompleted = $completed > $index;
            $itemKey = $item['key'] ?? $index;
        @endphp
        <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-neutral-200 transition-colors {{ $isCompleted ? 'bg-success-50 border-success-200' : '' }}">
            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ $isCompleted ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-neutral-400' }}">
                @if ($isCompleted)
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                @else
                    {{ $item['icon'] ?? '' }}
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-neutral-900">{{ $item['label'] }}</p>
                @if (isset($item['description']))
                    <p class="text-xs text-neutral-500 mt-0.5">{{ $item['description'] }}</p>
                @endif
            </div>

            @if ($interactive)
                <button
                    type="button"
                    class="p-2 rounded-lg {{ $isCompleted ? 'bg-success-50 text-success-600' : 'bg-neutral-100 text-neutral-400 hover:bg-neutral-200' }} transition-colors"
                    wire:click="toggleAmalan('{{ $itemKey }}')"
                    aria-label="{{ $isCompleted ? 'Batalkan' : 'Tandai selesai' }} {{ $item['label'] }}"
                >
                    @if ($isCompleted)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
            @endif
        </div>
    @endforeach
</div>