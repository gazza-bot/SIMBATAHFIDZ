@php
    $maxCount = collect($juzRanges)->max('count') ?? 0;
    $adaData = $coverageFooter['jumlah_santri'] > 0;
@endphp

@if (! $adaData)
    <div class="flex flex-col items-center gap-2 py-10 text-center">
        <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <p class="text-sm text-neutral-500">Belum ada santri pada cakupan ini.</p>
    </div>
@else
    <div class="grid grid-cols-6 gap-1.5 sm:gap-3" role="list" aria-label="Distribusi progres hafalan per rentang 5 juz">
        @foreach ($juzRanges as $range)
            @php
                $barHeight = $maxCount > 0 && $range['count'] > 0
                    ? max(4, (int) round(($range['count'] / $maxCount) * 100))
                    : 0;
            @endphp
            <div class="flex flex-col items-center" role="listitem" aria-label="{{ $range['label'] }}: {{ $range['count'] }} santri">
                <span class="mb-1 text-[11px] sm:text-xs font-semibold text-neutral-700">{{ $range['count'] }}</span>
                <div class="relative w-full h-40 sm:h-52 rounded-md bg-neutral-50 overflow-hidden flex items-end" aria-hidden="true">
                    <div class="w-full rounded-t-md {{ $range['count'] > 0 ? 'bg-primary-500' : 'bg-transparent' }}" style="height: {{ $barHeight }}%"></div>
                </div>
                <span class="mt-2 text-[10px] sm:text-xs text-neutral-500 text-center leading-tight">{{ $range['label'] }}</span>
            </div>
        @endforeach
    </div>

    @if ($belumMulai > 0)
        <p class="mt-3 text-xs text-neutral-500">Belum mulai (progres 0): <span class="font-semibold text-neutral-700">{{ $belumMulai }} santri</span></p>
    @endif
@endif

<div class="mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl bg-neutral-50 border border-neutral-100 px-4 py-3">
    <div class="flex items-center gap-6">
        <div>
            <p class="text-lg font-bold text-neutral-900">{{ number_format($coverageFooter['rata_juz'], 1) }} juz</p>
            <p class="text-xs text-neutral-500">Rata-rata capaian</p>
        </div>
        <div>
            <p class="text-lg font-bold text-neutral-900">{{ $coverageFooter['siap_khatam'] }} santri</p>
            <p class="text-xs text-neutral-500">Siap khatam 30 juz</p>
        </div>
        <div>
            <p class="text-lg font-bold text-neutral-900">{{ $coverageFooter['jumlah_santri'] }} santri</p>
            <p class="text-xs text-neutral-500">Dalam cakupan</p>
        </div>
    </div>
</div>
