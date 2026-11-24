@extends('layouts.print')

@section('content')
<div class="page-header">
    <div class="logo">
        <div style="width: 22px; height: 16px; border: 2px solid #FFFFFF; border-radius: 2px; margin: 20px auto 0;">
            <div style="width: 2px; height: 100%; background: #FFFFFF; margin: 0 auto;"></div>
        </div>
    </div>
    <h1>Rapor Hafalan Santri</h1>
    <p>Pondok Pesantren Darut Taqwa Ponorogo</p>
</div>

<div class="card">
    <div class="grid-2 mb-4">
        <div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
            <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $santri->nama }}</p>
            <p class="text-sm text-neutral-500">NIS: {{ $santri->nis }} | Angkatan: {{ $santri->angkatan->nama ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Periode</p>
            <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <div class="grid-3 mb-4">
        <div class="info-row">
            <span class="info-label">Target Juz</span>
            <span class="info-value font-bold text-primary-600">{{ $progressJuz < 30 ? 'Juz ' . ($progressJuz + 1) : 'Lulus' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Progres</span>
            <span class="info-value font-bold text-success-600">{{ $progressPercent }}%</span>
        </div>
        <div class="info-row">
            <span class="info-label">Halaqah</span>
            <span class="info-value">{{ $santri->kelompok->nama ?? '-' }}</span>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="card-title">I. Capaian Hafalan (Ziyadah & Tasmi')</h3>
    <table class="table">
        <thead>
            <tr class="border-b border-neutral-300">
                <th class="text-left py-2 px-3">No</th>
                <th class="text-left py-2 px-3">Tanggal</th>
                <th class="text-left py-2 px-3">Jenis</th>
                <th class="text-left py-2 px-3">Surah</th>
                <th class="text-left py-2 px-3">Ayat</th>
                <th class="text-left py-2 px-3">Juz</th>
                <th class="text-left py-2 px-3">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($setorans as $setoran)
                <tr class="border-b border-neutral-200">
                    <td class="py-2 px-3">{{ $loop->iteration }}</td>
                    <td class="py-2 px-3">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                    <td class="py-2 px-3">
                        <span class="badge badge-{{ $setoran->jenis == 'ziyadah' ? 'info' : ($setoran->jenis == 'murajaah' ? 'warning' : 'primary') }}">
                            {{ ucfirst($setoran->jenis) }}
                        </span>
                    </td>
                    <td class="py-2 px-3">
                        <span class="font-arabic">{{ $setoran->surah->nama_arab ?? $setoran->surah->nama_latin }}</span>
                        <br>
                        <small>{{ $setoran->surah->nama_latin }}</small>
                    </td>
                    <td class="py-2 px-3">{{ $setoran->ayat_range }}</td>
                    <td class="py-2 px-3">Juz {{ $setoran->juz }}</td>
                    <td class="py-2 px-3">
                        <span class="badge badge-{{ $setoran->nilai == 'mumtaz' ? 'success' : ($setoran->nilai == 'jayyid_jiddan' ? 'success' : ($setoran->nilai == 'jayyid' ? 'info' : 'warning')) }}">
                            {{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-neutral-500">Tidak ada data setoran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <h3 class="card-title">II. Kehadiran</h3>
    <div class="grid-4 mb-4">
        <div class="info-row">
            <span class="info-label">Hadir</span>
            <span class="info-value font-bold text-success-600">{{ $absensis->get('hadir', collect())->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Izin</span>
            <span class="info-value font-bold text-info-600">{{ $absensis->get('izin', collect())->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sakit</span>
            <span class="info-value font-bold text-warning-600">{{ $absensis->get('sakit', collect())->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Alpa</span>
            <span class="info-value font-bold text-error-600">{{ $absensis->get('alpa', collect())->count() }}</span>
        </div>
    </div>
</div>

<div class="page-footer">
    Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} oleh Ustadz {{ auth()->user()->name ?? 'Ustadz' }} | SIMBA TAHFIDZ
</div>
@endsection