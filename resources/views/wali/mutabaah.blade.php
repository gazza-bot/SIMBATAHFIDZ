@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
            <h2 class="text-xl sm:text-2xl font-bold">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm sm:text-base text-primary-100">Pantau mutaba'ah hafalan anak Anda.</p>
        </div>

        @forelse ($anakans as $anak)
            <div class="bg-white rounded-xl border border-neutral-200 p-5 flex items-center gap-4">
                <x-avatar :name="$anak->nama" size="lg" />
                <div class="flex-1 min-w-0">
                    <p class="text-base font-semibold text-neutral-900 truncate">{{ $anak->nama }}</p>
                    <p class="text-sm text-neutral-500">Kelompok {{ $anak->kelompok->nama ?? '-' }}</p>
                </div>
                <x-badge variant="info">{{ $anak->progress_percent }}%</x-badge>
            </div>
        @empty
            <x-alert variant="warning" title="Belum ada data anak">
                Akun wali ini belum terhubung ke data santri. Hubungi admin untuk menghubungkan anak Anda.
            </x-alert>
        @endforelse
    </div>
@endsection