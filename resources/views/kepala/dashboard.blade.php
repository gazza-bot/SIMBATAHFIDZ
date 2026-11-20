@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@php
    $ind = $indicators;
    $cap = $capaian;
    $deltaPositif = $ind['setoran_delta'] >= 0;
    $progressBar = fn (int $pct) => $pct >= 90 ? 'bg-success-500' : ($pct >= 50 ? 'bg-warning-500' : 'bg-error-500');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Hero --}}
    <section class="rounded-2xl border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-white p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-5">
            <div class="flex items-center gap-4">
                <x-avatar :name="auth()->user()->name" size="lg" />
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-900">Ahlan wa Sahlan, {{ auth()->user()->name }}</h2>
                    <p class="text-sm text-neutral-500">Kepala Divisi Tahfidz</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-neutral-200 px-3 py-2 text-xs font-medium text-neutral-600">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $hijriDate }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-neutral-200 px-3 py-2 text-xs font-medium text-neutral-600">
                    {{ $semester }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-success-50 border border-success-200 px-3 py-2 text-xs font-semibold text-success-700">
                    <span class="w-2 h-2 rounded-full bg-success-500" aria-hidden="true"></span>
                    {{ $ind['kelompok_aktif'] }}/{{ $ind['total_kelompok'] }} Halaqah Aktif
                </span>
            </div>
        </div>
    </section>

    {{-- Indikator Utama Hari Ini --}}
    <section aria-labelledby="indikator-title">
        <div class="flex items-center justify-between mb-3">
            <h3 id="indikator-title" class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Indikator Utama Hari Ini</h3>
            <span class="text-xs text-neutral-400">Diperbarui {{ now()->format('H:i') }} WIB</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri Hadir</p>
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-primary-50 text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm0 0a4 4 0 014 4v1h-8v-1a4 4 0 014-4z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $ind['hadir'] }}<span class="text-base font-medium text-neutral-400">/{{ $ind['total_santri'] }}</span></p>
                <p class="mt-1 text-xs text-neutral-500">{{ $ind['hadir_pct'] }}% tingkat hadir</p>
            </div>

            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Setoran Sesi</p>
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-info-50 text-info-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $ind['setoran_sesi'] }}<span class="text-base font-medium text-neutral-400"> santri</span></p>
                <p class="mt-1 text-xs font-medium {{ $deltaPositif ? 'text-success-600' : 'text-error-600' }}">
                    {{ $deltaPositif ? '+' : '' }}{{ $ind['setoran_delta'] }} vs kemarin
                </p>
            </div>

            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Indeks Mutu</p>
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-accent-50 text-accent-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ number_format($ind['indeks_mutu'], 1) }}</p>
                <p class="mt-1 text-xs text-neutral-500">{{ $ind['mutu_label'] }}</p>
            </div>

            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Asatidz Aktif</p>
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-success-50 text-success-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $ind['asatidz'] }}<span class="text-base font-medium text-neutral-400"> musyrif</span></p>
                <p class="mt-1 text-xs text-neutral-500">{{ $ind['kelompok_lengkap_pct'] }}% kelompok terisi</p>
            </div>
        </div>
    </section>

    {{-- Capaian Keseluruhan --}}
    <section aria-labelledby="capaian-title">
        <h3 id="capaian-title" class="text-sm font-semibold uppercase tracking-wide text-neutral-500 mb-3">Capaian Keseluruhan</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Santri Aktif</p>
                <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $cap['total_santri'] }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Rata-rata Capaian</p>
                <p class="mt-2 text-2xl font-bold text-primary-600">{{ number_format($cap['rata_capaian'], 1) }}<span class="text-base font-medium text-neutral-400"> juz</span></p>
            </div>
            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Mutqin 30 Juz</p>
                <p class="mt-2 text-2xl font-bold text-success-600">{{ $cap['mutqin30'] }}<span class="text-base font-medium text-neutral-400"> santri</span></p>
            </div>
            <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Perlu Perhatian</p>
                <p class="mt-2 text-2xl font-bold {{ $cap['perlu_perhatian'] > 0 ? 'text-error-600' : 'text-neutral-900' }}">{{ $cap['perlu_perhatian'] }}<span class="text-base font-medium text-neutral-400"> santri</span></p>
            </div>
        </div>
    </section>

    {{-- Pemantauan Halaqah --}}
    <section aria-labelledby="halaqah-title">
        <div class="flex items-center justify-between mb-3">
            <h3 id="halaqah-title" class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Pemantauan Halaqah</h3>
            <span class="text-xs text-neutral-400">{{ $halaqahMonitor->count() }} kelompok</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse ($halaqahMonitor as $item)
                <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-neutral-900">{{ $item['kelompok']->nama }}</p>
                            <p class="text-xs text-neutral-500">{{ $item['kelompok']->ustadz?->nama ?? 'Belum ada musyrif' }} &middot; {{ $item['kelompok']->angkatan?->nama ?? '-' }}</p>
                        </div>
                        <x-badge :variant="$item['status_variant']" size="sm" dot>{{ $item['status_label'] }}</x-badge>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-neutral-500 mb-1">
                            <span>Progress setoran hari ini</span>
                            <span class="font-semibold text-neutral-700">{{ $item['sudah_setor'] }}/{{ $item['total'] }} ({{ $item['progress_pct'] }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $progressBar($item['progress_pct']) }}" style="width: {{ $item['progress_pct'] }}%"></div>
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg bg-neutral-50 px-2 py-2">
                            <dt class="text-[11px] text-neutral-500">Rata mutu</dt>
                            <dd class="text-sm font-semibold text-neutral-900">{{ number_format($item['mutu'], 0) }}</dd>
                        </div>
                        <div class="rounded-lg bg-neutral-50 px-2 py-2">
                            <dt class="text-[11px] text-neutral-500">Rata juz</dt>
                            <dd class="text-sm font-semibold text-neutral-900">{{ number_format($item['rata_juz'], 1) }}</dd>
                        </div>
                        <div class="rounded-lg bg-neutral-50 px-2 py-2">
                            <dt class="text-[11px] text-neutral-500">Kehadiran</dt>
                            <dd class="text-sm font-semibold text-neutral-900">{{ $item['kehadiran_pct'] }}%</dd>
                        </div>
                    </dl>
                </div>
            @empty
                <div class="lg:col-span-2 rounded-2xl bg-white border border-neutral-200 p-8 text-center">
                    <p class="text-neutral-500">Belum ada kelompok halaqah terdaftar.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Peta Hafalan 30 Juz --}}
    <section aria-labelledby="peta-title">
        <div class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h3 id="peta-title" class="text-base font-semibold text-neutral-900">Peta Hafalan 30 Juz</h3>
                <div class="flex flex-wrap gap-1 bg-neutral-100 rounded-lg p-1" role="tablist" aria-label="Filter angkatan">
                    <a href="{{ route('kepala.dashboard') }}"
                       class="inline-flex items-center min-h-[44px] px-3 py-2 text-xs font-medium rounded-md {{ !$selectedAngkatan ? 'bg-white text-primary-600 shadow-sm' : 'text-neutral-600 hover:text-neutral-900' }}"
                       @if (! $selectedAngkatan) aria-current="page" @endif>
                        Semua
                    </a>
                    @foreach ($angkatanList as $angkatan)
                        <a href="{{ route('kepala.dashboard', ['angkatan' => $angkatan->id]) }}"
                           class="inline-flex items-center min-h-[44px] px-3 py-2 text-xs font-medium rounded-md {{ $selectedAngkatan === $angkatan->id ? 'bg-white text-primary-600 shadow-sm' : 'text-neutral-600 hover:text-neutral-900' }}"
                           @if ($selectedAngkatan === $angkatan->id) aria-current="page" @endif>
                            {{ $angkatan->nama }}
                        </a>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-neutral-500 mb-4">Persentase santri yang telah menuntaskan hafalan tiap juz (setoran ziyadah cover penuh).</p>

            <x-peta-juz variant="percent" :percentages="$juzCoverage" :columns="6" />

            <div class="mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl bg-neutral-50 border border-neutral-100 px-4 py-3">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-lg font-bold text-neutral-900">{{ number_format($coverageFooter['rata_juz'], 1) }} juz</p>
                        <p class="text-xs text-neutral-500">Rata-rata capaian</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-neutral-900">{{ $coverageFooter['siap_khatam'] }} santri</p>
                        <p class="text-xs text-neutral-500">Siap khatam 30 juz</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-neutral-900">{{ $coverageFooter['jumlah_santri'] }} santri</p>
                        <p class="text-xs text-neutral-500">Dalam cakupan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Aktivitas + Perlu Perhatian --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Aktivitas Setoran --}}
        <section class="lg:col-span-2 rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5" aria-labelledby="aktivitas-title" data-table-container>
            <div class="flex items-center justify-between mb-4">
                <h3 id="aktivitas-title" class="text-base font-semibold text-neutral-900">Aktivitas Setoran &amp; Tasmi' Terkini</h3>
                <span class="text-xs text-neutral-400">{{ $activities->count() }} terakhir</span>
            </div>
            <div class="relative">
                <div class="table-loading-overlay absolute inset-0 z-10 items-center justify-center bg-white/70" aria-hidden="true">
                    <span class="text-sm text-neutral-500">Memuat...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" role="grid" aria-label="Aktivitas setoran terkini">
                        <thead class="bg-neutral-50 border-b border-neutral-200">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Waktu</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Materi</th>
                                <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Predikat</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Musyrif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse ($activities as $setoran)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <x-avatar :name="$setoran->santri->nama" size="xs" />
                                            <span class="text-sm font-medium text-neutral-900">{{ $setoran->santri->nama }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-neutral-500">
                                        {{ $setoran->tanggal->format('d M') }} &middot; {{ $setoran->created_at?->format('H:i') ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-neutral-600">
                                        {{ ucfirst($setoran->jenis) }} &middot; {{ $setoran->surah?->nama_latin ?? '-' }} {{ $setoran->ayat_range }} (Juz {{ $setoran->juz }})
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <x-badge variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'dhaif')) }}" size="xs">
                                            {{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-neutral-600">{{ $setoran->ustadz?->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-9 h-9 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <p class="text-sm text-neutral-500">Belum ada setoran tercatat.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Perlu Perhatian --}}
        <section class="rounded-2xl bg-white border border-neutral-200 p-4 sm:p-5" aria-labelledby="perhatian-title">
            <div class="flex items-center justify-between mb-4">
                <h3 id="perhatian-title" class="text-base font-semibold text-neutral-900">Perlu Perhatian</h3>
                <x-badge variant="{{ $attention->count() > 0 ? 'error' : 'success' }}" size="sm">{{ $attention->count() }}</x-badge>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse ($attention as $item)
                    <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-neutral-900">{{ $item['santri']->nama }}</p>
                            <span class="text-xs text-neutral-500">Juz {{ $item['progress'] }}</span>
                        </div>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $item['kelompok'] ?? '-' }} &middot; {{ $item['ustadz'] ?? '-' }}</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($item['reasons'] as $reason)
                                <li class="inline-flex items-center gap-1 rounded-md bg-error-50 text-error-700 text-[11px] px-2 py-0.5 mr-1 mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error-500" aria-hidden="true"></span>
                                    {{ $reason }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="flex flex-col items-center gap-2 py-8 text-center">
                        <svg class="w-9 h-9 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-neutral-500">Tidak ada santri yang perlu perhatian.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Menuju Khatam --}}
    <section aria-labelledby="khatam-title">
        <h3 id="khatam-title" class="text-sm font-semibold uppercase tracking-wide text-neutral-500 mb-3">Santri Terdekat Menuju Khatam 30 Juz</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($nearKhatam as $item)
                <div class="rounded-2xl bg-white border border-neutral-200 p-4">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$item['santri']->nama" size="sm" />
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ $item['santri']->nama }}</p>
                            <p class="text-xs text-neutral-500">{{ $item['santri']->kelompok?->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-neutral-500">
                        <span>Juz {{ $item['progress'] }} / 30</span>
                        <span class="font-semibold text-neutral-700">{{ (int) round(($item['progress'] / 30) * 100) }}%</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-neutral-100 overflow-hidden">
                        <div class="h-full rounded-full bg-primary-500" style="width: {{ (int) round(($item['progress'] / 30) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-4 rounded-2xl bg-white border border-neutral-200 p-8 text-center">
                    <p class="text-neutral-500">Belum ada data capaian santri.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Quote --}}
    <footer class="rounded-2xl border border-neutral-100 bg-white px-4 py-6 text-center">
        <p class="text-sm text-neutral-600 italic">&ldquo;Sebaik-baik kalian adalah orang yang belajar Al-Qur'an dan mengajarkannya.&rdquo;</p>
        <p class="mt-1 text-xs text-neutral-400">HR. Bukhari No. 5027</p>
    </footer>
</div>
@endsection
