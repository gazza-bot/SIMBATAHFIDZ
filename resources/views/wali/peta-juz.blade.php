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
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900">Peta Hafalan 30 Juz Al-Qur'an</h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">
                    Sebaran capaian hafalan santri <span class="font-semibold text-neutral-800">{{ $selectedSantri->nama }}</span>
                </p>
            </div>
        </div>

        @if ($anakans->count() > 1)
            <div class="flex items-center gap-2">
                <label for="peta-santri-switcher" class="text-xs text-neutral-500 font-medium">Santri:</label>
                <select id="peta-santri-switcher"
                        onchange="window.location.href='{{ url('/wali/peta-juz') }}/' + this.value"
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-neutral-200 text-center">
            <span class="inline-block w-3 h-3 rounded-full bg-success-500 mb-1"></span>
            <p class="text-xs text-neutral-500 font-medium">Mutqin (100%)</p>
            <p class="text-xl font-bold text-success-600 mt-1">{{ $juzCounts['mutqin'] }} Juz</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-neutral-200 text-center">
            <span class="inline-block w-3 h-3 rounded-full bg-accent-500 mb-1"></span>
            <p class="text-xs text-neutral-500 font-medium">Parsial (1-99%)</p>
            <p class="text-xl font-bold text-accent-600 mt-1">{{ $juzCounts['parsial'] }} Juz</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-neutral-200 text-center">
            <span class="inline-block w-3 h-3 rounded-full bg-neutral-300 mb-1"></span>
            <p class="text-xs text-neutral-500 font-medium">Belum Dihafal</p>
            <p class="text-xl font-bold text-neutral-700 mt-1">{{ $juzCounts['belum'] }} Juz</p>
        </div>
    </div>

    {{-- Grid 30 Juz Interaktif --}}
    <div class="bg-white p-6 rounded-2xl border border-neutral-200 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-100 pb-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-neutral-900">Grid 30 Juz</h2>
                <p class="text-xs text-neutral-500">Klik kotak nomor juz untuk melihat rincian surah di dalamnya.</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-neutral-600">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-success-500"></span> Mutqin</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-accent-500"></span> Parsial</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-neutral-200"></span> Belum</span>
            </div>
        </div>

        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-10 gap-2.5">
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
                    class="flex flex-col items-center justify-center p-3 rounded-xl {{ $bgClass }} transition-transform hover:scale-105 duration-fast focus:outline-none focus:ring-2 focus:ring-primary-500 min-h-[64px] cursor-pointer"
                    aria-label="Juz {{ $j }}: {{ ucfirst($st) }}">
                    <span class="text-[10px] uppercase font-semibold opacity-80 leading-none">Juz</span>
                    <span class="text-base font-bold font-mono leading-tight mt-1">{{ $j }}</span>
                </button>
            @endfor
        </div>
    </div>

    {{-- Detail Surah per Juz List --}}
    <div class="bg-white p-6 rounded-2xl border border-neutral-200 space-y-4">
        <h2 class="text-base sm:text-lg font-bold text-neutral-900">Rincian Surah Lengkap per Juz</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($juzDetails as $jNum => $jInfo)
                <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/50 space-y-2">
                    <div class="flex items-center justify-between pb-2 border-b border-neutral-200/70">
                        <span class="font-bold text-sm text-neutral-900">Juz {{ $jNum }}</span>
                        @php
                            $badgeVariant = match($jInfo['status']) {
                                'mutqin' => 'success',
                                'parsial' => 'warning',
                                default => 'neutral',
                            };
                        @endphp
                        <x-badge :variant="$badgeVariant" size="sm">
                            {{ ucfirst($jInfo['status']) }}
                        </x-badge>
                    </div>
                    <div class="space-y-1 text-xs">
                        @foreach ($jInfo['surahs'] as $s)
                            <div class="flex items-center justify-between text-neutral-700">
                                <span>{{ $s['surah_nomor'] }}. QS. {{ $s['surah_nama_latin'] }}</span>
                                <span class="font-arabic text-neutral-500" dir="rtl">{{ $s['surah_nama_arab'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

 {{-- MODAL Detail Juz --}}
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
                    '<span class="font-bold text-neutral-900">' + s.nomor + '. QS. ' + s.nama_latin + '</span>' +
                    '<span class="text-neutral-500 block">' + s.jumlah_ayat + ' Ayat</span>' +
                    '</div>' +
                    '<span class="font-arabic text-sm text-neutral-700" dir="rtl">' + s.nama_arab + '</span>' +
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeJuzDetailModal();
        }
    });
</script>
@endsection