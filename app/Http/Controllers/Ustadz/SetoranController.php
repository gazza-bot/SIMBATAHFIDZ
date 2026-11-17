<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetoranController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $query = Setoran::with(['santri', 'surah', 'ustadz'])
            ->whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))
            ->where('is_draft', false)
            ->latest('tanggal');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $setorans = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Setoran::whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))->where('is_draft', false)->count(),
            'pending' => Setoran::whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))->where('status', 'pending')->where('is_draft', false)->count(),
            'approved' => Setoran::whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))->where('status', 'approved')->count(),
            'rejected' => Setoran::whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))->where('status', 'rejected')->count(),
        ];

        return view('ustadz.setorans.index', compact('setorans', 'stats'));
    }

    public function create()
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];
        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        $surahs = Surah::orderBy('nomor')->get(['id', 'nama_latin', 'nama_arab', 'jumlah_ayat', 'juz_mulai', 'juz_selesai']);

        return view('ustadz.setorans.create', compact('santris', 'surahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'jenis' => 'required|in:ziyadah,murajaah,tasmi',
            'surah_id' => 'required|exists:surahs,id',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'nilai' => 'required|in:maqbul,jayyid,jayyid_jiddan,mumtaz',
            'skor' => 'nullable|integer|min:0|max:100',
            'tuntas_lanjut' => 'boolean',
            'is_draft' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $surah = Surah::findOrFail($validated['surah_id']);

        if ($validated['ayat_akhir'] > $surah->jumlah_ayat) {
            return back()->withErrors(['ayat_akhir' => "Ayat akhir tidak boleh melebihi {$surah->jumlah_ayat}."])->withInput();
        }

        if ($validated['ayat_awal'] > $validated['ayat_akhir']) {
            return back()->withErrors(['ayat_awal' => 'Ayat awal tidak boleh melebihi ayat akhir.'])->withInput();
        }

        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403, 'Akun ustadz tidak ditemukan.');
        }

        $validated['ustadz_id'] = $ustadz->id;
        $validated['juz'] = Setoran::resolveJuz($surah->nomor, $validated['ayat_awal']);
        $validated['status'] = $validated['is_draft'] ? 'pending' : 'pending';

        $setoran = Setoran::create($validated);

        if ($setoran->is_draft) {
            return redirect()->route('ustadz.setorans.index')
                ->with('success', 'Draft setoran tersimpan.');
        }

        return redirect()->route('ustadz.setorans.sukses', $setoran)
            ->with('success', 'Setoran berhasil ditambahkan.');
    }

    public function show(Setoran $setoran)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        if (!$setoran->santri || !in_array($setoran->santri->kelompok_id, $kelompokIds)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }

        $setoran->load(['santri', 'surah', 'ustadz', 'validator']);

        return view('ustadz.setorans.show', compact('setoran'));
    }

    public function edit(Setoran $setoran)
    {
        if ($setoran->status !== 'pending' || $setoran->is_draft) {
            return redirect()->route('ustadz.setorans.index')
                ->with('error', 'Setoran yang sudah divalidasi atau draft tidak dapat diedit.');
        }

        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        if (!$setoran->santri || !in_array($setoran->santri->kelompok_id, $kelompokIds)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }

        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        $surahs = Surah::orderBy('nomor')->get(['id', 'nama_latin', 'nama_arab', 'jumlah_ayat', 'juz_mulai', 'juz_selesai']);

        return view('ustadz.setorans.edit', compact('setoran', 'santris', 'surahs'));
    }

    public function update(Request $request, Setoran $setoran)
    {
        if ($setoran->status !== 'pending' || $setoran->is_draft) {
            return redirect()->route('ustadz.setorans.index')
                ->with('error', 'Setoran yang sudah divalidasi atau draft tidak dapat diubah.');
        }

        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'jenis' => 'required|in:ziyadah,murajaah,tasmi',
            'surah_id' => 'required|exists:surahs,id',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'nilai' => 'required|in:maqbul,jayyid,jayyid_jiddan,mumtaz',
            'skor' => 'nullable|integer|min:0|max:100',
            'tuntas_lanjut' => 'boolean',
            'is_draft' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $surah = Surah::findOrFail($validated['surah_id']);

        if ($validated['ayat_akhir'] > $surah->jumlah_ayat) {
            return back()->withErrors(['ayat_akhir' => "Ayat akhir tidak boleh melebihi {$surah->jumlah_ayat}."])->withInput();
        }

        if ($validated['ayat_awal'] > $validated['ayat_akhir']) {
            return back()->withErrors(['ayat_awal' => 'Ayat awal tidak boleh melebihi ayat akhir.'])->withInput();
        }

        $validated['juz'] = Setoran::resolveJuz($surah->nomor, $validated['ayat_awal']);

        $setoran->update($validated);

        return redirect()->route('ustadz.setorans.index')
            ->with('success', 'Setoran berhasil diperbarui.');
    }

    public function destroy(Setoran $setoran)
    {
        if ($setoran->status !== 'pending' || $setoran->is_draft) {
            return redirect()->route('ustadz.setorans.index')
                ->with('error', 'Setoran yang sudah divalidasi atau draft tidak dapat dihapus.');
        }

        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        if (!$setoran->santri || !in_array($setoran->santri->kelompok_id, $kelompokIds)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }

        $setoran->delete();

        return redirect()->route('ustadz.setorans.index')
            ->with('success', 'Setoran berhasil dihapus.');
    }

    public function validasi(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        $query = Setoran::with(['santri', 'surah', 'ustadz'])
            ->whereIn('santri_id', Santri::whereIn('kelompok_id', $kelompokIds)->pluck('id'))
            ->where('status', 'pending')
            ->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $setorans = $query->paginate(15)->withQueryString();

        return view('ustadz.setorans.validasi', compact('setorans'));
    }

    public function approve(Setoran $setoran)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        if (!$setoran->santri || !in_array($setoran->santri->kelompok_id, $kelompokIds)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }

        if ($setoran->status !== 'pending') {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }

        $setoran->update([
            'status' => 'approved',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Setoran berhasil disetujui.');
    }

    public function reject(Request $request, Setoran $setoran)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];

        if (!$setoran->santri || !in_array($setoran->santri->kelompok_id, $kelompokIds)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }

        if ($setoran->status !== 'pending') {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }

        $request->validate([
            'catatan_reject' => 'required|string|max:1000',
        ]);

        $catatan = ($setoran->catatan ? $setoran->catatan . "\n" : '') . '[REJECTED] ' . $request->catatan_reject;

        $setoran->update([
            'status' => 'rejected',
            'catatan' => $catatan,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Setoran ditolak dengan catatan.');
    }

    public function sukses(Setoran $setoran)
    {
        return view('ustadz.setorans.sukses', ['setoran' => $setoran]);
    }
}
