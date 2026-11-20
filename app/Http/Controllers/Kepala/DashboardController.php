<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Ustadz;
use App\Support\HijriDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $days30Start = Carbon::now()->subDays(29);

        $santris = Santri::where('status_aktif', 'aktif')
            ->with(['kelompok.ustadz', 'angkatan'])
            ->orderBy('nama')
            ->get();

        $santriIds = $santris->pluck('id');

        $progressMap = Setoran::whereIn('santri_id', $santriIds)
            ->where('jenis', 'ziyadah')
            ->where('status', 'approved')
            ->selectRaw('santri_id, max(juz) as max_juz')
            ->groupBy('santri_id')
            ->pluck('max_juz', 'santri_id');

        $setoranTodayIds = Setoran::whereIn('santri_id', $santriIds)
            ->where('status', 'approved')
            ->whereDate('tanggal', $today)
            ->pluck('santri_id')
            ->unique();

        $setoranYesterdayIds = Setoran::whereIn('santri_id', $santriIds)
            ->where('status', 'approved')
            ->whereDate('tanggal', $today->copy()->subDay())
            ->pluck('santri_id')
            ->unique();

        $absensiToday = Absensi::whereIn('santri_id', $santriIds)
            ->whereDate('tanggal', $today)
            ->get(['santri_id', 'status']);

        $absensi30 = Absensi::whereIn('santri_id', $santriIds)
            ->whereBetween('tanggal', [$days30Start, $today])
            ->get(['santri_id', 'status'])
            ->groupBy('santri_id');

        $setoran30 = Setoran::whereIn('santri_id', $santriIds)
            ->where('status', 'approved')
            ->where('tanggal', '>=', $days30Start)
            ->get(['santri_id', 'nilai', 'jenis', 'tanggal']);

        $totalSantri = $santris->count();
        $totalKelompok = Kelompok::count();
        $totalUstadz = Ustadz::count();
        $kelompokTerisi = Kelompok::whereNotNull('ustadz_id')->count();

        $hadirToday = $absensiToday->where('status', 'hadir')->count();
        $hadirPct = $totalSantri > 0 ? (int) round(($hadirToday / $totalSantri) * 100) : 0;

        $setoranSesi = $setoranTodayIds->count();
        $setoranDelta = $setoranSesi - $setoranYesterdayIds->count();

        $indeksMutu = $this->averageMutu($setoran30);
        $mutuLabel = $this->mutuLabel($indeksMutu);

        $kelompokLengkapPct = $totalKelompok > 0
            ? (int) round(($kelompokTerisi / $totalKelompok) * 100)
            : 0;

        $rataCapaian = $this->averageProgress($progressMap, $santriIds);

        $mutqin30 = $santris->filter(
            fn ($s) => ($progressMap[$s->id] ?? 0) >= 30
        )->count();

        $attention = $this->buildAttention($santris, $progressMap, $absensi30, $setoran30, $days30Start);
        $perluPerhatian = $attention->count();

        $halaqahMonitor = $this->buildHalaqahMonitor(
            $santris,
            $progressMap,
            $setoranTodayIds,
            $absensi30,
            $setoran30
        );

        $angkatanList = Angkatan::orderByDesc('tahun_mulai')->get();
        $selectedAngkatan = $request->integer('angkatan') ?: null;
        if ($selectedAngkatan && !$angkatanList->contains('id', $selectedAngkatan)) {
            $selectedAngkatan = null;
        }

        $histogram = $this->histogramData($santris, $progressMap, $selectedAngkatan);
        $juzRanges = $histogram['juzRanges'];
        $belumMulai = $histogram['belumMulai'];
        $coverageFooter = $histogram['coverageFooter'];

        $activities = Setoran::with(['santri', 'surah', 'ustadz'])
            ->whereIn('santri_id', $santriIds)
            ->where('status', 'approved')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $nearKhatam = $santris
            ->map(fn ($s) => ['santri' => $s, 'progress' => $progressMap[$s->id] ?? 0])
            ->filter(fn ($item) => $item['progress'] > 0)
            ->sortByDesc('progress')
            ->take(4)
            ->values();

        return view('kepala.dashboard', [
            'title' => 'Dashboard Kepala Tahfidz',
            'pageTitle' => 'Ikhtisar Dasbor',
            'hijriDate' => HijriDate::format($today),
            'semester' => HijriDate::semester($today),
            'indicators' => [
                'hadir' => $hadirToday,
                'total_santri' => $totalSantri,
                'hadir_pct' => $hadirPct,
                'setoran_sesi' => $setoranSesi,
                'setoran_delta' => $setoranDelta,
                'indeks_mutu' => $indeksMutu,
                'mutu_label' => $mutuLabel,
                'asatidz' => $totalUstadz,
                'kelompok_lengkap_pct' => $kelompokLengkapPct,
                'kelompok_aktif' => $kelompokTerisi,
                'total_kelompok' => $totalKelompok,
            ],
            'capaian' => [
                'total_santri' => $totalSantri,
                'rata_capaian' => $rataCapaian,
                'mutqin30' => $mutqin30,
                'perlu_perhatian' => $perluPerhatian,
            ],
            'halaqahMonitor' => $halaqahMonitor,
            'angkatanList' => $angkatanList,
            'selectedAngkatan' => $selectedAngkatan,
            'juzRanges' => $juzRanges,
            'belumMulai' => $belumMulai,
            'coverageFooter' => $coverageFooter,
            'activities' => $activities,
            'attention' => $attention,
            'nearKhatam' => $nearKhatam,
        ]);
    }

    public function juzDistribution(Request $request)
    {
        $santris = Santri::where('status_aktif', 'aktif')->get(['id', 'angkatan_id']);

        $progressMap = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('jenis', 'ziyadah')
            ->where('status', 'approved')
            ->selectRaw('santri_id, max(juz) as max_juz')
            ->groupBy('santri_id')
            ->pluck('max_juz', 'santri_id');

        $angkatanId = $request->integer('angkatan') ?: null;
        if ($angkatanId && ! Angkatan::whereKey($angkatanId)->exists()) {
            $angkatanId = null;
        }

        $data = $this->histogramData($santris, $progressMap, $angkatanId);

        return response()->json([
            'selected' => $angkatanId,
            'html' => view('kepala.partials.juz-histogram', $data)->render(),
        ]);
    }

    private function histogramData(Collection $santris, Collection $progressMap, ?int $angkatanId): array
    {
        $scope = $angkatanId
            ? $santris->where('angkatan_id', $angkatanId)
            : $santris;
        $scopeIds = $scope->pluck('id');

        return [
            'juzRanges' => $this->buildProgressDistribution($progressMap, $scopeIds),
            'belumMulai' => $scopeIds
                ->filter(fn ($id) => ($progressMap[$id] ?? 0) === 0)
                ->count(),
            'coverageFooter' => [
                'rata_juz' => $this->averageProgress($progressMap, $scopeIds),
                'siap_khatam' => $scope->filter(
                    fn ($s) => ($progressMap[$s->id] ?? 0) >= 30
                )->count(),
                'jumlah_santri' => $scope->count(),
            ],
        ];
    }

    private function scoreOf(?string $nilai): ?int
    {
        return match ($nilai) {
            'mumtaz' => 100,
            'jayyid_jiddan' => 85,
            'jayyid' => 70,
            'maqbul' => 55,
            default => null,
        };
    }

    private function averageMutu(Collection $setorans): float
    {
        $scores = $setorans
            ->map(fn ($s) => $this->scoreOf($s->nilai))
            ->filter(fn ($v) => $v !== null);

        return $scores->isEmpty() ? 0.0 : round($scores->avg(), 1);
    }

    private function mutuLabel(float $score): string
    {
        if ($score >= 90) {
            return 'Mumtaz (A)';
        }
        if ($score >= 80) {
            return 'Jayyid Jiddan (B)';
        }
        if ($score >= 70) {
            return 'Jayyid (C)';
        }
        if ($score > 0) {
            return 'Maqbul (D)';
        }

        return 'Belum ada data';
    }

    private function averageProgress(Collection $progressMap, Collection $ids): float
    {
        if ($ids->isEmpty()) {
            return 0.0;
        }

        $sum = $ids->sum(fn ($id) => $progressMap[$id] ?? 0);

        return round($sum / $ids->count(), 1);
    }

    private function buildHalaqahMonitor(
        Collection $santris,
        Collection $progressMap,
        Collection $setoranTodayIds,
        Collection $absensi30,
        Collection $setoran30
    ): Collection {
        $santrisByKelompok = $santris->groupBy('kelompok_id');
        $kelompoks = Kelompok::with(['ustadz', 'angkatan'])->orderBy('nama')->get();

        return $kelompoks->map(function ($kelompok) use ($santrisByKelompok, $progressMap, $setoranTodayIds, $absensi30, $setoran30) {
            $anggota = $santrisByKelompok->get($kelompok->id, collect());
            $anggotaIds = $anggota->pluck('id');
            $total = $anggota->count();

            $sudahSetor = $anggotaIds->filter(fn ($id) => $setoranTodayIds->contains($id))->count();
            $progressPct = $total > 0 ? (int) round(($sudahSetor / $total) * 100) : 0;

            $mutu = $this->averageMutu(
                $setoran30->whereIn('santri_id', $anggotaIds)
            );

            $rataJuz = $this->averageProgress($progressMap, $anggotaIds);

            $absensiAnggota = $absensi30
                ->filter(fn ($rows, $key) => $anggotaIds->contains((int) $key))
                ->flatten(1);
            $absensiTotal = $absensiAnggota->count();
            $absensiHadir = $absensiAnggota->where('status', 'hadir')->count();
            $kehadiranPct = $absensiTotal > 0 ? (int) round(($absensiHadir / $absensiTotal) * 100) : 0;

            if ($progressPct >= 90) {
                $statusLabel = 'Lengkap';
                $statusVariant = 'success';
            } elseif ($progressPct >= 50) {
                $statusLabel = 'Sebagian';
                $statusVariant = 'warning';
            } else {
                $statusLabel = 'Belum';
                $statusVariant = 'error';
            }

            return [
                'kelompok' => $kelompok,
                'total' => $total,
                'sudah_setor' => $sudahSetor,
                'progress_pct' => $progressPct,
                'mutu' => $mutu,
                'rata_juz' => $rataJuz,
                'kehadiran_pct' => $kehadiranPct,
                'status_label' => $statusLabel,
                'status_variant' => $statusVariant,
            ];
        });
    }

    private function buildProgressDistribution(Collection $progressMap, Collection $scopeIds): array
    {
        $ranges = [];

        for ($start = 1; $start <= 30; $start += 5) {
            $end = $start + 4;

            $count = $scopeIds->filter(function ($id) use ($progressMap, $start, $end) {
                $progress = (int) ($progressMap[$id] ?? 0);

                return $progress >= $start && $progress <= $end;
            })->count();

            $ranges[] = [
                'label' => "Juz {$start}-{$end}",
                'from' => $start,
                'to' => $end,
                'count' => $count,
            ];
        }

        return $ranges;
    }

    private function buildAttention(
        Collection $santris,
        Collection $progressMap,
        Collection $absensi30,
        Collection $setoran30,
        Carbon $days30Start
    ): Collection {
        $murajaahLast = $setoran30
            ->where('jenis', 'murajaah')
            ->groupBy('santri_id')
            ->map(fn ($rows) => $rows->max('tanggal'));

        $threshold = Carbon::now()->subDays(14);

        return $santris
            ->map(function ($santri) use ($progressMap, $absensi30, $murajaahLast, $threshold) {
                $reasons = [];

                $absensi = $absensi30->get($santri->id, collect());
                $totalAbsensi = $absensi->count();
                if ($totalAbsensi > 0) {
                    $hadir = $absensi->where('status', 'hadir')->count();
                    $rate = (int) round(($hadir / $totalAbsensi) * 100);
                    if ($rate < 75) {
                        $reasons[] = "Kehadiran {$rate}% (30 hari)";
                    }
                }

                $lastMurajaah = $murajaahLast->get($santri->id);
                if (!$lastMurajaah || Carbon::parse($lastMurajaah)->lt($threshold)) {
                    $reasons[] = 'Belum murajaah > 14 hari';
                }

                $progress = $progressMap[$santri->id] ?? 0;
                if ($progress < 3) {
                    $reasons[] = 'Progres < Juz 3';
                }

                return [
                    'santri' => $santri,
                    'progress' => $progress,
                    'kelompok' => $santri->kelompok?->nama,
                    'ustadz' => $santri->kelompok?->ustadz?->nama,
                    'reasons' => $reasons,
                ];
            })
            ->filter(fn ($item) => count($item['reasons']) > 0)
            ->values();
    }
}
