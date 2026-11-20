<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Kelompok;
use App\Models\Ustadz;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $days30Start = Carbon::now()->subDays(29);

        // Overall stats
        $totalSantri = Santri::where('status_aktif', 'aktif')->count();
        $totalKelompok = Kelompok::count();
        $totalUstadz = Ustadz::count();

        $totalSetoranThisMonth = Setoran::where('status', 'approved')
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->count();

        // Attendance rate this month
        $attendanceStats = Absensi::whereBetween('tanggal', [$monthStart, $monthEnd])
            ->selectRaw('
                sum(case when status = "hadir" then 1 else 0 end) as hadir,
                count(*) as total
            ')
            ->first();
        $attendanceRate = $attendanceStats->total > 0
            ? round(($attendanceStats->hadir / $attendanceStats->total) * 100)
            : 0;

        // Chart 1: Progress Hafalan per Juz (Mutqin)
        // Use accessor via collection map since progress_juz is not a DB column
        $allSantris = Santri::where('status_aktif', 'aktif')->get();
        $progressPerJuz = $allSantris
            ->map(fn($s) => $s->progress_juz)
            ->countBy();
        
        $juzLabels = [];
        $juzData = [];
        $juzBgColors = [];
        $juzBorderColors = [];
        for ($i = 1; $i <= 30; $i++) {
            $juzLabels[] = 'Juz ' . $i;
            $juzData[] = $progressPerJuz->get($i - 1, 0);
            if ($i >= 25) {
                $juzBgColors[] = '#31533B';
                $juzBorderColors[] = '#274230';
            } elseif ($i >= 20) {
                $juzBgColors[] = '#3C6749';
                $juzBorderColors[] = '#31533B';
            } elseif ($i >= 15) {
                $juzBgColors[] = '#4A7C59';
                $juzBorderColors[] = '#3D6B4A';
            } else {
                $juzBgColors[] = '#6B997A';
                $juzBorderColors[] = '#4A7C59';
            }
        }

        // Chart 2: Kehadiran 30 hari
        $attendanceDaily = Absensi::whereBetween('tanggal', [$days30Start, $today])
            ->selectRaw('tanggal,
                sum(case when status = "hadir" then 1 else 0 end) as hadir,
                count(*) as total
            ')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $attendanceLabels = [];
        $attendanceRates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $days30Start->copy()->addDays($i)->toDateString();
            $attendanceLabels[] = Carbon::parse($date)->format('d M');
            $dayData = $attendanceDaily->get($date);
            $rate = $dayData && $dayData->total > 0
                ? round(($dayData->hadir / $dayData->total) * 100)
                : 0;
            $attendanceRates[] = $rate;
        }

        // Chart 3: Mutu Distribution
        $mutuDist = Setoran::where('status', 'approved')
            ->where('jenis', 'ziyadah')
            ->selectRaw('nilai, count(*) as cnt')
            ->groupBy('nilai')
            ->pluck('cnt', 'nilai');

        $mutuLabels = ['Maqbul', 'Jayyid', 'Jayyid Jiddan', 'Mumtaz'];
        $mutuData = [
            $mutuDist->get('maqbul', 0),
            $mutuDist->get('jayyid', 0),
            $mutuDist->get('jayyid_jiddan', 0),
            $mutuDist->get('mumtaz', 0),
        ];

        // Attention List
        // Criteria: < 75% attendance last 30 days OR > 14 days no murajaah OR progress < 3 juz
        $attentionSantris = Santri::where('status_aktif', 'aktif')
            ->with(['kelompok.ustadz'])
            ->get()
            ->filter(function ($santri) use ($days30Start, $today) {
                // Attendance check
                $absensi = Absensi::where('santri_id', $santri->id)
                    ->whereBetween('tanggal', [$days30Start, $today])
                    ->selectRaw('
                        sum(case when status = "hadir" then 1 else 0 end) as hadir,
                        count(*) as total
                    ')
                    ->first();
                $attRate = $absensi->total > 0 ? ($absensi->hadir / $absensi->total) : 1;
                $lowAttendance = $absensi->total > 0 && $attRate < 0.75;

                // Murajaah check
                $lastMurajaah = $santri->last_murajaah_date;
                $noMurajaah = !$lastMurajaah || Carbon::parse($lastMurajaah)->lt(Carbon::now()->subDays(14));

                // Progress check
                $lowProgress = $santri->progress_juz < 3;

                return $lowAttendance || $noMurajaah || $lowProgress;
            })
            ->values();

        $attentionDetails = $attentionSantris->map(function ($santri) use ($days30Start, $today) {
            $reasons = [];

            $absensi = Absensi::where('santri_id', $santri->id)
                ->whereBetween('tanggal', [$days30Start, $today])
                ->selectRaw('
                    sum(case when status = "hadir" then 1 else 0 end) as hadir,
                    count(*) as total
                ')
                ->first();
            if ($absensi->total > 0 && ($absensi->hadir / $absensi->total) < 0.75) {
                $rate = round(($absensi->hadir / $absensi->total) * 100);
                $reasons[] = "Kehadiran {$rate}% (30 hari)";
            }

            if (!$santri->last_murajaah_date || $santri->last_murajaah_date->lt(Carbon::now()->subDays(14))) {
                $reasons[] = 'Belum murajaah > 14 hari';
            }

            if ($santri->progress_juz < 3) {
                $reasons[] = 'Progres < Juz 3';
            }

            return [
                'santri' => $santri,
                'reasons' => $reasons,
                'kelompok' => $santri->kelompok?->nama,
                'ustadz' => $santri->kelompok?->ustadz?->nama,
            ];
        });

        return view('kepala.dashboard', [
            'title' => 'Dashboard Kepala Tahfidz',
            'pageTitle' => 'Dashboard',
            'totalSantri' => $totalSantri,
            'totalKelompok' => $totalKelompok,
            'totalUstadz' => $totalUstadz,
            'totalSetoranThisMonth' => $totalSetoranThisMonth,
            'attendanceRate' => $attendanceRate,
            'juzLabels' => json_encode($juzLabels),
            'juzData' => json_encode($juzData),
            'attendanceLabels' => json_encode($attendanceLabels),
            'attendanceRates' => json_encode($attendanceRates),
            'mutuLabels' => json_encode($mutuLabels),
            'mutuData' => json_encode($mutuData),
            'juzBgColors' => json_encode($juzBgColors),
            'juzBorderColors' => json_encode($juzBorderColors),
            'attentionSantris' => $attentionDetails,
        ]);
    }
}