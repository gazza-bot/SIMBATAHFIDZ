@extends('layouts.app', ['title' => 'Edit Setoran', 'pageTitle' => 'Edit Setoran'])

@section('content')
<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-col gap-3">
        <x-breadcrumb :items="[
            ['label' => 'Dasbor Halaqah', 'url' => route('ustadz.dashboard')],
            ['label' => 'Input Setoran', 'url' => route('ustadz.setorans.index')],
            ['label' => 'Edit Setoran'],
        ]" />
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-neutral-900 sm:text-2xl">Edit Setoran Hafalan</h1>
                <p class="mt-1 text-sm text-neutral-500">Perbarui bacaan atau penilaian, lalu simpan perubahan.</p>
            </div>
            <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat detail
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-start">
        <div class="space-y-4 lg:col-span-5">
            @if ($selectedSantri && $santriContext)
                @include('ustadz.setorans.partials.santri-context', [
                    'selectedSantri' => $selectedSantri,
                    'santriContext' => $santriContext,
                ])
            @endif

            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-level-1 sm:p-8">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Setoran hari ini</p>
                        <p class="mt-1 text-lg font-bold text-neutral-900">{{ $todayStats['sudah_setor'] }} dari {{ $todayStats['total_santri'] }} santri</p>
                    </div>
                    <span class="rounded-lg bg-primary-50 px-2.5 py-1 text-sm font-semibold text-primary-700">{{ $todayStats['percent'] }}%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-neutral-200">
                    <div class="h-full rounded-full bg-primary-500" style="width: {{ $todayStats['percent'] }}%"></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            @include('ustadz.setorans.partials.form', [
                'mode' => 'edit',
                'action' => route('ustadz.setorans.update', $setoran),
                'setoran' => $setoran,
                'kelompoks' => $kelompoks,
                'selectedSantri' => $selectedSantri,
                'juzSurahMap' => $juzSurahMap,
            ])
        </div>
    </div>
</div>
@endsection
