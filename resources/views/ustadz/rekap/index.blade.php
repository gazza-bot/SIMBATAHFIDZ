@extends('layouts.app', ['title' => 'Rekap Capaian', 'pageTitle' => 'Rekap Capaian'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Rekap Capaian</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <select name="periode" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <option value="hari_ini" {{ $period == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                <option value="pekan_ini" {{ $period == 'pekan_ini' ? 'selected' : '' }}>Pekan Ini</option>
                <option value="bulan_ini" {{ $period == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="kustom" {{ $period == 'kustom' ? 'selected' : '' }}>Rentang Kustom</option>
            </select>
            <select name="santri_id" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <option value="">Semua Santri</option>
                @foreach ($santris as $santri)
                    <option value="{{ $santri->id }}" {{ $santriId == $santri->id ? 'selected' : '' }}>{{ $santri->nama }} ({{ $santri->nis }})</option>
                @endforeach
            </select>
            <select name="jenis" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <option value="">Semua Jenis</option>
                <option value="ziyadah" {{ $jenis == 'ziyadah' ? 'selected' : '' }}>Ziyadah</option>
                <option value="murajaah" {{ $jenis == 'murajaah' ? 'selected' : '' }}>Murajaah</option>
                <option value="tasmi" {{ $jenis == 'tasmi' ? 'selected' : '' }}>Tasmi</option>
            </select>
            <select name="nilai" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <option value="">Semua Predikat</option>
                <option value="maqbul" {{ $nilai == 'maqbul' ? 'selected' : '' }}>Maqbul</option>
                <option value="jayyid" {{ $nilai == 'jayyid' ? 'selected' : '' }}>Jayyid</option>
                <option value="jayyid_jiddan" {{ $nilai == 'jayyid_jiddan' ? 'selected' : '' }}>Jayyid Jiddan</option>
                <option value="mumtaz" {{ $nilai == 'mumtaz' ? 'selected' : '' }}>Mumtaz</option>
            </select>
            @if ($period == 'kustom')
                <input type="date" name="tanggal_mulai" value="{{ $start->format('Y-m-d') }}" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                <input type="date" name="tanggal_selesai" value="{{ $end->format('Y-m-d') }}" class="w-full sm:w-48 rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
            @endif
            <x-button type="submit" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.293V4z"/></svg>
                Tampilkan
            </x-button>
        </div>
        @if ($period == 'kustom')
            <input type="hidden" name="periode" value="kustom">
        @endif
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Santri</p>
            <p class="mt-2 text-2xl font-bold text-neutral-900">{{ $stats['total_santri'] }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Total Setoran</p>
            <p class="mt-2 text-2xl font-bold text-primary-600">{{ $stats['total_setoran'] }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Volume Ayat</p>
            <p class="mt-2 text-2xl font-bold text-success-600">{{ number_format($stats['volume_ayat']) }}</p>
        </x-card>
        <x-card padding="sm">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Rata-rata Mutu</p>
            <p class="mt-2 text-2xl font-bold text-warning-600">{{ $stats['rata_mutu'] }}%</p>
        </x-card>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" role="grid">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-10">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Waktu & Sesi</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Materi</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Volume</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Predikat</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($setorans as $setoran)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $setorans->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-neutral-900">
                                {{ $setoran->santri->nama ?? '-' }}
                                <span class="block text-xs text-neutral-500">{{ $setoran->santri->nis ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                <x-badge variant="{{ $setoran->jenis == 'ziyadah' ? 'info' : ($setoran->jenis == 'murajaah' ? 'secondary' : 'primary') }}" size="sm">{{ ucfirst($setoran->jenis) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                {{ $setoran->surah->nama_latin ?? '-' }} {{ $setoran->ayat_range }} (Juz {{ $setoran->juz }})
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                {{ $setoran->ayat_akhir - $setoran->ayat_awal + 1 }} ayat
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <x-badge
                                    variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}"
                                    size="sm"
                                >{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <x-button variant="ghost" size="sm" href="{{ route('ustadz.setorans.show', $setoran) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Detail
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <x-card padding="none">
                                    <p class="mt-3 text-neutral-500">Tidak ada data setoran pada periode ini.</p>
                                </x-card>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($setorans->hasPages())
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-neutral-500">
                    Menampilkan {{ $setorans->firstItem() }} sampai {{ $setorans->lastItem() }} dari {{ $setorans->total() }} data
                </div>
                <div class="flex gap-1">
                    {{ $setorans->links() }}
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6 space-y-4">
        <h3 class="text-lg font-semibold text-neutral-900">Ekspor Data</h3>
        <div class="flex flex-wrap gap-2">
            <x-button variant="primary" href="{{ route('ustadz.rekap.export.csv', array_merge(request()->query(), ['format' => 'csv'])) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor CSV
            </x-button>
            <x-button variant="primary" href="{{ route('ustadz.rekap.export.pdf', array_merge(request()->query(), ['format' => 'pdf'])) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor PDF
            </x-button>
        </div>
    </div>
</div>
@endsection