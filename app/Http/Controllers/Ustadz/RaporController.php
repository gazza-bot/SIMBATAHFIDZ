<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $santriId = $request->get('santri_id');
        $start = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->filled('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai)->endOfDay() : Carbon::now()->endOfMonth();

        $selectedSantri = $santriId ? $santris->find($santriId) : null;

        if ($selectedSantri) {
            $setorans = Setoran::with(['surah'])
                ->where('santri_id', $santriId)
                ->whereBetween('tanggal', [$start, $end])
                ->where('is_draft', false)
                ->where('status', 'approved')
                ->latest('tanggal')
                ->get();

            $absensis = Absensi::where('santri_id', $santriId)
                ->whereBetween('tanggal', [$start, $end])
                ->get()
                ->groupBy('status');

            $progressJuz = $selectedSantri->progress_juz;
            $progressPercent = $selectedSantri->progress_percent;
        } else {
            $setorans = collect();
            $absensis = collect();
            $progressJuz = 0;
            $progressPercent = 0;
        }

        return view('ustadz.rapor.index', compact(
            'santris', 'selectedSantri', 'setorans', 'absensis',
            'progressJuz', 'progressPercent', 'start', 'end', 'santriId'
        ));
    }
}