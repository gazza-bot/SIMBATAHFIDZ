@extends('layouts.print')

@section('content')
<div class="page-header">
    <div class="logo">
        <div style="width: 22px; height: 16px; border: 2px solid #FFFFFF; border-radius: 2px; margin: 20px auto 0;">
            <div style="width: 2px; height: 100%; background: #FFFFFF; margin: 0 auto;"></div>
        </div>
    </div>
    <h1>Rekap Capaian Setoran</h1>
    <p>{{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}</p>
</div>

<div class="card">
    <table class="w-full">
        <thead>
            <tr class="border-b border-neutral-300">
                <th class="text-left py-2 px-3">No</th>
                <th class="text-left py-2 px-3">Santri</th>
                <th class="text-left py-2 px-3">Tanggal</th>
                <th class="text-left py-2 px-3">Jenis</th>
                <th class="text-left py-2 px-3">Surah</th>
                <th class="text-left py-2 px-3">Ayat</th>
                <th class="text-left py-2 px-3">Juz</th>
                <th class="text-right py-2 px-3">Volume</th>
                <th class="text-left py-2 px-3">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($setorans as $setoran)
                <tr class="border-b border-neutral-200">
                    <td class="py-2 px-3">{{ $loop->iteration }}</td>
                    <td class="py-2 px-3">{{ $setoran->santri->nama ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $setoran->tanggal->format('d/m/Y') }}</td>
                    <td class="py-2 px-3">{{ ucfirst($setoran->jenis) }}</td>
                    <td class="py-2 px-3">{{ $setoran->surah->nama_latin ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $setoran->ayat_range }}</td>
                    <td class="py-2 px-3">{{ $setoran->juz }}</td>
                    <td class="py-2 px-3 text-right">{{ $setoran->ayat_akhir - $setoran->ayat_awal + 1 }}</td>
                    <td class="py-2 px-3">{{ ucwords(str_replace('_', ' ', $setoran->nilai)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-neutral-500">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="page-footer">
    Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} oleh SIMBA TAHFIDZ
</div>
@endsection