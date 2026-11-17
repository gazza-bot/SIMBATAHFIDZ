@extends('layouts.app', ['title' => 'Setoran Tersimpan', 'pageTitle' => 'Setoran Berhasil'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="text-center py-12">
        <div class="w-20 h-20 mx-auto mb-4 bg-success-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-neutral-900">Setoran Tersinkron</h2>
        <p class="mt-2 text-neutral-600">Data hafalan telah disimpan dan menunggu validasi.</p>
    </div>

    <x-card class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                <p class="font-medium text-neutral-900">{{ $setoran->santri->nama ?? '-' }}</p>
                <p class="text-sm text-neutral-500">{{ $setoran->santri->nis ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Jenis</p>
                <x-badge variant="{{ $setoran->jenis == 'ziyadah' ? 'info' : ($setoran->jenis == 'murajaah' ? 'secondary' : 'primary') }}" size="sm">
                    {{ ucfirst($setoran->jenis) }}
                </x-badge>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Materi</p>
                <p class="font-medium text-neutral-900">{{ $setoran->surah->nama_latin ?? '-' }}</p>
                <p class="text-sm text-neutral-500">{{ $setoran->ayat_range }} | Juz {{ $setoran->juz }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Predikat</p>
                <x-badge
                    variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                    size="sm"
                >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Skor</p>
                <p class="font-medium text-neutral-900">{{ $setoran->skor ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Tuntas</p>
                <p class="font-medium text-neutral-900">{{ $setoran->tuntas_lanjut ? 'Ya, lanjut ayat berikutnya' : 'Belum' }}</p>
            </div>
        </div>

        <div class="pt-4 border-t border-neutral-100">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Catatan Musyrif</p>
            <p class="text-neutral-700 whitespace-pre-wrap">{{ $setoran->catatan ?: '-' }}</p>
        </div>

        <div class="pt-4 border-t border-neutral-100">
            <x-badge variant="success" size="sm" dot>Tersinkron</x-badge>
            <span class="text-sm text-neutral-500 ml-2">Notifikasi terkirim ke wali</span>
        </div>
    </x-card>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <x-button variant="primary" href="{{ route('ustadz.setorans.create') }}" size="lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Setoran Lainnya
        </x-button>
        <x-button variant="outline" href="{{ route('ustadz.dashboard') }}" size="lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dasbor
        </x-button>
        <x-button variant="outline" href="{{ route('ustadz.rekap.index') }}" size="lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Lihat Rekap Halaqah
        </x-button>
    </div>
</div>
@endsection