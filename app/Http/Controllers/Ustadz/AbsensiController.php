<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $date = $request->get('tanggal', Carbon::today()->toDateString());
        $sesi = $request->get('sesi', 'ashar');

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $existingAbsensi = Absensi::whereIn('santri_id', $santris->pluck('id'))
            ->where('tanggal', $date)
            ->where('sesi', $sesi)
            ->get()
            ->keyBy('santri_id');

        $stats = [
            'total' => $santris->count(),
            'hadir' => $existingAbsensi->where('status', 'hadir')->count(),
            'izin' => $existingAbsensi->where('status', 'izin')->count(),
            'sakit' => $existingAbsensi->where('status', 'sakit')->count(),
            'alpa' => $existingAbsensi->where('status', 'alpa')->count(),
        ];

        return view('ustadz.absensis.index', compact('santris', 'existingAbsensi', 'stats', 'date', 'sesi'));
    }

    public function create(Request $request)
    {
        return redirect()->route('ustadz.absensis.index', $request->only(['tanggal', 'sesi']));
    }

    public function store(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403);
        }

        $kelompokIds = $ustadz->kelompoks->pluck('id')->toArray();

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'sesi' => 'required|in:subuh,ashar,maghrib',
            'absensi' => 'required|array',
            'absensi.*.santri_id' => ['required', Rule::exists('santris', 'id')->whereIn('kelompok_id', $kelompokIds)],
            'absensi.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'absensi.*.waktu_tiba' => 'nullable|date_format:H:i',
            'absensi.*.catatan' => 'nullable|string|max:500',
        ]);

        foreach ($validated['absensi'] as $item) {
            Absensi::updateOrCreate(
                [
                    'santri_id' => $item['santri_id'],
                    'tanggal' => $validated['tanggal'],
                    'sesi' => $validated['sesi'],
                ],
                [
                    'ustadz_id' => $ustadz->id,
                    'status' => $item['status'],
                    'waktu_tiba' => $item['waktu_tiba'] ?? null,
                    'catatan' => $item['catatan'] ?? null,
                ]
            );
        }

        return redirect()->route('ustadz.absensis.index', [
            'tanggal' => $validated['tanggal'],
            'sesi' => $validated['sesi'],
        ])->with('success', 'Presensi berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $month = $request->get('bulan', Carbon::now()->month);
        $year = $request->get('tahun', Carbon::now()->year);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $absensiData = Absensi::whereIn('santri_id', $santris->pluck('id'))
            ->whereBetween('tanggal', [$start, $end])
            ->get()
            ->groupBy('santri_id');

        $rekap = [];
        foreach ($santris as $santri) {
            $absensi = $absensiData->get($santri->id, collect());
            $rekap[] = [
                'santri' => $santri,
                'hadir' => $absensi->where('status', 'hadir')->count(),
                'izin' => $absensi->where('status', 'izin')->count(),
                'sakit' => $absensi->where('status', 'sakit')->count(),
                'alpa' => $absensi->where('status', 'alpa')->count(),
                'total' => $absensi->count(),
            ];
        }

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create()->month($m)->translatedFormat('F')])->all();
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 2);

        return view('ustadz.absensis.rekap', compact('rekap', 'month', 'year', 'months', 'years'));
    }
}