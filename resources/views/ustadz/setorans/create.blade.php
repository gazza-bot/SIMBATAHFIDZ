@extends('layouts.app', ['title' => 'Input Setoran', 'pageTitle' => 'Input Setoran'])

@section('content')
@php
    $adaSantri = $kelompoks->contains(fn ($kelompok) => $kelompok->santris->isNotEmpty());
@endphp

<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-col gap-3">
        <x-breadcrumb :items="[
            ['label' => 'Dasbor Halaqah', 'url' => route('ustadz.dashboard')],
            ['label' => 'Input Setoran'],
        ]" />
        <div>
            <h1 class="text-xl font-bold text-neutral-900 sm:text-2xl">Catat Setoran Hafalan dan Murajaah</h1>
            <p class="mt-1 max-w-2xl text-sm text-neutral-500">Pilih santri, isi bacaan, lalu simpan penilaian setoran.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-start">
        <div class="space-y-4 lg:col-span-5">
            <div class="rounded-2xl border border-neutral-200 bg-white p-8 shadow-level-1 sm:p-8">
                <form method="GET" action="{{ route('ustadz.setorans.create') }}">
                    <label for="santri_picker" class="mb-1.5 block text-sm font-medium text-neutral-700">Ganti santri halaqah</label>
                    <select
                        id="santri_picker"
                        name="santri"
                        class="min-h-11 w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2"
                        onchange="this.form.submit()"
                        @if (! $adaSantri) disabled @endif
                    >
                        <option value="">Pilih santri</option>
                        @foreach ($kelompoks as $kelompok)
                            @if ($kelompok->santris->isNotEmpty())
                                <optgroup label="{{ $kelompok->nama }}">
                                    @foreach ($kelompok->santris as $santri)
                                        <option value="{{ $santri->id }}" @selected($selectedSantri?->id === $santri->id)>
                                            {{ $santri->nama }} ({{ $santri->nis }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit" class="mt-2 inline-flex min-h-11 items-center rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>
                    </noscript>
                </form>
            </div>

            @if ($selectedSantri && $santriContext)
                @include('ustadz.setorans.partials.santri-context', [
                    'selectedSantri' => $selectedSantri,
                    'santriContext' => $santriContext,
                ])
            @else
                <x-card padding="lg">
                    <div class="py-4 text-center">
                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm0 0a4 4 0 014 4v1h-8v-1a4 4 0 014-4z"/>
                        </svg>
                        <p class="mt-3 text-sm font-medium text-neutral-700">Belum ada santri aktif</p>
                        <p class="mt-1 text-sm text-neutral-500">Hubungi admin untuk menambahkan santri ke kelompok Anda.</p>
                    </div>
                </x-card>
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
                <p class="mt-2 text-xs text-neutral-500">
                    {{ $todayStats['belum_setor'] > 0 ? $todayStats['belum_setor'] . ' santri belum menyetor hari ini.' : 'Semua santri sudah menyetor hari ini.' }}
                </p>
            </div>
        </div>

        <div class="lg:col-span-7">
            @if ($selectedSantri)
                @include('ustadz.setorans.partials.form', [
                    'mode' => 'create',
                    'action' => route('ustadz.setorans.store'),
                    'setoran' => null,
                    'kelompoks' => $kelompoks,
                    'selectedSantri' => $selectedSantri,
                    'juzSurahMap' => $juzSurahMap,
                ])
            @endif
        </div>
    </div>
</div>
@endsection
