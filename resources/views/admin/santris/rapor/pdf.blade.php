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
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <tr>
            <td style="width: 62%; vertical-align: top; padding-right: 18px;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Santri</p>
                <p class="mt-1 text-lg font-semibold text-neutral-900">{{ $santri->nama }}</p>
                <p class="text-sm text-neutral-500">NIS: {{ $santri->nis }} | Angkatan: {{ $santri->angkatan?->nama ?? '-' }}</p>
                <p class="text-sm text-neutral-500">Halaqah: {{ $santri->kelompok?->nama ?? '-' }} | Musyrif: {{ $santri->kelompok?->ustadz?->user?->name ?? '-' }}</p>
                <p class="text-sm text-neutral-500">Wali: {{ $santri->wali?->user?->name ?? '-' }}</p>
            </td>
            <td style="width: 38%; vertical-align: top;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Cakupan</p>
                <p class="mt-1 text-lg font-semibold text-neutral-900">Riwayat Lengkap</p>
                <p class="text-sm text-neutral-500">{{ $setorans->count() }} setoran disetujui</p>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #F1F4F2;">
        <tr>
            <td style="padding: 6px 0; color: #64746B;">Progres Ziyadah</td>
            <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #15803D;">{{ $progressPercent }}% menuju 30 Juz</td>
        </tr>
    </table>
</div>

<div class="card">
    <h3 class="card-title">I. Riwayat Setoran (Ziyadah, Murajaah, Tasmi')</h3>
    <table class="table w-full">
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
                    <td class="py-2 px-3">{{ $setoran->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $setoran->jenis_label }}</td>
                    <td class="py-2 px-3">{{ $setoran->surah?->nama_latin ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $setoran->ayat_range }}</td>
                    <td class="py-2 px-3">Juz {{ $setoran->juz }}</td>
                    <td class="py-2 px-3">{{ $setoran->nilai_label }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-neutral-500">Belum ada setoran yang disetujui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <h3 class="card-title">II. Kehadiran (Seluruh Riwayat)</h3>
    <table class="table w-full">
        <thead>
            <tr>
                <th class="text-center">Hadir</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Alpa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center font-bold text-success-600">{{ $absensis->get('hadir', collect())->count() }}</td>
                <td class="text-center font-bold text-info-600">{{ $absensis->get('izin', collect())->count() }}</td>
                <td class="text-center font-bold text-warning-600">{{ $absensis->get('sakit', collect())->count() }}</td>
                <td class="text-center font-bold text-error-600">{{ $absensis->get('alpa', collect())->count() }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h3 class="card-title">III. Penilaian Tilawah, Tajwid, dan Adab</h3>
    <table class="table w-full">
        <thead>
            <tr class="border-b border-neutral-300">
                <th class="text-left py-2 px-3">Aspek Penilaian</th>
                <th class="text-center py-2 px-3">Predikat</th>
                <th class="text-center py-2 px-3">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-neutral-200">
                <td class="py-2 px-3">Tilawah (Kelancaran & Makhraj)</td>
                <td class="text-center py-2 px-3">{{ $santri->penilaian_tilawah ?? '-' }}</td>
                <td class="py-2 px-3">{{ $santri->catatan_tilawah ?? '-' }}</td>
            </tr>
            <tr class="border-b border-neutral-200">
                <td class="py-2 px-3">Tajwid (Idgham, Ikhfa, Qalqalah, dll)</td>
                <td class="text-center py-2 px-3">{{ $santri->penilaian_tajwid ?? '-' }}</td>
                <td class="py-2 px-3">{{ $santri->catatan_tajwid ?? '-' }}</td>
            </tr>
            <tr class="border-b border-neutral-200">
                <td class="py-2 px-3">Adab & Khusyuk</td>
                <td class="text-center py-2 px-3">{{ $santri->penilaian_adab ?? '-' }}</td>
                <td class="py-2 px-3">{{ $santri->catatan_adab ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h3 class="card-title">IV. Pengesahan</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="width: 33%; vertical-align: bottom; padding-bottom: 40px;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Wali Santri</p>
                <p class="mt-8 font-medium text-neutral-900 border-t border-neutral-400 pt-2">{{ $santri->wali?->user?->name ?? '________________________' }}</p>
            </td>
            <td style="width: 34%; text-align: center; vertical-align: bottom; padding-bottom: 40px;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Musyrif</p>
                <p class="mt-8 font-medium text-neutral-900 border-t border-neutral-400 pt-2">{{ $santri->kelompok?->ustadz?->user?->name ?? '________________________' }}</p>
            </td>
            <td style="width: 33%; text-align: right; vertical-align: bottom; padding-bottom: 40px;">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Kepala Tahfidz</p>
                <p class="mt-8 font-medium text-neutral-900 border-t border-neutral-400 pt-2">{{ $kepala?->nama ?? '________________________' }}</p>
            </td>
        </tr>
    </table>
</div>

<div class="page-footer">
    Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} oleh Admin {{ auth()->user()->name ?? 'Admin' }} | SIMBA TAHFIDZ
</div>
@endsection
