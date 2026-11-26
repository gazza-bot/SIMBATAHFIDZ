@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Peta Hafalan 30 Juz</h2>
                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">Cakupan ayat per juz (setoran ziyadah approved) per santri</p>
            </div>
        </div>

        {{-- Filter Form --}}
        <form method="GET" class="mt-4 flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="angkatan" class="block text-xs font-medium text-neutral-700 mb-1.5">Angkatan</label>
                <select name="angkatan" id="angkatan"
                    class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                    <option value="">Semua Angkatan</option>
                    @foreach ($angkatanList as $angkatan)
                        <option value="{{ $angkatan->id }}" {{ $angkatanId == $angkatan->id ? 'selected' : '' }}>
                            {{ $angkatan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="kelompok" class="block text-xs font-medium text-neutral-700 mb-1.5">Kelompok</label>
                <select name="kelompok" id="kelompok"
                    class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                    <option value="">Semua Kelompok</option>
                    @foreach ($kelompokList as $kelompok)
                        <option value="{{ $kelompok->id }}" {{ $kelompokId == $kelompok->id ? 'selected' : '' }}>
                            {{ $kelompok->nama }} (Ust. {{ $kelompok->ustadz->nama ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-auto">
                <x-button type="submit" variant="primary" class="min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.293V4z"/></svg>
                    Filter
                </x-button>
            </div>
        </form>
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-3 h-3 rounded-md bg-success-500 inline-block"></span>
                Mutqin (100%)
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-3 h-3 rounded-md bg-accent-500 inline-block"></span>
                Parsial (1-99%)
            </span>
            <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-3 h-3 rounded-md bg-neutral-200 inline-block"></span>
                Belum mulai
            </span>
        </div>
    </div>

    {{-- Santri List --}}
    @if (empty($santriMaps))
        <div class="bg-white rounded-2xl border border-neutral-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <p class="mt-3 text-neutral-500">Tidak ada santri pada filter ini.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($santriMaps as $map)
                <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-base font-mono flex-shrink-0">
                                {{ $map['santri']->progress_juz ?? 0 }}
                            </div>
                            <div>
                                <p class="text-base font-semibold text-neutral-900">{{ $map['santri']->nama }}</p>
                                <p class="text-xs text-neutral-500">
                                    NIS: {{ $map['santri']->nis }} &middot; {{ $map['santri']->angkatan->nama ?? '-' }} &middot; {{ $map['santri']->kelompok->nama ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs sm:text-sm">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-success-50 text-success-700 border border-success-200">
                                {{ $map['counts']['mutqin'] }} Mutqin
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-accent-50 text-accent-700 border border-accent-200">
                                {{ $map['counts']['parsial'] }} Parsial
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                                {{ $map['counts']['belum'] }} Belum
                            </span>
                        </div>
                    </div>

                    {{-- Grid 30 Juz --}}
                    <div class="grid grid-cols-6 sm:grid-cols-10 gap-1.5">
                        @for ($j = 1; $j <= 30; $j++)
                            @php
                                $st = $map['statusMap'][$j] ?? 'belum';
                                $pct = $map['percentages'][$j] ?? 0;
                                $bgClass = match($st) {
                                    'mutqin' => 'bg-success-500 text-white',
                                    'parsial' => 'bg-accent-500 text-neutral-900',
                                    default => 'bg-neutral-100 text-neutral-600',
                                };
                            @endphp
                            <div
                                class="aspect-square rounded-xl {{ $bgClass }} flex flex-col items-center justify-center text-center p-1.5 min-h-[48px]"
                                role="img"
                                aria-label="Juz {{ $j }}: {{ ucfirst($st) }} ({{ $pct }}%)"
                            >
                                <span class="text-[9px] uppercase font-semibold opacity-80 leading-none">Juz</span>
                                <span class="text-xs font-bold font-mono leading-tight mt-0.5">{{ $j }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection