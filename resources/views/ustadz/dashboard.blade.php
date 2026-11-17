@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
            <h2 class="text-xl sm:text-2xl font-bold">Assalamu'alaikum, Ustadz {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm sm:text-base text-primary-100">Pantau setoran dan absensi kelompok halaqah Anda.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSantri }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Setoran</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSetoran }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Menunggu Validasi</p>
                <p class="mt-2 text-2xl font-bold text-warning-600">{{ $totalMenunggu }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-button href="{{ route('ustadz.setorans.index') }}" variant="primary" size="lg" fullWidth>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Input Setoran
            </x-button>
            <x-button href="{{ route('ustadz.setorans.validasi') }}" variant="outline" size="lg" fullWidth>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Validasi Setoran
            </x-button>
        </div>

        <x-alert variant="info" title="Sprint 2 - Input Setoran">
            Fitur input setoran hafalan dan validasi sudah aktif. Kelola setoran santri di kelompok Anda melalui menu di atas.
        </x-alert>
    </div>
@endsection