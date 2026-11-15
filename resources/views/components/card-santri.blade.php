@props([
    'santri' => null,
    'variant' => 'mobile',
    'showActions' => true,
    'attributes' => [],
])

<?php
$variants = [
    'mobile' => 'flex flex-col sm:flex-row gap-4 p-4 sm:p-5 bg-white border border-neutral-200 rounded-2xl shadow-level-1',
    'desktop' => 'flex flex-col gap-4 p-5 sm:p-6 bg-white border border-neutral-200 rounded-2xl shadow-level-2',
];
$variantClass = $variants[$variant] ?? $variants['mobile'];
?>

<div class="{{ $variantClass }} {{ $attributes->get('class', '') }}">
    {{-- Avatar --}}
    <div class="flex-shrink-0">
        <x-avatar :name="$santri->nama ?? 'Santri'" size="lg" :photo="$santri->foto ?? null" />
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div>
                <h3 class="text-base font-semibold text-neutral-900 truncate">{{ $santri->nama ?? 'Nama Santri' }}</h3>
                <p class="text-sm text-neutral-500">{{ $santri->nis ?? 'NIS' }} • {{ $santri->kelompok->nama ?? 'Kelompok' }}</p>
            </div>
            @if ($santri->status_aktif)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700">
                    Aktif
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                    Nonaktif
                </span>
            @endif
        </div>

        {{-- Progress Juz --}}
        @if (isset($santri->progress_juz))
            <div class="mt-3">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-neutral-600">Juz Terakhir</span>
                    <span class="font-medium text-neutral-900">Juz {{ $santri->progress_juz }}</span>
                </div>
                <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full transition-all duration-normal" style="width: {{ $santri->progress_percent ?? 0 }}%"></div>
                </div>
                <p class="mt-1 text-xs text-neutral-500">{{ $santri->progress_percent ?? 0 }}% dari target</p>
            </div>
        @endif

        {{-- Badge Mutu --}}
        @if (isset($santri->mutu_terakhir))
            <div class="mt-3 flex items-center gap-2 flex-wrap">
                @php
                    $mutuColors = [
                        'mumtaz' => 'bg-green-50 text-green-700 border-green-200',
                        'jayyid_jiddan' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'jayyid' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'dhaif' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $mutuClass = $mutuColors[strtolower($santri->mutu_terakhir)] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mutuClass }}">
                    {{ ucfirst(str_replace('_', ' ', $santri->mutu_terakhir)) }}
                </span>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    @if ($showActions)
        <div class="flex flex-col sm:flex-row gap-2 pt-2 border-t border-neutral-100">
            <x-button variant="primary" size="sm" class="flex-1 sm:flex-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Setor
            </x-button>
            <x-button variant="outline" size="sm" class="flex-1 sm:flex-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Detail
            </x-button>
        </div>
    @endif
</div>