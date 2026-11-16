<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\Ustadz;
use App\Models\Angkatan;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelompok::with(['ustadz.user', 'angkatan', 'santris']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ustadz_id')) {
            $query->where('ustadz_id', $request->ustadz_id);
        }

        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }

        $kelompoks = $query->latest()->paginate(15)->withQueryString();

        $ustadzs = Ustadz::with('user')->get();
        $angkatans = Angkatan::orderBy('nama')->get();

        return view('admin.kelompoks.index', [
            'title' => 'Kelompok Halaqah',
            'pageTitle' => 'Kelompok Halaqah',
            'kelompoks' => $kelompoks,
            'ustadzs' => $ustadzs,
            'angkatans' => $angkatans,
            'filters' => $request->only(['search', 'ustadz_id', 'angkatan_id']),
        ]);
    }

    public function create()
    {
        return view('admin.kelompoks.create', [
            'title' => 'Tambah Kelompok Halaqah',
            'pageTitle' => 'Tambah Kelompok Halaqah',
            'ustadzs' => Ustadz::with('user')->get(),
            'angkatans' => Angkatan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'ustadz_id' => 'nullable|exists:ustadzs,id',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        Kelompok::create($validated);

        return redirect()->route('admin.kelompoks.index')
            ->with('success', 'Kelompok halaqah berhasil ditambahkan.');
    }

    public function show(Kelompok $kelompok)
    {
        $kelompok->load(['ustadz.user', 'angkatan', 'santris.wali.user']);

        return view('admin.kelompoks.show', [
            'title' => 'Detail Kelompok: ' . $kelompok->nama,
            'pageTitle' => 'Detail Kelompok',
            'kelompok' => $kelompok,
        ]);
    }

    public function edit(Kelompok $kelompok)
    {
        return view('admin.kelompoks.edit', [
            'title' => 'Edit Kelompok: ' . $kelompok->nama,
            'pageTitle' => 'Edit Kelompok',
            'kelompok' => $kelompok,
            'ustadzs' => Ustadz::with('user')->get(),
            'angkatans' => Angkatan::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Kelompok $kelompok)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'ustadz_id' => 'nullable|exists:ustadzs,id',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $kelompok->update($validated);

        return redirect()->route('admin.kelompoks.index')
            ->with('success', 'Kelompok halaqah berhasil diperbarui.');
    }

    public function destroy(Kelompok $kelompok)
    {
        $kelompok->delete();

        return redirect()->route('admin.kelompoks.index')
            ->with('success', 'Kelompok halaqah berhasil dihapus.');
    }
}