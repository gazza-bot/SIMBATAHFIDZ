<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\HalaqahNote;
use App\Models\Santri;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $allSantris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $totalSantri = $allSantris->count();
        $totalSetoran = Setoran::whereIn('santri_id', $allSantris->pluck('id'))
            ->where('status', 'approved')
            ->count();
        $totalMenunggu = Setoran::whereIn('santri_id', $allSantris->pluck('id'))
            ->where('status', 'pending')
            ->count();

        $hadirToday = Absensi::whereIn('santri_id', $allSantris->pluck('id'))
            ->where('tanggal', $today)
            ->where('status', 'hadir')
            ->count();

        $setoranHariIni = Setoran::with('surah')
            ->whereIn('santri_id', $allSantris->pluck('id'))
            ->where('tanggal', $today)
            ->where('is_draft', false)
            ->get()
            ->keyBy('santri_id');

        $sudahSetorToday = $setoranHariIni->count();
        $belumSetorToday = $totalSantri - $sudahSetorToday;

        $mutuValues = Setoran::whereIn('santri_id', $allSantris->pluck('id'))
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
            ->whereIn('santri_id', $allSantris->pluck('id'))
            ->where('status', 'approved')
            ->latest('tanggal')
            ->take(5)
            ->get();

        $targetSimaan = Setoran::whereIn('santri_id', $allSantris->pluck('id'))
            ->where('jenis', 'murajaah')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$weekStart, $weekEnd])
            ->count();

        $halaqahNote = HalaqahNote::where('kelompok_id', $kelompokIds)
            ->where('tanggal', $today)
            ->first();

        $filter = $request->get('filter', 'semua');
        $allowedFilters = ['semua', 'belum', 'sudah'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'semua';
        }

        if ($filter === 'belum') {
            $santris = $allSantris->filter(fn($s) => !$setoranHariIni->has($s->id));
        } elseif ($filter === 'sudah') {
            $santris = $allSantris->filter(fn($s) => $setoranHariIni->has($s->id));
        } else {
            $santris = $allSantris;
        }

        return view('ustadz.dashboard', [
            'title' => 'Dasbor Halaqah',
            'pageTitle' => 'Dasbor Halaqah',
            'ustadz' => $ustadz,
            'kelompokIds' => $kelompokIds,
            'santris' => $santris,
            'allSantris' => $allSantris,
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
            'filter' => $filter,
            'setoranHariIni' => $setoranHariIni,
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