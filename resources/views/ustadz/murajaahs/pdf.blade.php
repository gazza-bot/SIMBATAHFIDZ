@extends('layouts.print')

@section('content')
<div class="page-header">
    <div class="logo">
        <div style="width: 22px; height: 16px; border: 2px solid #FFFFFF; border-radius: 2px; margin: 20px auto 0;">
            <div style="width: 2px; height: 100%; background: #FFFFFF; margin: 0 auto;"></div>
        </div>
    </div>
    <h1>Rekap Murajaah Bulanan</h1>
    <p>Pondok Pesantren Darut Taqwa Ponorogo</p>
    <p>Bulan: {{ Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}</p>
</div>

<div class="card">
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <tr>
            <td style="width: 33%;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Sudah Murajaah</p>
                <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $sudahCount }}</p>
            </td>
            <td style="width: 34%; text-align: center;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Belum Murajaah</p>
                <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $belumCount }}</p>
            </td>
            <td style="width: 33%; text-align: right;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Persentase</p>
                <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $persen }}%</p>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h3 class="card-title">Daftar Santri</h3>
    <table class="table w-full">
        <thead>
            <tr class="border-b border-neutral-300">
                <th class="text-left py-2 px-3">No</th>
                <th class="text-left py-2 px-3">NIS</th>
                <th class="text-left py-2 px-3">Nama Santri</th>
                <th class="text-left py-2 px-3">Angkatan</th>
                <th class="text-left py-2 px-3">Kelompok</th>
                <th class="text-center py-2 px-3">Status</th>
                <th class="text-center py-2 px-3">Jumlah Bulan Ini</th>
                <th class="text-left py-2 px-3">Terakhir Murajaah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santriData as $item)
                <tr class="border-b border-neutral-200">
                    <td class="py-2 px-3">{{ $loop->iteration }}</td>
                    <td class="py-2 px-3">{{ $item['santri']->nis }}</td>
                    <td class="py-2 px-3">{{ $item['santri']->nama }}</td>
                    <td class="py-2 px-3">{{ $item['santri']->angkatan->nama ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $item['santri']->kelompok->nama ?? '-' }}</td>
                    <td class="text-center py-2 px-3">
                        @if ($item['status'] === 'sudah')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700">
                                Sudah
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-error-50 text-error-700">
                                Belum
                            </span>
                        @endif
                    </td>
                    <td class="text-center py-2 px-3">{{ $item['count_bulan'] }}</td>
                    <td class="py-2 px-3">
                        @if ($item['last_murajaah'])
                            {{ $item['last_murajaah']->tanggal->translatedFormat('d F Y') }}
                        @else
                            <span class="text-error-600">Belum pernah</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-neutral-500">Tidak ada data santri.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection