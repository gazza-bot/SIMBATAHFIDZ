<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Support\JuzCoverage;
use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Wali;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    private JuzCoverage $juzCoverage;

    public function __construct(JuzCoverage $juzCoverage)
    {
        $this->juzCoverage = $juzCoverage;
    }

    public function index(Request $request)
    {
        $query = Santri::with(['angkatan', 'kelompok.ustadz', 'wali.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $totalSantri = (clone $query)->count();
        $totalAktif = (clone $query)->where('status_aktif', 'aktif')->count();
        $totalNonaktif = (clone $query)->where('status_aktif', 'nonaktif')->count();
        $totalLulus = (clone $query)->where('status_aktif', 'lulus')->count();

        $santris = $query->latest()->paginate(15)->withQueryString();

        $kelompoks = Kelompok::with('ustadz')->get();
        $angkatans = Angkatan::where('status_aktif', true)->get();

        $viewData = [
            'title' => 'Data Santri',
            'pageTitle' => 'Data Santri',
            'santris' => $santris,
            'kelompoks' => $kelompoks,
            'angkatans' => $angkatans,
            'filters' => $request->only(['search', 'kelompok_id', 'angkatan_id', 'status_aktif']),
            'summary' => [
                'total' => $totalSantri,
                'aktif' => $totalAktif,
                'nonaktif' => $totalNonaktif,
                'lulus' => $totalLulus,
            ],
        ];

        if ($request->boolean('partial')) {
            $html = view('admin.santris.partials.results', $viewData)->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.santris.index', $viewData);
    }

    public function create()
    {
        $kelompoks = Kelompok::with('ustadz')->get();
        $angkatans = Angkatan::where('status_aktif', true)->get();
        $walis = Wali::with('user')->get();

        return view('admin.santris.create', [
            'title' => 'Tambah Santri',
            'pageTitle' => 'Tambah Santri',
            'kelompoks' => $kelompoks,
            'angkatans' => $angkatans,
            'walis' => $walis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:santris,nis',
            'nama' => 'required|string|max:100',
            'angkatan_id' => 'required|exists:angkatans,id',
            'kelompok_id' => 'nullable|exists:kelompoks,id',
            'wali_id' => 'nullable|exists:walis,id',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'required|in:aktif,nonaktif,lulus,pindah',
            'tanggal_masuk' => 'nullable|date',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('santris', 'public');
            $validated['foto'] = $path;
        }

        Santri::create($validated);

        return redirect()->route('admin.santris.index')
            ->with('success', 'Santri berhasil ditambahkan.');
    }

    public function show(Santri $santri)
    {
        $santri->load(['angkatan', 'kelompok.ustadz', 'wali.user', 'setorans.surah', 'absensis']);

        $juzMap = $this->juzCoverage->forSantri($santri);

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

        return view('admin.santris.show', [
            'title' => 'Detail Santri: ' . $santri->nama,
            'pageTitle' => 'Detail Santri',
            'santri' => $santri,
            'juzStatus' => $juzStatus,
            'juzPercentages' => $juzPercentages,
            'juzCounts' => $juzCounts,
        ]);
    }

    public function edit(Santri $santri)
    {
        $kelompoks = Kelompok::with('ustadz')->get();
        $angkatans = Angkatan::where('status_aktif', true)->get();
        $walis = Wali::with('user')->get();

        return view('admin.santris.edit', [
            'title' => 'Edit Santri: ' . $santri->nama,
            'pageTitle' => 'Edit Santri',
            'santri' => $santri,
            'kelompoks' => $kelompoks,
            'angkatans' => $angkatans,
            'walis' => $walis,
        ]);
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:santris,nis,' . $santri->id,
            'nama' => 'required|string|max:100',
            'angkatan_id' => 'required|exists:angkatans,id',
            'kelompok_id' => 'nullable|exists:kelompoks,id',
            'wali_id' => 'nullable|exists:walis,id',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'required|in:aktif,nonaktif,lulus,pindah',
            'tanggal_masuk' => 'nullable|date',
        ]);

        if ($request->hasFile('foto')) {
            if ($santri->foto) {
                \Storage::disk('public')->delete($santri->foto);
            }
            $path = $request->file('foto')->store('santris', 'public');
            $validated['foto'] = $path;
        }

        $santri->update($validated);

        return redirect()->route('admin.santris.index')
            ->with('success', 'Santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        if ($santri->foto) {
            \Storage::disk('public')->delete($santri->foto);
        }
        $santri->delete();

        return redirect()->route('admin.santris.index')
            ->with('success', 'Santri berhasil dihapus.');
    }
}