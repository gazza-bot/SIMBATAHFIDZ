@extends('layouts.app', ['title' => 'Detail Setoran', 'pageTitle' => 'Detail Setoran'])

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </x-button>
            <x-button variant="outline" size="sm" href="{{ route('ustadz.setorans.edit', $setoran) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </x-button>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-level-1 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-neutral-100 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900">Detail Setoran</h2>
                    <p class="mt-1 text-sm text-neutral-500">Informasi lengkap setoran hafalan.</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-6">
                {{-- Santri Info --}}
                <div class="flex items-center gap-4 p-4 bg-neutral-50 rounded-xl">
                    <x-avatar :name="$setoran->santri->nama ?? 'Santri'" size="lg" />
                    <div>
                        <p class="font-semibold text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</p>
                        <p class="text-sm text-neutral-500">NIS: {{ $setoran->santri->nis ?? '-' }}</p>
                    </div>
                </div>

                {{-- Detail Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Tanggal Setoran</p>
                        <p class="mt-1 text-sm font-medium text-neutral-900">{{ $setoran->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Jenis</p>
                        <p class="mt-1">
                            <x-badge variant="{{ $setoran->jenis == 'ziyadah' ? 'info' : 'secondary' }}" size="sm">
                                {{ $setoran->jenis == 'ziyadah' ? 'Ziyadah (Hafalan Baru)' : 'Murajaah (Mengulang)' }}
                            </x-badge>
                        </p>
                    </div>
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Surah</p>
                        <p class="mt-1 text-sm font-medium text-neutral-900">{{ $setoran->surah->nama_latin ?? '-' }}</p>
                        <p class="text-xs text-neutral-500">{{ $setoran->surah->nama_arab ?? '' }} (Juz {{ $setoran->juz }})</p>
                    </div>
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Ayat</p>
                        <p class="mt-1 text-sm font-medium text-neutral-900">
                            @if ($setoran->ayat_awal == $setoran->ayat_akhir)
                                Ayat {{ $setoran->ayat_awal }}
                            @else
                                Ayat {{ $setoran->ayat_awal }} - {{ $setoran->ayat_akhir }}
                            @endif
                        </p>
                        @if ($setoran->ayat_awal == 1 && $setoran->ayat_akhir == ($setoran->surah->jumlah_ayat ?? 0))
                            <x-badge variant="success" size="xs" class="mt-1">Cover Penuh (Mutqin)</x-badge>
                        @endif
                    </div>
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Nilai</p>
                        <p class="mt-1">
                            <x-badge
                                variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                                size="sm"
                            >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
                        </p>
                    </div>
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Ustadz Musyrif</p>
                        <p class="mt-1 text-sm font-medium text-neutral-900">{{ $setoran->ustadz->user->name ?? $setoran->ustadz->nama ?? '-' }}</p>
                    </div>
                </div>

                {{-- Catatan --}}
                @if ($setoran->catatan)
                    <div class="p-4 border border-neutral-100 rounded-xl">
                        <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Catatan</p>
                        <p class="mt-1 text-sm text-neutral-700 whitespace-pre-line">{{ $setoran->catatan }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection