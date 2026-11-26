<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $period = $request->get('periode', 'hari_ini');
        $santriId = $request->get('santri_id');
        $jenis = $request->get('jenis');
        $nilai = $request->get('nilai');

        $now = Carbon::now();
        switch ($period) {
            case 'pekan_ini':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'bulan_ini':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'kustom':
                $start = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai)->startOfDay() : $now->copy()->startOfDay();
                $end = $request->filled('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai)->endOfDay() : $now->copy()->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }

        $santris = Santri::whereIn('kelompok_id', $ustadz?->kelompoks->pluck('id') ?? [])
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $query = Setoran::with(['santri', 'surah', 'ustadz'])
            ->whereIn('santri_id', $santris->pluck('id'))
            ->whereBetween('tanggal', [$start, $end])
            ->where('is_draft', false)
            ->latest('tanggal');

        if ($santriId) {
            $query->where('santri_id', $santriId);
        }
        if ($jenis) {
            $query->where('jenis', $jenis);
        }
        if ($nilai) {
            $query->where('nilai', $nilai);
        }

        $setorans = $query->paginate(15)->withQueryString();

        $stats = [
            'total_santri' => $santris->count(),
            'total_setoran' => $setorans->total(),
            'volume_ayat' => Setoran::whereIn('santri_id', $santris->pluck('id'))
                ->whereBetween('tanggal', [$start, $end])
                ->where('is_draft', false)
                ->sum(\DB::raw('ayat_akhir - ayat_awal + 1')),
            'rata_mutu' => $this->avgMutuScore($santris->pluck('id'), $start, $end),
        ];

        return view('ustadz.rekap.index', compact(
            'setorans', 'santris', 'stats', 'period', 'santriId', 'jenis', 'nilai',
            'start', 'end'
        ));
    }

    private function avgMutuScore($santriIds, $start, $end): int
    {
        $values = Setoran::whereIn('santri_id', $santriIds)
            ->whereBetween('tanggal', [$start, $end])
            ->where('status', 'approved')
            ->pluck('nilai');

        if ($values->isEmpty()) {
            return 0;
        }

        $scoreMap = [
            'mumtaz' => 100,
            'jayyid_jiddan' => 85,
            'jayyid' => 70,
            'maqbul' => 55,
        ];

        return round($values->map(fn($n) => $scoreMap[$n] ?? 0)->avg());
    }

    public function exportCsv(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $period = $request->get('periode', 'hari_ini');
        $santriId = $request->get('santri_id');
        $jenis = $request->get('jenis');
        $nilai = $request->get('nilai');

        $now = Carbon::now();
        switch ($period) {
            case 'pekan_ini':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'bulan_ini':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'kustom':
                $start = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai)->startOfDay() : $now->copy()->startOfDay();
                $end = $request->filled('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai)->endOfDay() : $now->copy()->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }

        $santris = Santri::whereIn('kelompok_id', $ustadz?->kelompoks->pluck('id') ?? [])
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $query = Setoran::with(['santri', 'surah'])
            ->whereIn('santri_id', $santris->pluck('id'))
            ->whereBetween('tanggal', [$start, $end])
            ->where('is_draft', false);

        if ($santriId) {
            $query->where('santri_id', $santriId);
        }
        if ($jenis) {
            $query->where('jenis', $jenis);
        }
        if ($nilai) {
            $query->where('nilai', $nilai);
        }

        $setorans = $query->latest('tanggal')->get();

        $headers = [
            'No',
            'Santri',
            'NIS',
            'Tanggal',
            'Jenis',
            'Surah',
            'Ayat',
            'Juz',
            'Volume',
            'Predikat',
            'Catatan',
        ];

        $callback = function () use ($setorans, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            $i = 1;
            foreach ($setorans as $setoran) {
                fputcsv($file, [
                    $i++,
                    $setoran->santri->nama ?? '-',
                    $setoran->santri->nis ?? '-',
                    $setoran->tanggal->format('d/m/Y'),
                    ucfirst($setoran->jenis),
                    $setoran->surah->nama_latin ?? '-',
                    $setoran->ayat_range,
                    $setoran->juz,
                    $setoran->ayat_akhir - $setoran->ayat_awal + 1,
                    ucwords(str_replace('_', ' ', $setoran->nilai)),
                    $setoran->catatan ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap_capaian_' . now()->format('Ymd_His') . '.csv"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $period = $request->get('periode', 'hari_ini');
        $santriId = $request->get('santri_id');
        $jenis = $request->get('jenis');
        $nilai = $request->get('nilai');

        $now = Carbon::now();
        switch ($period) {
            case 'pekan_ini':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'bulan_ini':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'kustom':
                $start = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai)->startOfDay() : $now->copy()->startOfDay();
                $end = $request->filled('tanggal_selesai') ? Carbon::parse($request->tanggal_selesai)->endOfDay() : $now->copy()->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }

        $santris = Santri::whereIn('kelompok_id', $ustadz?->kelompoks->pluck('id') ?? [])
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $query = Setoran::with(['santri', 'surah'])
            ->whereIn('santri_id', $santris->pluck('id'))
            ->whereBetween('tanggal', [$start, $end])
            ->where('is_draft', false);

        if ($santriId) {
            $query->where('santri_id', $santriId);
        }
        if ($jenis) {
            $query->where('jenis', $jenis);
        }
        if ($nilai) {
            $query->where('nilai', $nilai);
        }

        $setorans = $query->latest('tanggal')->get();

        $pdf = \PDF::loadView('ustadz.rekap.pdf', compact('setorans', 'start', 'end'));
        return $pdf->download('rekap_capaian_' . now()->format('Ymd_His') . '.pdf');
    }
}