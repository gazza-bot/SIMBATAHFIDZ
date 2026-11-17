@extends('layouts.app', ['title' => 'Cetak Rapor', 'pageTitle' => 'Cetak Rapor Santri'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Cetak Rapor Santri</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="santri_id" class="block text-sm font-medium text-neutral-700 mb-1.5">
                    Pilih Santri <span class="text-error-500">*</span>
                </label>
                <select name="santri_id" id="santri_id" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                    <option value="">Pilih Santri</option>
                    @foreach ($santris as $santri)
                        <option value="{{ $santri->id }}" {{ $santriId == $santri->id ? 'selected' : '' }}>{{ $santri->nama }} ({{ $santri->nis }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-neutral-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $start->format('Y-m-d') }}" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-neutral-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $end->format('Y-m-d') }}" class="w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <x-button type="submit" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.293V4z"/></svg>
                Pratinjau Rapor
            </x-button>
        </div>
    </form>

    @if ($selectedSantri)
        <div class="bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Pratinjau Rapor</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                    <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $selectedSantri->nama }}</p>
                    <p class="text-sm text-neutral-500">{{ $selectedSantri->nis }} | {{ $selectedSantri->angkatan->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Periode</p>
                    <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Target Juz</p>
                    <p class="mt-2 text-2xl font-bold text-primary-600">{{ $selectedSantri->progress_juz + 1 }}</p>
                </x-card>
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Progres</p>
                    <p class="mt-2 text-2xl font-bold text-success-600">{{ $progressPercent }}%</p>
                </x-card>
            </div>

            <h4 class="text-lg font-semibold text-neutral-900 mb-3">I. Capaian Hafalan (Ziyadah & Tasmi')</h4>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-10">No</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Jenis</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Surah</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Ayat</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Juz</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($setorans as $setoran)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-4 py-2 text-sm text-neutral-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 text-sm text-neutral-600">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <x-badge variant="{{ $setoran->jenis == 'ziyadah' ? 'info' : ($setoran->jenis == 'murajaah' ? 'secondary' : 'primary') }}" size="sm">{{ ucfirst($setoran->jenis) }}</x-badge>
                                </td>
                                <td class="px-4 py-2 text-sm text-neutral-600">{{ $setoran->surah->nama_latin ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-neutral-600">{{ $setoran->ayat_range }}</td>
                                <td class="px-4 py-2 text-sm text-neutral-600">Juz {{ $setoran->juz }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <x-badge variant="{{ $setoran->nilai == 'mumtaz' ? 'mumtaz' : ($setoran->nilai == 'jayyid_jiddan' ? 'jayyid_jiddan' : ($setoran->nilai == 'jayyid' ? 'jayyid' : 'error')) }}" size="sm">
                                        {{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}
                                    </x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-neutral-500">Tidak ada data setoran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h4 class="text-lg font-semibold text-neutral-900 mt-6 mb-3">II. Kehadiran</h4>
            <div class="grid grid-cols-4 gap-3 mb-4">
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Hadir</p>
                    <p class="mt-2 text-2xl font-bold text-success-600">{{ $absensis->get('hadir', collect())->count() }}</p>
                </x-card>
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Izin</p>
                    <p class="mt-2 text-2xl font-bold text-info-600">{{ $absensis->get('izin', collect())->count() }}</p>
                </x-card>
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Sakit</p>
                    <p class="mt-2 text-2xl font-bold text-warning-600">{{ $absensis->get('sakit', collect())->count() }}</p>
                </x-card>
                <x-card padding="sm">
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Alpa</p>
                    <p class="mt-2 text-2xl font-bold text-error-600">{{ $absensis->get('alpa', collect())->count() }}</p>
                </x-card>
            </div>

<div class="flex justify-end gap-2 pt-4 border-t border-neutral-100">
                <x-button variant="secondary" href="{{ route('ustadz.rapor.pdf', array_merge(request()->query(), ['inline' => '1'])) }}" target="_blank">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2a2 2 0 002 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </x-button>
                <x-button variant="primary" href="{{ route('ustadz.rapor.pdf', array_merge(request()->query(), ['inline' => '0'])) }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh PDF
                </x-button>
            </div>
        </div>
    @else
        <x-card padding="lg" class="text-center">
            <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <p class="mt-3 text-neutral-500">Pilih santri dan periode untuk melihat pratinjau rapor.</p>
        </x-card>
    @endif
</div>
@endsection