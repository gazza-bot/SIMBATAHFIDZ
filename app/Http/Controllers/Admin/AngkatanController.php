<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Angkatan;
use Illuminate\Http\Request;

class AngkatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Angkatan::withCount(['kelompoks', 'santris']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('tahun_mulai', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->boolean('status_aktif'));
        }

        $totalAngkatan = (clone $query)->count();
        $totalAktif = (clone $query)->where('status_aktif', true)->count();

        $angkatans = $query->latest()->paginate(15)->withQueryString();

        $viewData = [
            'title' => 'Angkatan Kelas',
            'pageTitle' => 'Angkatan Kelas',
            'angkatans' => $angkatans,
            'filters' => $request->only(['search', 'status_aktif']),
            'summary' => [
                'total' => $totalAngkatan,
                'aktif' => $totalAktif,
            ],
        ];

        if ($request->boolean('partial')) {
            $html = view('admin.angkatans.partials.results', $viewData)->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.angkatans.index', $viewData);
    }

    public function create()
    {
        return view('admin.angkatans.create', [
            'title' => 'Tambah Angkatan Kelas',
            'pageTitle' => 'Tambah Angkatan Kelas',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tahun_mulai' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'status_aktif' => 'required|boolean',
        ]);

        Angkatan::create($validated);

        return redirect()->route('admin.angkatans.index')
            ->with('success', 'Angkatan kelas berhasil ditambahkan.');
    }

    public function show(Angkatan $angkatan)
    {
        $angkatan->load(['kelompoks.ustadz.user', 'santris.wali.user']);

        return view('admin.angkatans.show', [
            'title' => 'Detail Angkatan: ' . $angkatan->nama,
            'pageTitle' => 'Detail Angkatan',
            'angkatan' => $angkatan,
        ]);
    }

    public function edit(Angkatan $angkatan)
    {
        return view('admin.angkatans.edit', [
            'title' => 'Edit Angkatan: ' . $angkatan->nama,
            'pageTitle' => 'Edit Angkatan',
            'angkatan' => $angkatan,
        ]);
    }

    public function update(Request $request, Angkatan $angkatan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tahun_mulai' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'status_aktif' => 'required|boolean',
        ]);

        $angkatan->update($validated);

        return redirect()->route('admin.angkatans.index')
            ->with('success', 'Angkatan kelas berhasil diperbarui.');
    }

    public function destroy(Angkatan $angkatan)
    {
        $angkatan->delete();

        return redirect()->route('admin.angkatans.index')
            ->with('success', 'Angkatan kelas berhasil dihapus.');
    }
}