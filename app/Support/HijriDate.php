<?php

namespace App\Support;

use Carbon\Carbon;

class HijriDate
{
    private const MONTHS = [
        1 => 'Muharram',
        2 => 'Safar',
        3 => 'Rabiul Awal',
        4 => 'Rabiul Akhir',
        5 => 'Jumadil Awal',
        6 => 'Jumadil Akhir',
        7 => 'Rajab',
        8 => "Sya'ban",
        9 => 'Ramadhan',
        10 => 'Syawal',
        11 => "Dzulqa'dah",
        12 => 'Dzulhijjah',
    ];

    public static function convert(Carbon $date): array
    {
        [$year, $month, $day] = self::gregorianToHijri(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );

        return ['year' => $year, 'month' => $month, 'day' => $day];
    }

    public static function format(Carbon $date): string
    {
        $hijri = self::convert($date);
        $monthName = self::MONTHS[$hijri['month']] ?? '';

        return sprintf('%d %s %d H', $hijri['day'], $monthName, $hijri['year']);
    }

    public static function semester(Carbon $date): string
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        if ($month >= 7) {
            $awal = $year;
            $akhir = $year + 1;
            $nama = 'Ganjil';
        } else {
            $awal = $year - 1;
            $akhir = $year;
            $nama = 'Genap';
        }

        return sprintf('Semester %s TA %d/%d', $nama, $awal, $akhir);
    }

    private static function gregorianToHijri(int $gy, int $gm, int $gd): array
    {
        if (($gy > 1582) || (($gy === 1582) && ($gm > 10)) || (($gy === 1582) && ($gm === 10) && ($gd > 14))) {
            $jd = intdiv(1461 * ($gy + 4800 + intdiv($gm - 14, 12)), 4)
                + intdiv(367 * ($gm - 2 - 12 * intdiv($gm - 14, 12)), 12)
                - intdiv(3 * intdiv($gy + 4900 + intdiv($gm - 14, 12), 100), 4)
                + $gd - 32075;
        } else {
            $jd = 367 * $gy
                - intdiv(7 * ($gy + 5001 + intdiv($gm - 9, 7)), 4)
                + intdiv(275 * $gm, 9)
                + $gd + 1729777;
        }

        $l = $jd - 1948440 + 10632;
        $n = intdiv($l - 1, 10631);
        $l = $l - 10631 * $n + 354;
        $j = (intdiv(10985 - $l, 5316)) * (intdiv(50 * $l, 17719))
            + (intdiv($l, 5670)) * (intdiv(43 * $l, 15238));
        $l = $l - (intdiv(30 - $j, 15)) * (intdiv(17719 * $j, 50))
            - (intdiv($j, 16)) * (intdiv(15238 * $j, 43)) + 29;
        $m = intdiv(24 * $l, 709);
        $d = $l - intdiv(709 * $m, 24);
        $y = 30 * $n + $j - 30;

        return [$y, $m, $d];
    }
}
