<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wali;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaliController extends Controller
{
    public function index(Request $request)
    {
        $query = Wali::with('user', 'santris.kelompok');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('username', 'like', "%{$search}%"));
            });
        }

        $totalWali = (clone $query)->count();

        $walis = $query->latest()->paginate(15)->withQueryString();

        $viewData = [
            'title' => 'Data Wali Santri',
            'pageTitle' => 'Data Wali Santri',
            'walis' => $walis,
            'filters' => $request->only(['search']),
            'summary' => [
                'total' => $totalWali,
                'aktif' => $totalWali,
            ],
        ];

        if ($request->boolean('partial')) {
            $html = view('admin.walis.partials.results', $viewData)->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.walis.index', $viewData);
    }

    public function create()
    {
        return view('admin.walis.create', [
            'title' => 'Tambah Wali Santri',
            'pageTitle' => 'Tambah Wali Santri',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'nama' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'wali',
        ]);

        Wali::create([
            'user_id' => $user->id,
            'nama' => $validated['nama'],
            'telepon' => $validated['telepon'],
            'alamat' => $validated['alamat'],
        ]);

        return redirect()->route('admin.walis.index')
            ->with('success', 'Wali santri berhasil ditambahkan.');
    }

    public function show(Wali $wali)
    {
        $wali->load(['user', 'santris.kelompok.ustadz', 'santris.angkatan']);

        return view('admin.walis.show', [
            'title' => 'Detail Wali: ' . $wali->nama,
            'pageTitle' => 'Detail Wali',
            'wali' => $wali,
        ]);
    }

    public function edit(Wali $wali)
    {
        $wali->load('user');

        return view('admin.walis.edit', [
            'title' => 'Edit Wali: ' . $wali->nama,
            'pageTitle' => 'Edit Wali',
            'wali' => $wali,
        ]);
    }

    public function update(Request $request, Wali $wali)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $wali->user_id,
            'password' => 'nullable|string|min:6|confirmed',
            'nama' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $wali->user->update([
            'username' => $validated['username'],
            'name' => $validated['nama'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $wali->user->password,
        ]);

        $wali->update([
            'nama' => $validated['nama'],
            'telepon' => $validated['telepon'],
            'alamat' => $validated['alamat'],
        ]);

        return redirect()->route('admin.walis.index')
            ->with('success', 'Wali santri berhasil diperbarui.');
    }

    public function destroy(Wali $wali)
    {
        $wali->user->delete();
        $wali->delete();

        return redirect()->route('admin.walis.index')
            ->with('success', 'Wali santri berhasil dihapus.');
    }
}