<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Setoran;
use App\Support\JuzCoverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class RaporController extends Controller
{
    private JuzCoverage $juzCoverage;

    public function __construct(JuzCoverage $juzCoverage)
    {
        $this->juzCoverage = $juzCoverage;
    }

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

            $juzMap = $this->juzCoverage->forSantri($selectedSantri);

            $juzStatus = [];
            $juzPercentages = [];
            $juzCounts = [
                'mutqin' => 0,
                'parsial' => 0,
                'belum' => 0,
            ];

            for ($juz = 1; $juz <= 30; $juz++) {
                $juzStatus[$juz] = $juzMap[$juz]['status'];
                $juzPercentages[$juz] = $juzMap[$juz]['percent'];
                $juzCounts[$juzMap[$juz]['status']] = ($juzCounts[$juzMap[$juz]['status']] ?? 0) + 1;
            }
        } else {
            $setorans = collect();
            $absensis = collect();
            $progressJuz = 0;
            $progressPercent = 0;
            $juzStatus = [];
            $juzPercentages = [];
            $juzCounts = [
                'mutqin' => 0,
                'parsial' => 0,
                'belum' => 0,
            ];
        }

        return view('ustadz.rapor.index', compact(
            'santris', 'selectedSantri', 'setorans', 'absensis',
            'progressJuz', 'progressPercent', 'start', 'end', 'santriId',
            'juzStatus', 'juzPercentages', 'juzCounts'
        ));
    }

    public function exportPdf(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $santriId = $request->get('santri_id');
        if (!$santriId) {
            return redirect()->route('ustadz.rapor.index')->with('error', 'Santri tidak dipilih.');
        }

        $santri = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->where('id', $santriId)
            ->first();

        if (!$santri) {
            return redirect()->route('ustadz.rapor.index')->with('error', 'Santri tidak ditemukan atau tidak di hak akses Anda.');
        }

        $start = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->filled('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai)->endOfDay() : Carbon::now()->endOfMonth();

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

        $progressJuz = $santri->progress_juz;
        $progressPercent = $santri->progress_percent;

        $inline = $request->boolean('inline');

        $pdf = PDF::loadView('ustadz.rapor.pdf', compact(
            'santri', 'setorans', 'absensis',
            'progressJuz', 'progressPercent', 'start', 'end'
        ));

        $filename = 'rapor_' . $santri->nis . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf';

        if ($inline) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}