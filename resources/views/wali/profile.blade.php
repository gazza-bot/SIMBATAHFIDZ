@extends('layouts.app', ['title' => 'Profil Wali Santri', 'pageTitle' => 'Profil Wali'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-neutral-200 p-6 space-y-6 shadow-sm">
        <div class="flex items-center gap-4 pb-6 border-b border-neutral-100">
            <x-avatar :name="auth()->user()->name" size="lg" :photo="auth()->user()->foto ?? null" />
            <div>
                <h1 class="text-xl font-bold text-neutral-900">{{ auth()->user()->name }}</h1>
                <p class="text-xs text-neutral-500 capitalize">Wali Santri • Username: {{ auth()->user()->username }}</p>
            </div>
        </div>

        @php
            $wali = auth()->user()->wali;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-100">
                <span class="text-xs text-neutral-500 font-medium block">Nomor Telepon / WhatsApp</span>
                <span class="text-sm font-semibold text-neutral-800 mt-0.5 block">{{ $wali->telepon ?? '-' }}</span>
            </div>

            <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-100">
                <span class="text-xs text-neutral-500 font-medium block">Alamat Tinggal</span>
                <span class="text-sm font-semibold text-neutral-800 mt-0.5 block">{{ $wali->alamat ?? '-' }}</span>
            </div>
        </div>

        <div class="space-y-3 pt-2">
            <h2 class="text-sm font-bold text-neutral-900">Ananda Santri Terdaftar</h2>
            <div class="space-y-2">
                @forelse ($wali?->santris ?? [] as $anak)
                    <div class="p-3.5 rounded-xl border border-neutral-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$anak->nama" size="sm" :photo="$anak->foto ?? null" />
                            <div>
                                <p class="text-sm font-bold text-neutral-900">{{ $anak->nama }}</p>
                                <p class="text-xs text-neutral-500">NIS: {{ $anak->nis }} • Kelas {{ $anak->angkatan->nama ?? '-' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('wali.mutabaah', ['santri' => $anak->id]) }}"
                           class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                            Lihat Dasbor &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-neutral-500">Belum ada santri terhubung ke akun ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
