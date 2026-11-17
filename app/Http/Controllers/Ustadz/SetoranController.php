<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\JuzStart;
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

    public function create(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        $kelompokIds = $ustadz?->kelompoks->pluck('id')->toArray() ?? [];
        $santris = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        $prefillSantriId = null;
        if ($request->filled('santri')) {
            $prefillSantriId = (int) $request->get('santri');
            if (!$santris->where('id', $prefillSantriId)->first()) {
                $prefillSantriId = null;
            }
        }

        $juzSurahMap = $this->buildJuzSurahMap();

        return view('ustadz.setorans.create', compact('santris', 'juzSurahMap', 'prefillSantriId'));
    }

    private function buildJuzSurahMap(): array
    {
        $surahs = Surah::orderBy('nomor')->get(['id', 'nomor', 'nama_latin', 'jumlah_ayat']);
        $starts = JuzStart::with('surah:id,nomor')->orderBy('juz')->get();

        $map = [];
        foreach ($starts as $i => $start) {
            $juz = $start->juz;
            $startSurahNomor = $start->surah->nomor;
            $startAyat = $start->ayat;

            if ($i + 1 < $starts->count()) {
                $next = $starts[$i + 1];
                $endSurahNomor = $next->surah->nomor;
                $endAyat = $next->ayat - 1;
            } else {
                $last = $surahs->last();
                $endSurahNomor = $last->nomor;
                $endAyat = $last->jumlah_ayat;
            }

            $items = [];
            foreach ($surahs as $s) {
                if ($s->nomor < $startSurahNomor || $s->nomor > $endSurahNomor) {
                    continue;
                }
                $min = ($s->nomor === $startSurahNomor) ? $startAyat : 1;
                $max = ($s->nomor === $endSurahNomor) ? $endAyat : $s->jumlah_ayat;
                $items[] = [
                    'id' => $s->id,
                    'nomor' => $s->nomor,
                    'nama' => $s->nama_latin,
                    'jumlah_ayat' => $s->jumlah_ayat,
                    'min' => $min,
                    'max' => $max,
                ];
            }
            $map[$juz] = $items;
        }
        return $map;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'jenis' => 'required|in:ziyadah,murajaah,tasmi',
            'surah_id' => 'required|exists:surahs,id',
            'juz_pilih' => 'required|integer|min:1|max:30',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'nilai' => 'required|in:maqbul,jayyid,jayyid_jiddan,mumtaz',
            'skor' => 'nullable|integer|min:0|max:100',
            'tuntas_lanjut' => 'boolean',
            'is_draft' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $surah = Surah::findOrFail($validated['surah_id']);
        $juzSurahMap = $this->buildJuzSurahMap();

        if (!$juzSurahMap[$validated['juz_pilih']] || !collect($juzSurahMap[$validated['juz_pilih']])->contains('id', $validated['surah_id'])) {
            return back()->withErrors(['surah_id' => "Surah ini tidak termasuk Juz {$validated['juz_pilih']}. Silakan pilih Juz yang sesuai."])->withInput();
        }

        if ($validated['ayat_akhir'] > $surah->jumlah_ayat) {
            return back()->withErrors(['ayat_akhir' => "Ayat akhir tidak boleh melebihi {$surah->jumlah_ayat}."])->withInput();
        }

        if ($validated['ayat_awal'] > $validated['ayat_akhir']) {
            return back()->withErrors(['ayat_awal' => 'Ayat awal tidak boleh melebihi ayat akhir.'])->withInput();
        }

        $juzList = $juzSurahMap[$validated['juz_pilih']];
        $item = collect($juzList)->firstWhere('id', $validated['surah_id']);
        if ($validated['ayat_awal'] < $item['min'] || $validated['ayat_akhir'] > $item['max']) {
            return back()->withErrors([
                'ayat_awal' => "Jumlah ayat harus berada di antara {$item['min']} dan {$item['max']} untuk juz {$validated['juz_pilih']} pilihan.",
            ])->withInput();
        }

        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403, 'Akun ustadz tidak ditemukan.');
        }

        $validated['ustadz_id'] = $ustadz->id;
        $validated['juz'] = (int) $validated['juz_pilih'];
        unset($validated['juz_pilih']);
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

        $juzSurahMap = $this->buildJuzSurahMap();

        return view('ustadz.setorans.edit', compact('setoran', 'santris', 'juzSurahMap'));
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
            'juz_pilih' => 'required|integer|min:1|max:30',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'nilai' => 'required|in:maqbul,jayyid,jayyid_jiddan,mumtaz',
            'skor' => 'nullable|integer|min:0|max:100',
            'tuntas_lanjut' => 'boolean',
            'is_draft' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $surah = Surah::findOrFail($validated['surah_id']);
        $juzSurahMap = $this->buildJuzSurahMap();

        if (!$juzSurahMap[$validated['juz_pilih']] || !collect($juzSurahMap[$validated['juz_pilih']])->contains('id', $validated['surah_id'])) {
            return back()->withErrors(['surah_id' => "Surah ini tidak termasuk Juz {$validated['juz_pilih']}. Silakan pilih Juz yang sesuai."])->withInput();
        }

        if ($validated['ayat_akhir'] > $surah->jumlah_ayat) {
            return back()->withErrors(['ayat_akhir' => "Ayat akhir tidak boleh melebihi {$surah->jumlah_ayat}."])->withInput();
        }

        if ($validated['ayat_awal'] > $validated['ayat_akhir']) {
            return back()->withErrors(['ayat_awal' => 'Ayat awal tidak boleh melebihi ayat akhir.'])->withInput();
        }

        $juzList = $juzSurahMap[$validated['juz_pilih']];
        $item = collect($juzList)->firstWhere('id', $validated['surah_id']);
        if ($validated['ayat_awal'] < $item['min'] || $validated['ayat_akhir'] > $item['max']) {
            return back()->withErrors([
                'ayat_awal' => "Jumlah ayat harus berada di antara {$item['min']} dan {$item['max']} untuk juz {$validated['juz_pilih']} pilihan.",
            ])->withInput();
        }

        $validated['juz'] = (int) $validated['juz_pilih'];
        unset($validated['juz_pilih']);

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

    public function sukses(Setoran $setoran)
    {
        return view('ustadz.setorans.sukses', ['setoran' => $setoran]);
    }
}
