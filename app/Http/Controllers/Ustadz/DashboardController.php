<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\HalaqahNote;
use App\Models\Santri;
use App\Models\Setoran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->with(['setorans' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->get();

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $totalSantri = $santris->count();
        $totalSetoran = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('status', 'approved')
            ->count();
        $totalMenunggu = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('status', 'pending')
            ->count();

        $hadirToday = Absensi::whereIn('santri_id', $santris->pluck('id'))
            ->where('tanggal', $today)
            ->where('status', 'hadir')
            ->count();

        $sudahSetorToday = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('tanggal', $today)
            ->where('status', '!=', 'draft')
            ->distinct('santri_id')
            ->count('santri_id');

        $belumSetorToday = $totalSantri - $sudahSetorToday;

        $mutuValues = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('status', 'approved')
            ->pluck('nilai');
        $mutuScore = $mutuValues->map(function ($n) {
            return match ($n) {
                'mumtaz' => 100,
                'jayyid_jiddan' => 85,
                'jayyid' => 70,
                'maqbul' => 55,
                default => 0,
            };
        })->avg() ?? 0;

        $lastSetorans = Setoran::with(['santri', 'surah'])
            ->whereIn('santri_id', $santris->pluck('id'))
            ->where('status', 'approved')
            ->latest('tanggal')
            ->take(5)
            ->get();

        $targetSimaan = Setoran::whereIn('santri_id', $santris->pluck('id'))
            ->where('jenis', 'murajaah')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$weekStart, $weekEnd])
            ->count();

        $halaqahNote = HalaqahNote::where('kelompok_id', $kelompokIds)
            ->where('tanggal', $today)
            ->first();

        return view('ustadz.dashboard', [
            'title' => 'Dasbor Halaqah',
            'pageTitle' => 'Dasbor Halaqah',
            'ustadz' => $ustadz,
            'kelompokIds' => $kelompokIds,
            'santris' => $santris,
            'totalSantri' => $totalSantri,
            'totalSetoran' => $totalSetoran,
            'totalMenunggu' => $totalMenunggu,
            'hadirToday' => $hadirToday,
            'sudahSetorToday' => $sudahSetorToday,
            'belumSetorToday' => $belumSetorToday,
            'mutuScore' => round($mutuScore),
            'lastSetorans' => $lastSetorans,
            'targetSimaan' => $targetSimaan,
            'halaqahNote' => $halaqahNote,
        ]);
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:2000',
        ]);

        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        HalaqahNote::updateOrCreate(
            [
                'kelompok_id' => $kelompokIds[0] ?? null,
                'tanggal' => Carbon::today(),
            ],
            [
                'ustadz_id' => $ustadz->id,
                'catatan' => $request->catatan,
            ]
        );

        return back()->with('success', 'Catatan halaqah tersimpan.');
    }
}