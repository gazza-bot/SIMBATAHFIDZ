@extends('layouts.app', ['title' => 'Setoran Tersimpan', 'pageTitle' => 'Setoran Tersimpan'])

@section('content')
@php
    $nilaiVariant = match ($setoran->nilai) {
        'mumtaz' => 'mumtaz',
        'jayyid_jiddan' => 'jayyid_jiddan',
        'jayyid' => 'jayyid',
        default => 'error',
    };
@endphp

<div class="mx-auto max-w-2xl space-y-5">
    <div class="rounded-2xl border border-neutral-200 bg-white p-6 text-center shadow-level-1 sm:p-8">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-50">
            <svg class="h-9 w-9 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <x-badge variant="success" size="sm" dot class="mt-4">Tersimpan</x-badge>
        <h1 class="mt-3 text-xl font-bold text-neutral-900 sm:text-2xl">Alhamdulillah, setoran berhasil disimpan</h1>
        <p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-neutral-500">
            Catatan hafalan santri sudah masuk ke sistem dan langsung tercatat pada progres hafalan.
        </p>
        <p class="mt-2 text-xs text-neutral-500">Disimpan {{ $setoran->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="rounded-2xl border border-neutral-200 bg-white shadow-level-1">
        <div class="flex flex-col gap-3 border-b border-neutral-100 p-6 sm:flex-row sm:items-start sm:justify-between sm:p-8">
            <div class="flex min-w-0 items-start gap-3">
                <x-avatar :name="$setoran->santri->nama ?? 'Santri'" size="lg" :photo="$setoran->santri->foto ?? null" />
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Santri</p>
                    <h2 class="truncate text-lg font-bold text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</h2>
                    <p class="mt-0.5 text-sm text-neutral-500">
                        {{ $setoran->santri->kelompok->nama ?? '-' }}
                        @if ($setoran->santri->angkatan)
                            <span class="text-neutral-400">|</span> {{ $setoran->santri->angkatan->nama }}
                        @endif
                    </p>
                </div>
            </div>
            <x-badge :variant="$nilaiVariant" size="md">{{ $setoran->nilai_label }}</x-badge>
        </div>

        <div class="grid grid-cols-1 gap-3 p-6 sm:grid-cols-2 sm:p-8">
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Jenis hafalan</p>
                <p class="mt-1"><x-badge variant="{{ $setoran->jenis === 'ziyadah' ? 'info' : ($setoran->jenis === 'murajaah' ? 'secondary' : 'primary') }}" size="sm">{{ $setoran->jenis_label }}</x-badge></p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Predikat</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->nilai_label }}{{ $setoran->skor !== null ? ' | Skor ' . $setoran->skor : '' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5 sm:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Detail bacaan</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">QS. {{ $setoran->surah->nama_latin ?? '-' }} (Juz {{ $setoran->juz }})</p>
                <p class="mt-0.5 text-sm text-neutral-600">{{ $setoran->ayat_range }} | {{ $setoran->jumlah_ayat }} ayat</p>
                @if ($setoran->is_cover_penuh)
                    <x-badge variant="success" size="xs" class="mt-2">Cover penuh surah</x-badge>
                @endif
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Tuntas lanjut</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->tuntas_lanjut ? 'Ya, lanjut ayat berikutnya' : 'Belum ditandai' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Musyrif</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->ustadz->user->name ?? $setoran->ustadz->nama ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5 sm:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Catatan musyrif</p>
                <p class="mt-1 whitespace-pre-line text-sm text-neutral-700">{{ $setoran->catatan ?: 'Tidak ada catatan.' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
        <x-button variant="primary" size="lg" href="{{ route('ustadz.setorans.create', ['santri' => $setoran->santri_id]) }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat setoran santri ini
        </x-button>
        <x-button variant="secondary" size="lg" href="{{ route('ustadz.setorans.create') }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
            Catat santri lain
        </x-button>
        <x-button variant="outline" size="md" href="{{ route('ustadz.dashboard') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Kembali ke dasbor
        </x-button>
        <x-button variant="ghost" size="md" href="{{ route('ustadz.rekap.index') }}">
            Lihat rekap halaqah
        </x-button>
    </div>
</div>
@endsection
