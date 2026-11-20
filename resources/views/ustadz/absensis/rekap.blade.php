@extends('layouts.app', ['title' => 'Rekap Presensi', 'pageTitle' => 'Rekap Presensi Bulanan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-neutral-900">Rekap Presensi Bulanan</h2>
            <p class="text-sm text-neutral-500">Halaqah {{ \App\Models\Kelompok::where('ustadz_id', auth()->user()->ustadz->id)->first()?->nama ?? 'A' }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <select
                name="bulan"
                id="rekap-bulan"
                class="w-full sm:w-auto rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]"
            >
                @foreach ($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select
                name="tahun"
                id="rekap-tahun"
                class="w-full sm:w-auto rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 focus:outline-none focus:ring-2 min-h-[44px]"
            >
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $year == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <x-button variant="primary" type="submit" form="filter-form" class="min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.293V4z"/></svg>
                Tampilkan
            </x-button>
        </div>
    </div>

    <form id="filter-form" method="GET" class="hidden">
        <input type="hidden" name="bulan" value="{{ $month }}">
        <input type="hidden" name="tahun" value="{{ $year }}">
    </form>

    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden" data-table-container>
        <div class="overflow-x-auto">
            <table class="w-full" role="grid" aria-label="Rekap presensi bulanan">
                <thead class="bg-neutral-50 border-b border-neutral-200 sticky top-0">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-10">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Santri</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">NIS</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Angkatan</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Hadir</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Izin</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Sakit</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Alpa</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($rekap as $index => $item)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $item['santri']->nama }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-500">{{ $item['santri']->nis }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $item['santri']->angkatan->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="success" size="sm" dot>{{ $item['hadir'] }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="info" size="sm" dot>{{ $item['izin'] }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="warning" size="sm" dot>{{ $item['sakit'] }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-badge variant="error" size="sm" dot>{{ $item['alpa'] }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-neutral-900">{{ $item['total'] }}</td>
                            <td class="px-4 py-3 text-center text-sm font-medium {{ $item['total'] > 0 && ($item['hadir']/$item['total']*100) >= 75 ? 'text-success-600' : 'text-error-600' }}">
                                {{ $item['total'] > 0 ? round($item['hadir']/$item['total']*100) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full border-2 border-dashed border-neutral-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <p class="text-neutral-500">Belum ada data presensi untuk bulan ini.</p>
                                    <x-button variant="primary" size="sm" href="{{ route('ustadz.absensis.index') }}" class="min-h-[44px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                        Presensi Harian
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <x-button variant="secondary" href="{{ route('ustadz.absensis.index') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Presensi Harian
        </x-button>
    </div>
</div>
@endsection