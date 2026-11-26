@extends('layouts.app', ['title' => $title, 'pageTitle' => $pageTitle])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header Sesi Aktif --}}
    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-5 sm:p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold">Assalamu'alaikum, Ustadz {{ $ustadz->nama }}</h2>
                <p class="mt-1 text-sm text-primary-100">Kelola Halaqah {{ \App\Models\Kelompok::where('ustadz_id', $ustadz->id)->first()?->nama ?? 'A' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button variant="outline-light" size="sm" href="{{ route('ustadz.absensis.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Absensi Sesi Ini
                </x-button>
                <x-button variant="secondary" size="sm" href="{{ route('ustadz.setorans.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Setoran Cepat
                </x-button>
                <x-button variant="outline-light" size="sm" href="{{ route('ustadz.rapor.index') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Pratinjau Rapor
                </x-button>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-card padding="default" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri Hadir</p>
            <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $hadirToday }}/{{ $totalSantri }}</p>
        </x-card>
        <x-card padding="default" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Sudah Setor</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ $sudahSetorToday }}/{{ $totalSantri }} ({{ $totalSantri > 0 ? round(($sudahSetorToday/$totalSantri)*100) : 0 }}%)</p>
        </x-card>
        <x-card padding="default" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Belum Setor</p>
            <p class="mt-2 text-2xl font-bold text-error-600">{{ $belumSetorToday }}</p>
        </x-card>
        <x-card padding="default" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Rata-rata Mutu</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ $mutuScore }}%</p>
        </x-card>
    </div>

    {{-- Daftar Santri & Status Setoran --}}
    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-neutral-900">Daftar Santri Halaqah & Status Setoran</h3>
                <p class="text-sm text-neutral-500">Hari ini: {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex gap-1 bg-neutral-100 rounded-lg p-1" role="tablist">
                <a href="{{ route('ustadz.dashboard', ['filter' => 'semua']) }}"
                   class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'semua' ? 'bg-white text-primary-600 shadow-sm' : 'text-neutral-600 hover:text-neutral-900' }}"
                   aria-current="{{ $filter === 'semua' ? 'page' : 'false' }}">
                    Semua ({{ $totalSantri }})
                </a>
                <a href="{{ route('ustadz.dashboard', ['filter' => 'belum']) }}"
                   class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'belum' ? 'bg-white text-primary-600 shadow-sm' : 'text-neutral-600 hover:text-neutral-900' }}"
                   aria-current="{{ $filter === 'belum' ? 'page' : 'false' }}">
                    Belum Setor ({{ $belumSetorToday }})
                </a>
                <a href="{{ route('ustadz.dashboard', ['filter' => 'sudah']) }}"
                   class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'sudah' ? 'bg-white text-primary-600 shadow-sm' : 'text-neutral-600 hover:text-neutral-900' }}"
                   aria-current="{{ $filter === 'sudah' ? 'page' : 'false' }}">
                    Sudah Setor ({{ $sudahSetorToday }})
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-neutral-200">
            <table class="w-full" role="grid">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-10">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Target Juz</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Setoran Hari Ini</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($santris as $santri)
                        @php
                            $setoranToday = $setoranHariIni->get($santri->id);
                        @endphp
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$santri->nama" size="sm" />
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-sm font-medium text-neutral-900">{{ $santri->nama }}</p>
                                            @php
                                                $santriMsgs = $waliMessages->get($santri->id);
                                                $latestMsg = $santriMsgs?->first();
                                                $hasUnreadMsg = $santriMsgs?->contains(fn($m) => ! $m->dibacakan);
                                            @endphp
                                            @if ($latestMsg)
                                                <button
                                                    type="button"
                                                    data-id="{{ $latestMsg->id }}"
                                                    data-wali="{{ $latestMsg->wali->nama ?? 'Wali Santri' }}"
                                                    data-santri="{{ $santri->nama }}"
                                                    data-pesan="{{ $latestMsg->pesan }}"
                                                    data-dibacakan="{{ $latestMsg->dibacakan ? '1' : '0' }}"
                                                    data-dibacakan-at="{{ $latestMsg->dibacakan_at ? $latestMsg->dibacakan_at->translatedFormat('d M Y, H:i') : '' }}"
                                                    data-waktu="{{ $latestMsg->created_at->diffForHumans() }}"
                                                    data-url="{{ route('ustadz.pesan-wali.bacakan', $latestMsg->id) }}"
                                                    onclick="openPesanModalFromBtn(this)"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium transition-colors cursor-pointer {{ $hasUnreadMsg ? 'bg-amber-100 text-amber-900 hover:bg-amber-200 ring-1 ring-amber-300' : 'bg-neutral-100 text-neutral-500 hover:bg-neutral-200' }}"
                                                    title="{{ $hasUnreadMsg ? 'Ada pesan doa baru dari orang tua' : 'Lihat doa dari orang tua' }}"
                                                    aria-label="Pesan doa dari orang tua untuk {{ $santri->nama }}">
                                                    <svg class="w-3.5 h-3.5 {{ $hasUnreadMsg ? 'text-amber-600 animate-pulse' : 'text-neutral-400' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                                    @if ($hasUnreadMsg)
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        <span class="text-[10px] font-bold">Doa Baru</span>
                                                    @endif
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-xs text-neutral-500">{{ $santri->nis }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $santri->angkatan->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                {{ $santri->progress_juz < 30 ? 'Juz ' . ($santri->progress_juz + 1) : 'Lulus' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($setoranToday)
                                    <x-badge variant="{{ $setoranToday->jenis == 'ziyadah' ? 'info' : ($setoranToday->jenis == 'murajaah' ? 'secondary' : 'primary') }}" size="sm" dot>
                                        {{ ucfirst($setoranToday->jenis) }}: {{ $setoranToday->surah->nama_latin }} {{ $setoranToday->ayat_range }}
                                    </x-badge>
                                @else
                                    <span class="text-sm text-neutral-400">Belum setor</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.create') }}?santri={{ $santri->id }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Catat
                                    </x-button>
                                    @if ($setoranToday)
                                        <x-button variant="outline" size="sm" href="{{ route('ustadz.setorans.show', $setoranToday) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Detail
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <x-card padding="8">
                                    <p class="text-neutral-500">
                                        @if ($filter === 'belum')
                                            Semua santri sudah setor.
                                        @elseif ($filter === 'sudah')
                                            Semua santri belum setor.
                                        @else
                                            Tidak ada santri di halaqah ini.
                                        @endif
                                    </p>
                                </x-card>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Setoran Terakhir Masuk (Live Feed) --}}
    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Setoran Terakhir Masuk</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse ($lastSetorans as $setoran)
                <div class="flex items-center gap-3 p-3 bg-neutral-50 rounded-xl border border-neutral-100">
                    <x-avatar :name="$setoran->santri->nama" size="sm" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-neutral-900">{{ $setoran->santri->nama }}</p>
                        <p class="text-xs text-neutral-500">{{ $setoran->surah->nama_latin }} {{ $setoran->ayat_range }} (Juz {{ $setoran->juz }})</p>
                    </div>
                    <x-badge
                        variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                        size="sm"
                    >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
                    <span class="text-xs text-neutral-500">{{ $setoran->tanggal->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-center text-neutral-500 py-4">Belum ada setoran terbaru.</p>
            @endforelse
        </div>
    </div>

    {{-- Target Sima'an Pekan Ini --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-card padding="lg" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Target Sima'an Pekan Ini</p>
            <p class="mt-2 text-2xl font-bold text-primary-600">{{ $targetSimaan }} juz</p>
        </x-card>

        <x-card padding="lg" class="bg-white border border-neutral-200">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Catatan Halaqah</p>
            <p class="mt-2 text-sm font-medium text-neutral-700 line-clamp-2">{{ $halaqahNote?->catatan ?: 'Belum ada catatan hari ini.' }}</p>
        </x-card>
    </div>

    {{-- Catatan Halaqah Hari Ini --}}
    <form method="POST" action="{{ route('ustadz.dashboard.catatan') }}" class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
        @csrf
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
            <h3 class="text-lg font-semibold text-neutral-900">Catatan Halaqah Hari Ini</h3>
            <span class="text-xs text-neutral-500">Simpan manual</span>
        </div>
        <textarea name="catatan" rows="3" placeholder="Tulis catatan pembinaan, catatan khusus santri, atau informasi penting untuk hari ini..." class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-500 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 transition-all duration-fast ease-standard">{{ $halaqahNote->catatan ?? '' }}</textarea>
        <div class="mt-4 flex justify-end">
            <x-button type="submit" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Catatan
            </x-button>
        </div>
    </form>

    {{-- Modal Pesan Doa Wali Santri --}}
    <div id="modal-pesan-wali" class="fixed inset-0 z-50 bg-neutral-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-pesan-title">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 id="modal-pesan-title" class="text-base font-bold text-neutral-900">Pesan Doa & Kasih Orang Tua</h3>
                </div>
                <button type="button" onclick="closePesanWaliModal()" class="text-neutral-400 hover:text-neutral-700 p-1 rounded-lg" aria-label="Tutup modal">
                    ✕
                </button>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs pb-1 border-b border-neutral-100">
                    <p class="text-neutral-600">
                        Dari: <span id="modal-wali-nama" class="font-bold text-neutral-900"></span>
                    </p>
                    <span id="modal-pesan-waktu" class="text-neutral-500 font-mono"></span>
                </div>

                <div class="text-xs text-neutral-600">
                    Untuk ananda: <span id="modal-santri-nama" class="font-bold text-neutral-900"></span>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                    <p class="text-xs font-semibold text-amber-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Pesan Doa untuk Dibacakan saat Halaqah:
                    </p>
                    <blockquote id="modal-pesan-teks" class="text-sm text-neutral-800 italic leading-relaxed whitespace-pre-wrap"></blockquote>
                </div>

                <div id="modal-status-container" class="pt-1">
                    {{-- Status Tag dynamically set --}}
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-neutral-100">
                <button
                    type="button"
                    onclick="closePesanWaliModal()"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition-colors">
                    Tutup
                </button>
                <form id="form-bacakan-pesan" method="POST" action="">
                    @csrf
                    <button
                        type="submit"
                        id="btn-bacakan-pesan"
                        class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai Sudah Dibacakan saat Halaqah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openPesanModalFromBtn(btn) {
        openPesanWaliModal({
            id: btn.dataset.id,
            wali_nama: btn.dataset.wali,
            santri_nama: btn.dataset.santri,
            pesan: btn.dataset.pesan,
            dibacakan: btn.dataset.dibacakan === '1',
            dibacakan_at: btn.dataset.dibacakanAt,
            created_at: btn.dataset.waktu,
            action_url: btn.dataset.url
        });
    }

    function openPesanWaliModal(data) {
        const modal = document.getElementById('modal-pesan-wali');
        if (!modal) return;

        document.getElementById('modal-wali-nama').textContent = data.wali_nama;
        document.getElementById('modal-santri-nama').textContent = data.santri_nama;
        document.getElementById('modal-pesan-teks').textContent = data.pesan;
        document.getElementById('modal-pesan-waktu').textContent = data.created_at;

        const form = document.getElementById('form-bacakan-pesan');
        form.action = data.action_url;

        const statusContainer = document.getElementById('modal-status-container');
        const btnBacakan = document.getElementById('btn-bacakan-pesan');

        if (data.dibacakan) {
            statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-success-50 text-success-700 border border-success-200"><span class="w-1.5 h-1.5 rounded-full bg-success-600"></span> Sudah dibacakan pada ' + (data.dibacakan_at || 'halaqah') + '</span>';
            btnBacakan.classList.add('hidden');
        } else {
            statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Belum dibacakan saat halaqah</span>';
            btnBacakan.classList.remove('hidden');
        }

        modal.classList.remove('hidden');
    }

    function closePesanWaliModal() {
        const modal = document.getElementById('modal-pesan-wali');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePesanWaliModal();
        }
    });
</script>
@endsection