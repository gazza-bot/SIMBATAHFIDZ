<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\SimaanSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MurajaahController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $filter = $request->get('filter', 'semua');

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->with(['setorans' => function ($q) {
                $q->where('jenis', 'murajaah')
                  ->where('status', 'approved')
                  ->latest('tanggal')
                  ->take(1);
            }])
            ->get();

        $santriData = [];
        foreach ($santris as $santri) {
            $lastMurajaah = $santri->setorans()
                ->where('jenis', 'murajaah')
                ->where('status', 'approved')
                ->latest('tanggal')
                ->first();

            $daysSince = $lastMurajaah ? Carbon::parse($lastMurajaah->tanggal)->diffInDays(Carbon::now()) : null;
            $priority = $daysSince !== null && $daysSince > 21 ? 'high' : ($daysSince !== null && $daysSince > 14 ? 'medium' : 'low');

            if ($filter === 'prioritas' && $priority !== 'high') {
                continue;
            }
            if ($filter === '3minggu' && $daysSince !== null && $daysSince <= 21) {
                continue;
            }

            $santriData[] = [
                'santri' => $santri,
                'last_murajaah' => $lastMurajaah,
                'days_since' => $daysSince,
                'priority' => $priority,
            ];
        }

        usort($santriData, function ($a, $b) {
            return ($b['days_since'] ?? 0) <=> ($a['days_since'] ?? 0);
        });

        return view('ustadz.murajaahs.index', compact('santriData', 'filter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        SimaanSchedule::create([
            'santri_id' => $request->santri_id,
            'ustadz_id' => Auth::user()->ustadz->id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'dijadwalkan',
        ]);

        return back()->with('success', 'Sima\'an berhasil dijadwalkan.');
    }
}