<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Support\JuzCoverage;
use Illuminate\Http\Request;

class PetaHafalanController extends Controller
{
    private JuzCoverage $juzCoverage;

    public function __construct(JuzCoverage $juzCoverage)
    {
        $this->juzCoverage = $juzCoverage;
    }

    public function index(Request $request)
    {
        $angkatanId = $request->integer('angkatan');
        $kelompokId = $request->integer('kelompok');

        if ($angkatanId && ! Angkatan::whereKey($angkatanId)->exists()) {
            $angkatanId = null;
        }

        $query = Santri::where('status_aktif', 'aktif')
            ->with(['angkatan', 'kelompok.ustadz']);

        if ($angkatanId) {
            $query->where('angkatan_id', $angkatanId);
        }

        if ($kelompokId) {
            $query->where('kelompok_id', $kelompokId);
        }

        $santris = $query->orderBy('nama')->get();

        $angkatanList = Angkatan::orderByDesc('tahun_mulai')->get();
        $kelompokList = Kelompok::with('ustadz')->get();

        $santriMaps = [];
        foreach ($santris as $santri) {
            $juzMap = $this->juzCoverage->forSantri($santri);

            $statusMap = [];
            $percentages = [];
            $counts = [
                'mutqin' => 0,
                'parsial' => 0,
                'belum' => 0,
            ];

            for ($juz = 1; $juz <= 30; $juz++) {
                $statusMap[$juz] = $juzMap[$juz]['status'];
                $percentages[$juz] = $juzMap[$juz]['percent'];
                $counts[$juzMap[$juz]['status']] = ($counts[$juzMap[$juz]['status']] ?? 0) + 1;
            }

            $santriMaps[] = [
                'santri' => $santri,
                'statusMap' => $statusMap,
                'percentages' => $percentages,
                'counts' => $counts,
            ];
        }

        return view('kepala.peta-hafalan.index', [
            'title' => 'Peta Hafalan 30 Juz',
            'pageTitle' => 'Peta Hafalan',
            'santriMaps' => $santriMaps,
            'angkatanList' => $angkatanList,
            'kelompokList' => $kelompokList,
            'angkatanId' => $angkatanId,
            'kelompokId' => $kelompokId,
        ]);
    }
}
