@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
            <h2 class="text-xl sm:text-2xl font-bold">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm sm:text-base text-primary-100">Kelola data master SIMBA TAHFIDZ dari satu tempat.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalSantri }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Ustadz</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalUstadz }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Wali</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalWali }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Kelompok</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $totalKelompok }}</p>
            </div>
        </div>

        <x-alert variant="info" title="Sprint 0 - Fondasi">
            Data di atas diambil langsung dari database (seed development). Fitur Kelola Data Master akan dibangun pada sprint berikutnya.
        </x-alert>
    </div>
@endsection