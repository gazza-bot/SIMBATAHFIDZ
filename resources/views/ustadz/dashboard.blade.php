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

        <x-alert variant="info" title="Sprint 0 - Fondasi">
            Angka di atas diambil langsung dari database (seed development). Fitur Input Setoran & Absensi akan dibangun pada sprint berikutnya.
        </x-alert>
    </div>
@endsection