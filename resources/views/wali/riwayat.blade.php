@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle, 'selectedSantri' => $selectedSantri, 'hijriDate' => $hijriDate ?? null, 'semester' => $semester ?? null])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-neutral-200">
        <div class="flex items-center gap-4">
            <a href="{{ route('wali.mutabaah', ['santri' => $selectedSantri->id]) }}"
               class="p-2 rounded-xl bg-neutral-100 text-neutral-600 hover:bg-neutral-200 hover:text-neutral-900 transition-colors"
               aria-label="Kembali ke Dasbor">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900">Riwayat Setoran & Presensi</h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">
                    Ananda <span class="font-semibold text-neutral-800">{{ $selectedSantri->nama }}</span> • {{ $selectedSantri->kelompok->nama ?? 'Halaqah' }}
                </p>
            </div>
        </div>

        @if ($anakans->count() > 1)
            <div class="flex items-center gap-2">
                <label for="santri-switcher" class="text-xs text-neutral-500 font-medium">Santri:</label>
                <select id="santri-switcher"
                        onchange="window.location.href='{{ url('/wali/riwayat') }}/' + this.value"
                        class="px-3 py-2 rounded-xl border border-neutral-300 text-xs sm:text-sm font-semibold text-neutral-800 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    @foreach ($anakans as $anak)
                        <option value="{{ $anak->id }}" {{ $anak->id === $selectedSantri->id ? 'selected' : '' }}>
                            {{ $anak->nama }} ({{ $anak->progress_juz ?? 0 }} Juz)
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Filter Tabs Jenis Setoran --}}
    <div class="flex flex-wrap items-center gap-2 bg-white p-3 rounded-2xl border border-neutral-200">
        <a href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id]) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors {{ is_null($selectedJenis) ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
            Semua Jenis
        </a>
        <a href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id, 'jenis' => 'ziyadah']) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors {{ $selectedJenis === 'ziyadah' ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
            Ziyadah (Hafalan Baru)
        </a>
        <a href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id, 'jenis' => 'murajaah']) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors {{ $selectedJenis === 'murajaah' ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
            Muraja'ah (Pengulangan)
        </a>
        <a href="{{ route('wali.riwayat', ['santri' => $selectedSantri->id, 'jenis' => 'tasmi']) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors {{ $selectedJenis === 'tasmi' ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
            Tasmi' (Ujian)
        </a>
    </div>

    {{-- Log Setoran --}}
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-neutral-100 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-bold text-neutral-900">Daftar Riwayat Setoran</h2>
            <span class="text-xs text-neutral-500">Total: {{ $setorans->total() }} catatan</span>
        </div>

        @if ($setorans->isEmpty())
            {{-- Empty State (R-27) --}}
            <div class="py-12 px-4 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-neutral-100 text-neutral-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-bold text-neutral-900">Belum Ada Setoran</p>
                <p class="text-xs text-neutral-500 mt-1">Tidak ditemukan riwayat setoran untuk filter yang dipilih.</p>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-600 font-semibold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Surah & Ayat</th>
                            <th class="py-3 px-4">Juz</th>
                            <th class="py-3 px-4">Nilai & Skor</th>
                            <th class="py-3 px-4">Musyrif</th>
                            <th class="py-3 px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($setorans as $setoran)
                            <tr class="hover:bg-neutral-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-neutral-700 whitespace-nowrap">
                                    {{ $setoran->tanggal->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $setoran->jenis === 'ziyadah' ? 'bg-primary-50 text-primary-700' : ($setoran->jenis === 'murajaah' ? 'bg-info-50 text-info-700' : 'bg-accent-50 text-accent-700') }}">
                                        {{ $setoran->jenis_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-neutral-900">
                                    QS. {{ $setoran->surah->nama_latin ?? '-' }}: {{ $setoran->ayat_range }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-neutral-700">
                                    Juz {{ $setoran->juz }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-badge variant="success" size="sm">
                                        {{ $setoran->nilai_label }} {{ $setoran->skor ? "({$setoran->skor})" : '' }}
                                    </x-badge>
                                </td>
                                <td class="py-3.5 px-4 text-neutral-600 whitespace-nowrap">
                                    Ust. {{ $setoran->ustadz->nama ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-neutral-600 max-w-xs truncate" title="{{ $setoran->catatan }}">
                                    {{ $setoran->catatan ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden divide-y divide-neutral-100">
                @foreach ($setorans as $setoran)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono text-neutral-500">{{ $setoran->tanggal->translatedFormat('d M Y') }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $setoran->jenis === 'ziyadah' ? 'bg-primary-50 text-primary-700' : ($setoran->jenis === 'murajaah' ? 'bg-info-50 text-info-700' : 'bg-accent-50 text-accent-700') }}">
                                {{ $setoran->jenis_label }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-neutral-900">QS. {{ $setoran->surah->nama_latin ?? '-' }}: {{ $setoran->ayat_range }}</p>
                            <span class="text-xs font-mono font-bold text-primary-700">Juz {{ $setoran->juz }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-neutral-600">
                            <span>Ust. {{ $setoran->ustadz->nama ?? '-' }}</span>
                            <x-badge variant="success" size="sm">{{ $setoran->nilai_label }}</x-badge>
                        </div>
                        @if ($setoran->catatan)
                            <p class="text-xs text-neutral-500 italic bg-neutral-50 p-2 rounded-lg">
                                "{{ $setoran->catatan }}"
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($setorans->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $setorans->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Log Presensi 30 Hari Terakhir --}}
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-neutral-900">Presensi Halaqah Terkini</h2>
                <p class="text-xs text-neutral-500">Catatan kehadiran ananda 30 sesi terakhir.</p>
            </div>
            <span class="text-xs font-mono font-semibold text-neutral-600">{{ $absensis->count() }} Sesi</span>
        </div>

        @if ($absensis->isEmpty())
            <div class="py-8 text-center text-xs text-neutral-500">
                Belum ada data presensi yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-600 font-semibold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Sesi</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Waktu Tiba</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($absensis as $absensi)
                            <tr class="hover:bg-neutral-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono text-neutral-700 whitespace-nowrap">
                                    {{ $absensi->tanggal->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-4 capitalize font-medium text-neutral-800">
                                    {{ $absensi->sesi }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @php
                                        $badgeVariant = match($absensi->status) {
                                            'hadir' => 'success',
                                            'izin' => 'info',
                                            'sakit' => 'warning',
                                            'alpa' => 'error',
                                            default => 'neutral',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVariant" size="sm">
                                        {{ ucfirst($absensi->status) }}
                                    </x-badge>
                                </td>
                                <td class="py-3 px-4 font-mono text-neutral-600">
                                    {{ $absensi->waktu_tiba ? date('H:i', strtotime($absensi->waktu_tiba)) . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-4 text-neutral-600 max-w-xs truncate">
                                    {{ $absensi->catatan ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
