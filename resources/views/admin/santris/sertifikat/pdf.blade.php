@extends('layouts.print')

@section('content')
<div class="page-header">
    <div class="logo">
        <div style="width: 22px; height: 16px; border: 2px solid #FFFFFF; border-radius: 2px; margin: 20px auto 0;">
            <div style="width: 2px; height: 100%; background: #FFFFFF; margin: 0 auto;"></div>
        </div>
    </div>
    <h1>Sertifikat Hafalan Al-Qur'an</h1>
    <p>Pondok Pesantren Darut Taqwa Ponorogo</p>
</div>

<div class="card" style="padding: 36px 32px; text-align: center;">
    <p style="font-size: 9px; letter-spacing: 0.18em; text-transform: uppercase; color: #94A39B; margin: 0 0 6px;">QS. Al-Qamar: 17</p>
    <div style="margin: 0 0 20px;">
        <img src="{{ public_path('images/arabic-al-qamar-17.png') }}" width="240" height="68" alt="QS. Al-Qamar: 17" style="width: 240px; height: 68px;">
    </div>

    <p style="font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #64746B; margin: 0;">Diberikan kepada</p>
    <p style="font-size: 28px; font-weight: 700; color: #1E2922; margin: 10px 0 4px;">{{ $santri->nama }}</p>
    <p style="font-size: 12px; color: #64746B; margin: 0;">NIS {{ $santri->nis }} | Angkatan {{ $santri->angkatan?->nama ?? '-' }}</p>

    <p style="font-size: 13px; line-height: 1.9; color: #334139; margin: 22px auto 0; max-width: 430px;">
        Dinyatakan telah menyelesaikan program tahfidz 30 Juz di SIMBA TAHFIDZ, Pondok Pesantren Darut Taqwa Ponorogo.
    </p>

    <table style="width: 100%; margin-top: 44px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                <p style="font-size: 12px; color: #64746B; margin: 0;">Kepala Tahfidz</p>
                <div style="height: 56px;"></div>
                <p style="font-size: 14px; font-weight: 600; color: #1E2922; margin: 0; border-top: 1px solid #1E2922; padding-top: 6px;">
                    {{ $kepala?->nama ?? '-' }}
                </p>
            </td>
        </tr>
    </table>
</div>
@endsection
