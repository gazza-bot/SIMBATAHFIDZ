@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
            <h2 class="text-xl sm:text-2xl font-bold">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm sm:text-base text-primary-100">Pantau capaian hafalan seluruh santri dari satu tempat.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSantri }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Kelompok</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalKelompok }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Setoran</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSetoran }}</p>
            </div>
        </div>

        <x-alert variant="info" title="Sprint 0 - Fondasi">
            Angka di atas diambil langsung dari database (seed development). Fitur Peta 30 Juz & Murajaah Bulanan akan dibangun pada sprint berikutnya.
        </x-alert>
    </div>
@endsection