@extends('layouts.print')

@section('content')
<div class="page-header">
    <div class="logo">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
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