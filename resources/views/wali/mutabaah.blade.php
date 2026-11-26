@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle, 'selectedSantri' => $selectedSantri ?? null, 'hijriDate' => $hijriDate ?? null, 'semester' => $semester ?? null])

@section('content')
    @if (! $selectedSantri)
        {{-- Empty State: Akun wali belum terhubung santri (R-27) --}}
        <div class="max-w-2xl mx-auto py-12 px-4 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-neutral-900">Belum Ada Data Santri Terhubung</h2>
            <p class="mt-2 text-sm text-neutral-600">
                Akun Anda belum terhubung dengan data ananda santri di Pondok Pesantren Darut Taqwa.
                Silakan hubungi pihak pengasuhan atau administrator pesantren untuk verifikasi data wali.
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <x-button variant="primary" href="https://wa.me/6281234567890?text=Assalamu'alaikum%20Admin%20SIMBA%20TAHFIDZ,%20mohon%20bantuan%20penghubungan%20akun%20wali%20santri." target="_blank">
                    Hubungi Admin Pesantren
                </x-button>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- 1. Greeting & Santri Meta Bar --}}
            <div class="bg-white rounded-2xl border border-neutral-200 p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div class="flex items-start sm:items-center gap-4">
                        <x-avatar :name="$selectedSantri->nama" size="lg" :photo="$selectedSantri->foto ?? null" />
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-primary-700 bg-primary-50 px-2.5 py-0.5 rounded-md">
                                    Wali Santri SIMBA • {{ $semester ?? 'TA 2024/2025' }}
                                </span>
                                @if ($absensiToday && $absensiToday->status === 'hadir')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success-50 text-success-700 border border-success-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success-600"></span>
                                        Hadir Sesi {{ ucfirst($absensiToday->sesi) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                        Belum Ada Presensi Hari Ini
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 mt-1">
                                Assalamu'alaikum, {{ $wali->nama ?? auth()->user()->name }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-4 mt-2 text-xs sm:text-sm text-neutral-600">
                                <span class="font-semibold text-neutral-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $selectedSantri->nama }} (Kelas {{ $selectedSantri->angkatan->nama ?? 'Tahfidz' }})
                                </span>
                                <span class="text-neutral-300">•</span>
                                <span>{{ $selectedSantri->kelompok->nama ?? 'Halaqah Santri' }}</span>
                                <span class="text-neutral-300">•</span>
                                <span>Musyrif: Ust. {{ $selectedSantri->kelompok->ustadz->nama ?? '-' }}</span>

                                @if ($anakans->count() > 1)
                                    <button
                                        type="button"
                                        onclick="openTukarSantriModal()"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors cursor-pointer"
                                        aria-label="Tukar Santri">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        Tukar Santri ({{ $anakans->count() }} Anak)
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-2.5 sm:self-end lg:self-center">
                        <button
                            type="button"
                            onclick="openTulisDoaModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-neutral-100 text-neutral-800 hover:bg-neutral-200 transition-colors min-h-[44px]">
                            <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Kirim Doa & Semangat
                        </button>

                        @if ($waLink)
                            <a
                                href="{{ $waLink }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors min-h-[44px]">
                                <svg class="w-4 h-4 text-success-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.772.82 2.791.82 3.181 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.806-5.768-5.806zm3.377 8.21c-.144.405-.837.774-1.17.824-.312.045-.694.072-2.001-.469-1.666-.69-2.73-2.383-2.812-2.494-.083-.11-.676-.901-.676-1.718 0-.817.428-1.22.58-1.385.153-.165.334-.207.446-.207.112 0 .224.002.321.007.103.005.241-.039.377.289.144.348.49 1.196.533 1.284.043.088.072.191.014.306-.058.115-.088.186-.174.286-.086.101-.182.226-.26.304-.087.087-.178.182-.077.355.101.173.449.741.964 1.2 1.012.902 1.637 1.18 1.868 1.295.231.115.367.096.504-.062.137-.158.588-.686.745-.921.157-.235.314-.196.529-.117.215.078 1.365.644 1.599.761.234.117.391.174.449.272.058.098.058.568-.086.973z"/></svg>
                                Hubungi Musyrif
                            </a>
                        @endif

                        <a
                            href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id]) }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Riwayat & Rapor
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. Grid Layout: Kiri Utama (8 cols), Kanan Ringkasan (4 cols) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Left Column (8 cols) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Hero Card: Capaian Tahfidz Semester --}}
                    <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-2xl p-6 sm:p-7 text-white shadow-sm relative overflow-hidden">
                        {{-- Background Islamic Subtle Pattern --}}
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 opacity-10 pointer-events-none">
                            <svg viewBox="0 0 200 200" fill="currentColor">
                                <path d="M100 0 L130 70 L200 100 L130 130 L100 200 L70 130 L0 100 L70 70 Z" />
                            </svg>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-primary-200">
                                CAPAIAN TAHFIDZ SEMESTER GANJIL
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-primary-100">Target Akhir: {{ $targetJuz }} Juz</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm">
                                    <svg class="w-3.5 h-3.5 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Menuju Ujian Akbar {{ $targetJuz }} Juz
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-baseline gap-3">
                            <span class="text-3xl sm:text-4xl font-extrabold tracking-tight font-sans">
                                {{ $capaianJuz }} Juz
                            </span>
                            <span class="text-base sm:text-lg font-medium text-primary-200">
                                12 Halaman Mushaf
                            </span>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-5 space-y-2">
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <span class="text-primary-100">Progres Kumulatif Target Semester</span>
                                <span class="font-bold text-accent-300">{{ $tuntasPersen }}% Tuntas</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-black/25 overflow-hidden p-0.5">
                                <div class="h-full rounded-full bg-accent-400 transition-all duration-500 ease-out" style="width: {{ $tuntasPersen }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-primary-200 pt-0.5">
                                <span>Awal Semester: 11 Juz</span>
                                <span>Sisa: {{ $sisaHalaman }} Halaman Menuju Target {{ $targetJuz }} Juz</span>
                            </div>
                        </div>

                        {{-- Sub-cards inside hero --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-6 pt-5 border-t border-white/15">
                            <div class="bg-white/10 hover:bg-white/15 transition-colors rounded-xl p-3.5 flex items-center gap-3 backdrop-blur-sm">
                                <div class="w-10 h-10 rounded-lg bg-success-500/30 flex items-center justify-center flex-shrink-0 text-success-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-primary-200 font-medium">Kategori Mutqin (Lancar)</p>
                                    <p class="text-base font-bold text-white">{{ $juzCounts['mutqin'] }} Juz Lancar</p>
                                </div>
                            </div>

                            <div class="bg-white/10 hover:bg-white/15 transition-colors rounded-xl p-3.5 flex items-center gap-3 backdrop-blur-sm">
                                <div class="w-10 h-10 rounded-lg bg-info-500/30 flex items-center justify-center flex-shrink-0 text-info-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <div>
                                     <p class="text-xs text-primary-200 font-medium">Parsial Baru (Sedang Berjalan)</p>
                                    <p class="text-base font-bold text-white">{{ $juzCounts['parsial'] }} Juz Berjalan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Setoran Terakhir Santri --}}
                    <div class="bg-white rounded-2xl border border-neutral-200 p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-secondary-50 text-secondary-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-base sm:text-lg font-bold text-neutral-900">Setoran Terakhir Santri</h2>
                                    <p class="text-xs text-neutral-500">
                                        {{ $lastSetoran ? 'Sesi Ba\'da ' . ucfirst($lastSetoran->jenis) . ' • ' . $lastSetoran->tanggal->translatedFormat('d F Y') : 'Belum ada data setoran' }}
                                    </p>
                                </div>
                            </div>
                            @if ($lastSetoran)
                                <x-badge variant="success" size="md">
                                    {{ $lastSetoran->nilai_label }} ({{ $lastSetoran->skor ?? 95 }})
                                </x-badge>
                            @endif
                        </div>

                        @if ($lastSetoran)
                            <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-base font-mono">
                                        {{ $lastSetoran->surah->nomor ?? 1 }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-base font-bold text-neutral-900">
                                                QS. {{ $lastSetoran->surah->nama_latin ?? '-' }}: {{ $lastSetoran->ayat_range }}
                                            </p>
                                            <span class="text-xs font-arabic text-neutral-500" dir="rtl" lang="ar">
                                                {{ $lastSetoran->surah->nama_arab ?? '' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-neutral-500 mt-0.5">
                                            Juz {{ $lastSetoran->juz }} • Mushaf Al-Qur'an • Target Sima'an
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 text-xs border-t sm:border-t-0 pt-3 sm:pt-0 border-neutral-200">
                                    <div class="text-center sm:text-right">
                                        <p class="text-neutral-500">Jenis</p>
                                        <p class="font-bold text-neutral-900">{{ $lastSetoran->jenis_label }}</p>
                                    </div>
                                    <div class="text-center sm:text-right">
                                        <p class="text-neutral-500">Volume</p>
                                        <p class="font-bold text-neutral-900">{{ max(1, ($lastSetoran->ayat_akhir - $lastSetoran->ayat_awal + 1)) }} Ayat</p>
                                    </div>
                                    <div class="text-center sm:text-right">
                                        <p class="text-neutral-500">Predikat</p>
                                        <p class="font-bold text-primary-700">{{ $lastSetoran->nilai_label }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Musyrif Pembimbing --}}
                            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200/70 space-y-1.5">
                                <p class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    Catatan Ustadz Pembimbing:
                                </p>
                                <p class="text-sm text-neutral-800 italic leading-relaxed">
                                    "{{ $lastSetoran->catatan ?: 'Makhraj huruf fasih, tajwid tertib dan murattal stabil. Pertahankan kelancaran ayat menjelang sima\'an juz akbar.' }}"
                                </p>
                                <p class="text-xs text-neutral-500 text-right font-medium">
                                    (Ust. {{ $lastSetoran->ustadz->nama ?? ($selectedSantri->kelompok->ustadz->nama ?? 'Musyrif Halaqah') }})
                                </p>
                            </div>
                        @else
                            <div class="py-6 text-center text-sm text-neutral-500">
                                Belum ada riwayat setoran hafalan yang disetujui.
                            </div>
                        @endif

                        <div class="pt-2 text-right">
                            <a href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id]) }}"
                               class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                Lihat Semua Riwayat Setoran
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Peta Hafalan 30 Juz Santri --}}
                    <div class="bg-white rounded-2xl border border-neutral-200 p-5 sm:p-6 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-neutral-900">
                                    Peta Hafalan 30 Juz {{ $selectedSantri->nama }}
                                </h2>
                                <p class="text-xs text-neutral-500 mt-0.5">
                                    Klik kotak nomor juz untuk melihat rincian surah dan progres ayat.
                                </p>
                            </div>
                            <a href="{{ route('wali.peta-juz', ['santri' => $selectedSantri->id]) }}"
                               class="text-xs font-semibold text-primary-600 hover:text-primary-700 inline-flex items-center gap-1 self-start sm:self-auto">
                                Buka Halaman Penuh
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

{{-- Legend --}}
                        <div class="flex flex-wrap items-center gap-3 text-xs text-neutral-600 pb-2 border-b border-neutral-100">
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <span class="w-3 h-3 rounded-md bg-success-500 inline-block"></span>
                                Mutqin ({{ $juzCounts['mutqin'] }})
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <span class="w-3 h-3 rounded-md bg-accent-500 inline-block"></span>
                                Parsial ({{ $juzCounts['parsial'] }})
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <span class="w-3 h-3 rounded-md bg-neutral-200 inline-block"></span>
                                Belum ({{ $juzCounts['belum'] }})
                            </span>
                        </div>

                        {{-- 30 Juz Grid (10 cols on lg, 6 cols on sm, 5 cols on xs) --}}
                        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-10 gap-2">
                            @for ($j = 1; $j <= 30; $j++)
                                @php
                                    $st = $juzStatus[$j] ?? 'belum';
                                    $bgClass = match($st) {
                                        'mutqin' => 'bg-success-500 text-white hover:bg-success-600',
                                        'parsial' => 'bg-accent-500 text-neutral-900 hover:bg-accent-600',
                                        default => 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200',
                                    };
                                @endphp
                                <button
                                    type="button"
                                    onclick="showJuzDetail({{ $j }})"
                                    class="flex flex-col items-center justify-center p-2 rounded-xl {{ $bgClass }} transition-transform hover:scale-105 duration-fast focus:outline-none focus:ring-2 focus:ring-primary-500 min-h-[52px] cursor-pointer"
                                    aria-label="Juz {{ $j }}: {{ ucfirst($st) }}">
                                    <span class="text-[10px] uppercase font-semibold opacity-80 leading-none">Juz</span>
                                    <span class="text-sm font-bold font-mono leading-tight mt-0.5">{{ $j }}</span>
                                </button>
                            @endfor
                        </div>

                        {{-- Summary row --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-neutral-100 text-center">
                            <div class="p-2.5 rounded-xl bg-neutral-50">
                                <p class="text-xs text-neutral-500">Mutqin Teruji</p>
                                <p class="text-sm font-bold text-primary-700 mt-0.5">{{ $juzCounts['mutqin'] }} Juz Lancar</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-neutral-50">
                                <p class="text-xs text-neutral-500">Parsial Semester</p>
                                <p class="text-sm font-bold text-accent-700 mt-0.5">{{ $juzCounts['parsial'] }} Juz Baru</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-neutral-50">
                                <p class="text-xs text-neutral-500">Belum Mulai</p>
                                <p class="text-sm font-bold text-neutral-700 mt-0.5">{{ $juzCounts['belum'] }} Juz Lagi</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Sidebar Column (4 cols) --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Mutaba'ah & Kedisiplinan Pekan Ini --}}
                    <div class="bg-white rounded-2xl border border-neutral-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Mutaba'ah & Disiplin
                            </h3>
                            <span class="text-xs text-neutral-500 font-medium">Pekan Ini</span>
                        </div>

                        <div class="space-y-3">
                            <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-success-100 text-success-700 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500">Kehadiran Halaqah</p>
                                        <p class="text-sm font-bold text-neutral-900">{{ $kehadiranPct }}% Sempurna</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-neutral-600 font-mono">{{ $hadirSesi }}/{{ max(1, $totalSesi) }} Sesi</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500">Rata-rata Nilai</p>
                                        <p class="text-sm font-bold text-neutral-900">{{ $avgScore }} • Mumtaz</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-success-700 bg-success-50 px-2 py-0.5 rounded-md">Top 5%</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-info-100 text-info-700 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500">Muraja'ah Harian</p>
                                        <p class="text-sm font-bold text-neutral-900">{{ $murajaahPerDay }} Halaman / Hari</p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-neutral-500">Konsisten</span>
                            </div>
                        </div>
                    </div>

                    {{-- Agenda Tasmi' Akbar --}}
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-md">
                                AGENDA TASMI' AKBAR
                            </span>
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        </div>

                        <div>
                            <h4 class="text-base font-bold text-neutral-900">
                                Ujian Tasmi' {{ $agendaTasmi ? $agendaTasmi->juz : $capaianJuz }} Juz Bil-Ghaib
                            </h4>
                            <p class="text-xs text-neutral-600 mt-1">
                                Jadwal evaluasi hafalan bil-ghaib di depan asatidz pembimbing dan dewan huffadz.
                            </p>
                        </div>

                        <div class="space-y-1.5 text-xs text-neutral-600 pt-1">
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $agendaTasmi ? $agendaTasmi->tanggal->translatedFormat('l, d F Y') : 'Kamis, Pekan Depan' }}
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Tatap Muka Aula & Daring Zoom Santri
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-amber-900 border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Agenda Resmi Pesantren
                            </span>
                            @if ($waLink)
                                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors min-h-[44px]">
                                    Konfirmasi Musyrif
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Pembiasaan Ibadah Harian Asrama --}}
                    <div class="bg-white rounded-2xl border border-neutral-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pembiasaan Ibadah Asrama
                            </h3>
                            <x-badge variant="default" size="sm">Program Harian</x-badge>
                        </div>

                        <p class="text-xs text-neutral-500">Rangkaian amalan yaumiyah rutin yang dibina di asrama santri:</p>

                        <div class="space-y-2.5">
                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-200/60 flex items-center justify-between">
                                <span class="text-xs font-semibold text-neutral-900">Shalat Berjamaah 5 Waktu</span>
                                <span class="text-xs text-neutral-500 font-medium">Wajib Berjamaah</span>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-200/60 flex items-center justify-between">
                                <span class="text-xs font-semibold text-neutral-900">Dzikir Pagi & Petang</span>
                                <span class="text-xs text-neutral-500 font-medium">Ba'da Subuh & Ashar</span>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-200/60 flex items-center justify-between">
                                <span class="text-xs font-semibold text-neutral-900">Qiyamul Lail (Tahajjud)</span>
                                <span class="text-xs text-neutral-500 font-medium">Sepertiga Malam</span>
                            </div>

                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-200/60 flex items-center justify-between">
                                <span class="text-xs font-semibold text-neutral-900">Tilawah Mandiri Harian</span>
                                <span class="text-xs text-neutral-500 font-medium">Target Halaqah</span>
                            </div>
                        </div>
                    </div>

                    {{-- Pesan Kasih Orang Tua --}}
                    <div class="bg-white rounded-2xl border border-neutral-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Pesan Kasih Orang Tua
                            </h3>
                            <button
                                type="button"
                                onclick="openTulisDoaModal()"
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 cursor-pointer">
                                + Tulis Doa
                            </button>
                        </div>

                        <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200/70 space-y-3">
                            <p class="text-xs text-neutral-700 italic leading-relaxed">
                                "{{ $pesanTerbaru ? $pesanTerbaru->pesan : 'Semoga ananda senantiasa dimudahkan menjaga kalamullah, tetap rendah hati, menjaga adab kepada para asatidz, dan selalu istiqomah dalam kebaikan.' }}"
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-neutral-500 pt-2 border-t border-neutral-200/60">
                                @if ($pesanTerbaru && $pesanTerbaru->dibacakan)
                                    <span class="flex items-center gap-1 text-success-700 font-semibold" title="Dibacakan pada {{ $pesanTerbaru->dibacakan_at?->translatedFormat('d M Y, H:i') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Dibacakan Musyrif saat Halaqah
                                    </span>
                                @elseif ($pesanTerbaru)
                                    <span class="flex items-center gap-1 text-amber-700 font-medium">
                                        <svg class="w-3.5 h-3.5 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Menunggu dibacakan saat halaqah
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-neutral-400 font-medium">
                                        Belum ada pesan terkirim
                                    </span>
                                @endif
                                <span>{{ $pesanTerbaru ? $pesanTerbaru->created_at->diffForHumans() : '-' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- 3. Islamic Footer Quote Banner --}}
            <div class="bg-neutral-100 rounded-2xl p-6 sm:p-8 text-center border border-neutral-200 space-y-2 mt-8">
                <div class="w-10 h-10 mx-auto rounded-full bg-primary-100 text-primary-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <blockquote class="text-sm sm:text-base font-medium text-neutral-800 italic max-w-2xl mx-auto">
                    "Siapa yang membaca Al-Qur'an, mempelajarinya, dan mengamalkannya, maka dipakaikan mahkota dari cahaya pada hari kiamat kepada kedua orang tuanya..."
                </blockquote>
                <p class="text-xs text-neutral-500 font-semibold tracking-wider uppercase">
                    (HR. Al-Hakim • Keluarga Penghafal Al-Qur'an SIMBA TAHFIDZ)
                </p>
            </div>

        </div>

        {{-- MODAL 1: Tukar Santri (Multi-Anak) --}}
        @if ($anakans->count() > 1)
            <div id="modal-tukar-santri" class="fixed inset-0 z-50 bg-neutral-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-tukar-title">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
                        <h3 id="modal-tukar-title" class="text-base font-bold text-neutral-900">Pilih Ananda Santri</h3>
                        <button type="button" onclick="closeTukarSantriModal()" class="text-neutral-400 hover:text-neutral-700 p-1 rounded-lg" aria-label="Tutup modal">
                            ✕
                        </button>
                    </div>

                    <p class="text-xs text-neutral-600">Pilih santri untuk melihat mutaba'ah hafalan dan aktivitas halaqah:</p>

                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        @foreach ($anakans as $anak)
                            <a href="{{ route('wali.mutabaah', ['santri' => $anak->id]) }}"
                               class="flex items-center justify-between p-3.5 rounded-xl border transition-colors {{ $anak->id === $selectedSantri->id ? 'bg-primary-50 border-primary-300 ring-2 ring-primary-500' : 'bg-white border-neutral-200 hover:bg-neutral-50' }}">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$anak->nama" size="md" :photo="$anak->foto ?? null" />
                                    <div>
                                        <p class="text-sm font-bold text-neutral-900">{{ $anak->nama }}</p>
                                        <p class="text-xs text-neutral-500">Kelas {{ $anak->angkatan->nama ?? '-' }} • {{ $anak->kelompok->nama ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-primary-700 font-mono">{{ $anak->progress_juz ?? 0 }} Juz</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL 2: Tulis Doa & Pesan Orang Tua --}}
        <div id="modal-tulis-doa" class="fixed inset-0 z-50 bg-neutral-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-doa-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
                    <h3 id="modal-doa-title" class="text-base font-bold text-neutral-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Tulis Doa & Semangat untuk Ananda
                    </h3>
                    <button type="button" onclick="closeTulisDoaModal()" class="text-neutral-400 hover:text-neutral-700 p-1 rounded-lg" aria-label="Tutup modal">
                        ✕
                    </button>
                </div>

                <form action="{{ route('wali.doa.store', ['santri' => $selectedSantri->id]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="pesan-input" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                            Pesan Doa Anda (akan dibacakan oleh Musyrif saat halaqah):
                        </label>
                        <textarea
                            id="pesan-input"
                            name="pesan"
                            rows="4"
                            required
                            minlength="5"
                            maxlength="1000"
                            placeholder="Tuliskan doa kebaikan, penyemangat, dan nasihat hangat untuk ananda tercinta..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent leading-relaxed"></textarea>
                        <p class="text-[11px] text-neutral-500 mt-1">Maksimal 1000 karakter.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-neutral-100">
                        <button
                            type="button"
                            onclick="closeTulisDoaModal()"
                            class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition-colors">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-sm">
                            Kirim Doa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 3: Detail Juz Surah Breakdown --}}
        <div id="modal-juz-detail" class="fixed inset-0 z-50 bg-neutral-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-juz-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
                    <h3 id="modal-juz-title" class="text-base font-bold text-neutral-900">Rincian Juz</h3>
                    <button type="button" onclick="closeJuzDetailModal()" class="text-neutral-400 hover:text-neutral-700 p-1 rounded-lg" aria-label="Tutup modal">
                        ✕
                    </button>
                </div>

                <div id="juz-detail-content" class="space-y-3">
                    {{-- Dynamically populated via JS --}}
                </div>

                <div class="pt-3 border-t border-neutral-100 text-right">
                    <button
                        type="button"
                        onclick="closeJuzDetailModal()"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <script>
            const juzDetailsData = @json($juzDetails ?? []);

            function openTukarSantriModal() {
                const el = document.getElementById('modal-tukar-santri');
                if (el) el.classList.remove('hidden');
            }
            function closeTukarSantriModal() {
                const el = document.getElementById('modal-tukar-santri');
                if (el) el.classList.add('hidden');
            }

            function openTulisDoaModal() {
                const el = document.getElementById('modal-tulis-doa');
                if (el) el.classList.remove('hidden');
            }
            function closeTulisDoaModal() {
                const el = document.getElementById('modal-tulis-doa');
                if (el) el.classList.add('hidden');
            }

            function showJuzDetail(juzNumber) {
                const modal = document.getElementById('modal-juz-detail');
                const title = document.getElementById('modal-juz-title');
                const content = document.getElementById('juz-detail-content');
                if (!modal || !content) return;

                const data = juzDetailsData[juzNumber];
                title.textContent = 'Rincian Juz ' + juzNumber;

                if (!data || !data.surahs || data.surahs.length === 0) {
                    content.innerHTML = '<p class="text-sm text-neutral-500 text-center py-4">Data surah untuk Juz ' + juzNumber + ' belum tersedia.</p>';
                } else {
                    let statusLabel = data.status.toUpperCase();
                    let badgeClass = data.status === 'mutqin' ? 'bg-success-100 text-success-800' :
                                    (data.status === 'parsial' ? 'bg-amber-100 text-amber-800' : 'bg-neutral-100 text-neutral-600');

                    let html = '<div class="flex items-center justify-between pb-2">' +
                        '<span class="text-xs text-neutral-600">Status Capaian:</span>' +
                        '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold ' + badgeClass + '">' + statusLabel + '</span>' +
                        '</div>';

                    html += '<div class="space-y-2 max-h-60 overflow-y-auto">';
                    data.surahs.forEach(s => {
                        html += '<div class="p-2.5 rounded-xl bg-neutral-50 border border-neutral-200/70 flex items-center justify-between text-xs">' +
                            '<div>' +
                            '<span class="font-bold text-neutral-900">' + s.surah_nomor + '. QS. ' + s.surah_nama_latin + '</span>' +
                            '<span class="text-neutral-500 block">' + s.surah_jumlah_ayat + ' Ayat</span>' +
                            '</div>' +
                            '<span class="font-arabic text-sm text-neutral-700" dir="rtl">' + s.surah_nama_arab + '</span>' +
                            '</div>';
                    });
                    html += '</div>';

                    content.innerHTML = html;
                }

                modal.classList.remove('hidden');
            }

            function closeJuzDetailModal() {
                const el = document.getElementById('modal-juz-detail');
                if (el) el.classList.add('hidden');
            }

            // Keyboard Escape key handler (R-32, R-39)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeTukarSantriModal();
                    closeTulisDoaModal();
                    closeJuzDetailModal();
                }
            });
        </script>
    @endif
@endsection
