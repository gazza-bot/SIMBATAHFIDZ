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
use PDF;

class MurajaahController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $bulan = $request->get('bulan');
        if ($bulan) {
            try {
                Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            } catch (\Exception $e) {
                $bulan = null;
            }
        }
        if (! $bulan) {
            $bulan = Carbon::now()->format('Y-m');
        }

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->with([
                'kelompok',
                'angkatan',
                'setorans' => function ($q) {
                    $q->where('jenis', 'murajaah')
                      ->where('status', 'approved')
                      ->latest('tanggal')
                      ->take(1);
                }
            ])
            ->orderBy('nama')
            ->get();

        $santriData = [];
        $sudahCount = 0;
        $belumCount = 0;

        foreach ($santris as $santri) {
            $lastMurajaah = $santri->setorans
                ? $santri->setorans->first()
                : null;

            $murajaahBulan = $santri->setorans()
                ->where('jenis', 'murajaah')
                ->where('status', 'approved')
                ->whereMonth('tanggal', Carbon::parse($bulan . '-01')->month)
                ->whereYear('tanggal', Carbon::parse($bulan . '-01')->year)
                ->count();

            $status = $murajaahBulan > 0 ? 'sudah' : 'belum';
            if ($status === 'sudah') {
                $sudahCount++;
            } else {
                $belumCount++;
            }

            $santriData[] = [
                'santri' => $santri,
                'last_murajaah' => $lastMurajaah,
                'count_bulan' => $murajaahBulan,
                'status' => $status,
            ];
        }

        usort($santriData, function ($a, $b) {
            // Sudah first, then by nama
            if ($a['status'] !== $b['status']) {
                return $a['status'] === 'sudah' ? -1 : 1;
            }
            return $a['santri']->nama <=> $b['santri']->nama;
        });

        $total = $sudahCount + $belumCount;
        $persen = $total > 0 ? round(($sudahCount / $total) * 100) : 0;

        return view('ustadz.murajaahs.index', compact(
            'santriData',
            'bulan',
            'sudahCount',
            'belumCount',
            'total',
            'persen'
        ));
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

    public function exportPdf(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $bulan = $request->get('bulan');
        if ($bulan) {
            try {
                Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            } catch (\Exception $e) {
                $bulan = null;
            }
        }
        if (! $bulan) {
            $bulan = Carbon::now()->format('Y-m');
        }

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->with([
                'kelompok',
                'angkatan',
                'setorans' => function ($q) {
                    $q->where('jenis', 'murajaah')
                      ->where('status', 'approved')
                      ->latest('tanggal')
                      ->take(1);
                }
            ])
            ->orderBy('nama')
            ->get();

        $santriData = [];
        $sudahCount = 0;
        $belumCount = 0;

        foreach ($santris as $santri) {
            $lastMurajaah = $santri->setorans
                ? $santri->setorans->first()
                : null;

            $murajaahBulan = $santri->setorans()
                ->where('jenis', 'murajaah')
                ->where('status', 'approved')
                ->whereMonth('tanggal', Carbon::parse($bulan . '-01')->month)
                ->whereYear('tanggal', Carbon::parse($bulan . '-01')->year)
                ->count();

            $status = $murajaahBulan > 0 ? 'sudah' : 'belum';
            if ($status === 'sudah') {
                $sudahCount++;
            } else {
                $belumCount++;
            }

            $santriData[] = [
                'santri' => $santri,
                'last_murajaah' => $lastMurajaah,
                'count_bulan' => $murajaahBulan,
                'status' => $status,
            ];
        }

        usort($santriData, function ($a, $b) {
            if ($a['status'] !== $b['status']) {
                return $a['status'] === 'sudah' ? -1 : 1;
            }
            return $a['santri']->nama <=> $b['santri']->nama;
        });

        $total = $sudahCount + $belumCount;
        $persen = $total > 0 ? round(($sudahCount / $total) * 100) : 0;

        $pdf = PDF::loadView('ustadz.murajaahs.pdf', compact(
            'santriData',
            'bulan',
            'sudahCount',
            'belumCount',
            'total',
            'persen'
        ));

        return $pdf->download('rekap_murajaah_bulanan_' . $bulan . '.pdf');
    }
}