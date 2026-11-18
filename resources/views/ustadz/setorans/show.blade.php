@extends('layouts.app', ['title' => 'Detail Setoran', 'pageTitle' => 'Detail Setoran'])

@section('content')
@php
    $nilaiVariant = match ($setoran->nilai) {
        'mumtaz' => 'mumtaz',
        'jayyid_jiddan' => 'jayyid_jiddan',
        'jayyid' => 'jayyid',
        default => 'error',
    };
@endphp

<div class="mx-auto max-w-3xl space-y-5">
    <div class="flex flex-col gap-3">
        <x-breadcrumb :items="[
            ['label' => 'Dasbor Halaqah', 'url' => route('ustadz.dashboard')],
            ['label' => 'Input Setoran', 'url' => route('ustadz.setorans.index')],
            ['label' => 'Detail Setoran'],
        ]" />
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-neutral-900 sm:text-2xl">Detail Setoran Hafalan</h1>
                <p class="mt-1 text-sm text-neutral-500">Catatan setoran dan penilaian musyrif.</p>
            </div>
            <x-button variant="outline" size="sm" href="{{ route('ustadz.setorans.edit', $setoran) }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit setoran
            </x-button>
        </div>
    </div>

    <div class="rounded-2xl border border-neutral-200 bg-white shadow-level-1">
        <div class="flex items-start justify-between gap-3 border-b border-neutral-100 p-6 sm:p-8">
            <div class="flex min-w-0 items-start gap-3">
                <x-avatar :name="$setoran->santri->nama ?? 'Santri'" size="lg" :photo="$setoran->santri->foto ?? null" />
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-bold text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</h2>
                    <p class="mt-0.5 text-sm text-neutral-500">NIS {{ $setoran->santri->nis ?? '-' }}</p>
                    <p class="mt-0.5 text-sm text-neutral-600">
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
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Tanggal setoran</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->tanggal->format('d/m/Y') }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Jenis setoran</p>
                <p class="mt-1"><x-badge variant="{{ $setoran->jenis === 'ziyadah' ? 'info' : ($setoran->jenis === 'murajaah' ? 'secondary' : 'primary') }}" size="sm">{{ $setoran->jenis_label }}</x-badge></p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5 sm:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Bacaan</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">QS. {{ $setoran->surah->nama_latin ?? '-' }}</p>
                <p class="mt-0.5 text-sm text-neutral-600">{{ $setoran->ayat_range }} | Juz {{ $setoran->juz }} | {{ $setoran->jumlah_ayat }} ayat</p>
                @if ($setoran->is_cover_penuh)
                    <x-badge variant="success" size="xs" class="mt-2">Cover penuh surah</x-badge>
                @endif
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Skor angka</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->skor !== null ? $setoran->skor . ' / 100' : 'Tidak diisi' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Tuntas lanjut</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->tuntas_lanjut ? 'Ya, lanjut ayat berikutnya' : 'Belum ditandai' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5 sm:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Musyrif</p>
                <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $setoran->ustadz->user->name ?? $setoran->ustadz->nama ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-100 p-5 sm:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500">Catatan musyrif</p>
                <p class="mt-1 whitespace-pre-line text-sm text-neutral-700">{{ $setoran->catatan ?: 'Tidak ada catatan.' }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-2 sm:flex-row sm:justify-between">
        <x-button variant="ghost" href="{{ route('ustadz.setorans.index') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke daftar
        </x-button>
        <x-button variant="primary" href="{{ route('ustadz.setorans.create', ['santri' => $setoran->santri_id]) }}">
            Catat setoran santri ini
        </x-button>
    </div>
</div>
@endsection
