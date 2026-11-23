<?php

namespace App\Support;

use App\Models\JuzStart;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use Illuminate\Support\Collection;

class JuzCoverage
{
    private Collection $juzStarts;
    private Collection $surahs;

    public function __construct()
    {
        $this->juzStarts = JuzStart::with('surah:id,nomor,jumlah_ayat')
            ->orderBy('juz')
            ->get();

        $this->surahs = Surah::orderBy('nomor')
            ->get(['id', 'nomor', 'nama_latin', 'nama_arab', 'jumlah_ayat', 'juz_mulai', 'juz_selesai']);
    }

    public function forSantri(Santri $santri): array
    {
        $setorans = $santri->setorans()
            ->where('status', 'approved')
            ->where('jenis', 'ziyadah')
            ->with('surah:id,nomor,jumlah_ayat')
            ->get();

        $surahCoverage = $this->buildSurahCoverage($setorans);

        $result = [];
        for ($juz = 1; $juz <= 30; $juz++) {
            $result[$juz] = $this->computeJuzStatus($juz, $surahCoverage);
        }

        return $result;
    }

    private function buildSurahCoverage(Collection $setorans): array
    {
        $coverage = [];

        foreach ($setorans as $setoran) {
            $surahId = $setoran->surah_id;
            if (! $surahId) {
                continue;
            }

            $start = (int) $setoran->ayat_awal;
            $end = (int) $setoran->ayat_akhir;

            if ($start > $end) {
                [$start, $end] = [$end, $start];
            }

            if (! isset($coverage[$surahId])) {
                $coverage[$surahId] = [];
            }

            $coverage[$surahId][] = [$start, $end];
        }

        foreach ($coverage as $surahId => $intervals) {
            $coverage[$surahId] = $this->mergeIntervals($intervals);
        }

        return $coverage;
    }

    private function mergeIntervals(array $intervals): array
    {
        if (empty($intervals)) {
            return [];
        }

        usort($intervals, fn ($a, $b) => $a[0] <=> $b[0]);

        $merged = [$intervals[0]];

        for ($i = 1; $i < count($intervals); $i++) {
            $last = &$merged[count($merged) - 1];
            $current = $intervals[$i];

            if ($current[0] <= $last[1] + 1) {
                $last[1] = max($last[1], $current[1]);
            } else {
                $merged[] = $current;
            }
        }

        return $merged;
    }

    private function computeJuzStatus(int $juz, array $surahCoverage): array
    {
        $required = $this->getRequiredAyatRanges($juz);

        if (empty($required)) {
            return [
                'status' => 'belum',
                'percent' => 0,
                'covered_ayat' => 0,
                'total_ayat' => 0,
                'surahs' => [],
            ];
        }

        $totalAyat = 0;
        $coveredAyat = 0;
        $surahDetails = [];

        foreach ($required as $req) {
            $surahId = $req['surah_id'];
            $needStart = $req['start'];
            $needEnd = $req['end'];
            $needCount = $needEnd - $needStart + 1;

            $totalAyat += $needCount;

            $surah = $this->surahs->firstWhere('id', $surahId);
            $surahName = $surah?->nama_latin ?? 'Surah ' . $surahId;
            $surahArab = $surah?->nama_arab ?? '';

            $coveredInSurah = 0;
            if (isset($surahCoverage[$surahId])) {
                foreach ($surahCoverage[$surahId] as [$covStart, $covEnd]) {
                    $overlapStart = max($needStart, $covStart);
                    $overlapEnd = min($needEnd, $covEnd);
                    if ($overlapStart <= $overlapEnd) {
                        $coveredInSurah += $overlapEnd - $overlapStart + 1;
                    }
                }
            }

            $coveredAyat += min($coveredInSurah, $needCount);

            $surahDetails[] = [
                'surah_id' => $surahId,
                'surah_nomor' => $surah?->nomor ?? 0,
                'surah_nama_latin' => $surahName,
                'surah_nama_arab' => $surahArab,
                'surah_jumlah_ayat' => $surah?->jumlah_ayat ?? 0,
                'required_start' => $needStart,
                'required_end' => $needEnd,
                'required_count' => $needCount,
                'covered' => min($coveredInSurah, $needCount),
            ];
        }

        $percent = $totalAyat > 0 ? (int) round(($coveredAyat / $totalAyat) * 100) : 0;

        if ($percent >= 100) {
            $status = 'mutqin';
        } elseif ($percent > 0) {
            $status = 'parsial';
        } else {
            $status = 'belum';
        }

        return [
            'status' => $status,
            'percent' => $percent,
            'covered_ayat' => $coveredAyat,
            'total_ayat' => $totalAyat,
            'surahs' => $surahDetails,
        ];
    }

    private function getRequiredAyatRanges(int $juz): array
    {
        $startRecord = $this->juzStarts->firstWhere('juz', $juz);
        if (! $startRecord) {
            return [];
        }

        $nextRecord = $this->juzStarts->firstWhere('juz', $juz + 1);

        $startSurahId = $startRecord->surah_id;
        $startAyat = $startRecord->ayat;

        $endSurahId = $nextRecord ? $nextRecord->surah_id : null;
        $endAyat = $nextRecord ? $nextRecord->ayat - 1 : null;

        $required = [];

        if ($nextRecord && $nextRecord->surah_id === $startSurahId) {
            $required[] = [
                'surah_id' => $startSurahId,
                'start' => $startAyat,
                'end' => $endAyat,
            ];
            return $required;
        }

        $startSurah = $this->surahs->firstWhere('id', $startSurahId);
        if (! $startSurah) {
            return [];
        }

        $required[] = [
            'surah_id' => $startSurahId,
            'start' => $startAyat,
            'end' => $startSurah->jumlah_ayat,
        ];

        if ($endSurahId) {
            $currentSurahId = $startSurahId;
            while ($currentSurahId !== $endSurahId) {
                $currentSurah = $this->surahs->firstWhere('id', $currentSurahId);
                if (! $currentSurah) {
                    break;
                }

                $nextSurah = $this->surahs->firstWhere('nomor', $currentSurah->nomor + 1);
                if (! $nextSurah) {
                    break;
                }

                if ($nextSurah->id === $endSurahId) {
                    break;
                }

                $required[] = [
                    'surah_id' => $nextSurah->id,
                    'start' => 1,
                    'end' => $nextSurah->jumlah_ayat,
                ];

                $currentSurahId = $nextSurah->id;
            }

            $endSurah = $this->surahs->firstWhere('id', $endSurahId);
            if ($endSurah) {
                $required[] = [
                    'surah_id' => $endSurahId,
                    'start' => 1,
                    'end' => $endAyat,
                ];
            }
        }

        return $required;
    }
}