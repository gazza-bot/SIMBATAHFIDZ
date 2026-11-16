<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ustadz;
use App\Models\User;
use App\Models\Angkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UstadzController extends Controller
{
    public function index(Request $request)
    {
        $query = Ustadz::with(['user', 'kelompoks.angkatan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('username', 'like', "%{$search}%"));
            });
        }

        $ustadzs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.ustadzs.index', [
            'title' => 'Data Ustadz Musyrif',
            'pageTitle' => 'Data Ustadz Musyrif',
            'ustadzs' => $ustadzs,
            'filters' => $request->only(['search']),
            'angkatans' => Angkatan::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.ustadzs.create', [
            'title' => 'Tambah Ustadz Musyrif',
            'pageTitle' => 'Tambah Ustadz Musyrif',
            'angkatans' => Angkatan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'nip' => 'required|string|max:20|unique:ustadzs,nip',
            'nama' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'angkatan_dibimbing' => 'nullable|exists:angkatans,id',
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'ustadz',
        ]);

        Ustadz::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'nama' => $validated['nama'],
            'telepon' => $validated['telepon'],
        ]);

        return redirect()->route('admin.ustadzs.index')
            ->with('success', 'Ustadz musyrif berhasil ditambahkan.');
    }

    public function show(Ustadz $ustadz)
    {
        $ustadz->load(['user', 'kelompoks.angkatan', 'kelompoks.santris', 'setorans', 'absensis']);

        return view('admin.ustadzs.show', [
            'title' => 'Detail Ustadz: ' . $ustadz->nama,
            'pageTitle' => 'Detail Ustadz',
            'ustadz' => $ustadz,
        ]);
    }

    public function edit(Ustadz $ustadz)
    {
        $ustadz->load('user');

        return view('admin.ustadzs.edit', [
            'title' => 'Edit Ustadz: ' . $ustadz->nama,
            'pageTitle' => 'Edit Ustadz',
            'ustadz' => $ustadz,
            'angkatans' => Angkatan::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Ustadz $ustadz)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $ustadz->user_id,
            'password' => 'nullable|string|min:6|confirmed',
            'nip' => 'required|string|max:20|unique:ustadzs,nip,' . $ustadz->id,
            'nama' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'angkatan_dibimbing' => 'nullable|exists:angkatans,id',
        ]);

        $ustadz->user->update([
            'username' => $validated['username'],
            'name' => $validated['nama'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $ustadz->user->password,
        ]);

        $ustadz->update([
            'nip' => $validated['nip'],
            'nama' => $validated['nama'],
            'telepon' => $validated['telepon'],
        ]);

        return redirect()->route('admin.ustadzs.index')
            ->with('success', 'Ustadz musyrif berhasil diperbarui.');
    }

    public function destroy(Ustadz $ustadz)
    {
        $ustadz->user->delete();
        $ustadz->delete();

        return redirect()->route('admin.ustadzs.index')
            ->with('success', 'Ustadz musyrif berhasil dihapus.');
    }
}