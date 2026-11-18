<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JuzStart;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\Ustadz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SetoranController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = $this->ustadzOrFail();

        $query = Setoran::with(['santri', 'surah', 'ustadz'])
            ->whereHas('santri', fn ($q) => $q->whereIn('kelompok_id', $ustadz->kelompoks->pluck('id')->all()))
            ->where('is_draft', false)
            ->latest('tanggal')
            ->latest('id');

        if (in_array($request->jenis, ['ziyadah', 'murajaah', 'tasmi'], true)) {
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

        return view('ustadz.setorans.index', compact('setorans'));
    }

    public function create(Request $request)
    {
        $ustadz = $this->ustadzOrFail();
        $kelompokIds = $ustadz->kelompoks->pluck('id');

        $kelompoks = $ustadz->kelompoks()
            ->with(['santris' => fn ($q) => $q->where('status_aktif', 'aktif')->orderBy('nama')])
            ->orderBy('nama')
            ->get();

        $selectedSantri = null;

        if ($request->filled('santri')) {
            $selectedSantri = Santri::whereIn('kelompok_id', $kelompokIds)
                ->where('status_aktif', 'aktif')
                ->with(['kelompok', 'angkatan'])
                ->find((int) $request->get('santri'));
        }

        $selectedSantri ??= Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->with(['kelompok', 'angkatan'])
            ->orderBy('nama')
            ->first();

        return view('ustadz.setorans.create', [
            'title' => 'Input Setoran',
            'pageTitle' => 'Input Setoran',
            'kelompoks' => $kelompoks,
            'selectedSantri' => $selectedSantri,
            'santriContext' => $selectedSantri ? $this->buildSantriContext($selectedSantri) : null,
            'todayStats' => $this->buildTodayStats($kelompokIds),
            'juzSurahMap' => $this->buildJuzSurahMap(),
        ]);
    }

    public function store(Request $request)
    {
        $ustadz = $this->ustadzOrFail();
        $kelompokIds = $ustadz->kelompoks->pluck('id')->all();

        $validated = $this->validateSetoran($request, $kelompokIds);

        $validated['ustadz_id'] = $ustadz->id;
        $validated['is_draft'] = false;
        $validated['status'] = 'approved';
        $validated['validated_by'] = Auth::id();
        $validated['validated_at'] = now();

        $setoran = Setoran::create($validated);

        return redirect()
            ->route('ustadz.setorans.sukses', $setoran)
            ->with('success', 'Setoran berhasil disimpan.');
    }

    public function show(Setoran $setoran)
    {
        $this->ensureOwned($setoran);

        $setoran->load(['santri.kelompok', 'santri.angkatan', 'surah', 'ustadz.user', 'validator']);

        return view('ustadz.setorans.show', compact('setoran'));
    }

    public function edit(Setoran $setoran)
    {
        $this->ensureOwned($setoran);
        $ustadz = $this->ustadzOrFail();

        $kelompoks = $ustadz->kelompoks()
            ->with(['santris' => fn ($q) => $q->where('status_aktif', 'aktif')->orderBy('nama')])
            ->orderBy('nama')
            ->get();

        $setoran->loadMissing(['santri.kelompok', 'santri.angkatan', 'surah']);

        return view('ustadz.setorans.edit', [
            'title' => 'Edit Setoran',
            'pageTitle' => 'Edit Setoran',
            'setoran' => $setoran,
            'kelompoks' => $kelompoks,
            'selectedSantri' => $setoran->santri,
            'santriContext' => $setoran->santri ? $this->buildSantriContext($setoran->santri) : null,
            'todayStats' => $this->buildTodayStats($ustadz->kelompoks->pluck('id')),
            'juzSurahMap' => $this->buildJuzSurahMap(),
        ]);
    }

    public function update(Request $request, Setoran $setoran)
    {
        $this->ensureOwned($setoran);
        $ustadz = $this->ustadzOrFail();
        $kelompokIds = $ustadz->kelompoks->pluck('id')->all();

        $validated = $this->validateSetoran($request, $kelompokIds);

        $validated['is_draft'] = false;
        $validated['status'] = 'approved';
        $validated['validated_by'] = Auth::id();
        $validated['validated_at'] = now();

        $setoran->update($validated);

        return redirect()
            ->route('ustadz.setorans.index')
            ->with('success', 'Setoran berhasil diperbarui.');
    }

    public function destroy(Setoran $setoran)
    {
        $this->ensureOwned($setoran);

        $setoran->delete();

        return redirect()
            ->route('ustadz.setorans.index')
            ->with('success', 'Setoran berhasil dihapus.');
    }

    public function sukses(Setoran $setoran)
    {
        $this->ensureOwned($setoran);

        $setoran->load(['santri.kelompok', 'santri.angkatan', 'surah', 'ustadz.user']);

        return view('ustadz.setorans.sukses', compact('setoran'));
    }

    private function ustadzOrFail(): Ustadz
    {
        $ustadz = Auth::user()->ustadz;

        abort_if(! $ustadz, 403, 'Akun ustadz tidak ditemukan.');

        return $ustadz;
    }

    private function ensureOwned(Setoran $setoran): void
    {
        $kelompokIds = Auth::user()->ustadz?->kelompoks->pluck('id')->all() ?? [];
        $setoran->loadMissing('santri');

        if (! $setoran->santri || ! in_array($setoran->santri->kelompok_id, $kelompokIds, true)) {
            abort(403, 'Anda tidak memiliki akses ke setoran ini.');
        }
    }

    private function buildSantriContext(Santri $santri): array
    {
        $approved = Setoran::query()
            ->where('santri_id', $santri->id)
            ->where('status', 'approved')
            ->where('is_draft', false);

        $lastSetoran = (clone $approved)
            ->with('surah')
            ->latest('tanggal')
            ->latest('id')
            ->first();

        $attendanceTotal = Absensi::where('santri_id', $santri->id)->count();
        $attendanceHadir = Absensi::where('santri_id', $santri->id)
            ->where('status', 'hadir')
            ->count();

        $averageScore = (clone $approved)
            ->whereNotNull('skor')
            ->avg('skor');

        $progressJuz = $santri->progress_juz ?? 0;

        return [
            'last_setoran' => $lastSetoran,
            'attendance_rate' => $attendanceTotal > 0
                ? (int) round(($attendanceHadir / $attendanceTotal) * 100)
                : null,
            'attendance_total' => $attendanceTotal,
            'average_score' => $averageScore !== null ? round((float) $averageScore, 1) : null,
            'total_setoran' => (clone $approved)->count(),
            'progress_juz' => $progressJuz,
            'progress_percent' => $santri->progress_percent,
            'next_juz' => min(30, $progressJuz + 1),
        ];
    }

    private function buildTodayStats($kelompokIds): array
    {
        $totalSantri = Santri::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', 'aktif')
            ->count();

        $sudahSetor = Setoran::whereIn(
            'santri_id',
            Santri::whereIn('kelompok_id', $kelompokIds)
                ->where('status_aktif', 'aktif')
                ->select('id')
        )
            ->whereDate('tanggal', today())
            ->where('is_draft', false)
            ->distinct()
            ->count('santri_id');

        return [
            'total_santri' => $totalSantri,
            'sudah_setor' => $sudahSetor,
            'belum_setor' => max(0, $totalSantri - $sudahSetor),
            'percent' => $totalSantri > 0 ? (int) round(($sudahSetor / $totalSantri) * 100) : 0,
        ];
    }

    private function validateSetoran(Request $request, array $kelompokIds): array
    {
        $validated = $request->validate([
            'santri_id' => ['required', Rule::exists('santris', 'id')->whereIn('kelompok_id', $kelompokIds)],
            'tanggal' => 'required|date|before_or_equal:today',
            'jenis' => 'required|in:ziyadah,murajaah,tasmi',
            'surah_id' => 'required|exists:surahs,id',
            'juz_pilih' => 'required|integer|min:1|max:30',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'nilai' => 'required|in:maqbul,jayyid,jayyid_jiddan,mumtaz',
            'skor' => 'nullable|integer|min:0|max:100',
            'tuntas_lanjut' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'santri_id.exists' => 'Santri tidak termasuk kelompok yang Anda ampu.',
        ]);

        $juz = (int) $validated['juz_pilih'];
        $juzSurahMap = $this->buildJuzSurahMap();
        $items = $juzSurahMap[$juz] ?? null;

        if (! $items) {
            throw ValidationException::withMessages([
                'juz_pilih' => 'Pembagian juz belum tersedia. Hubungi admin untuk melengkapi data juz.',
            ]);
        }

        $surah = Surah::findOrFail($validated['surah_id']);
        $item = collect($items)->firstWhere('id', (int) $validated['surah_id']);

        if (! $item) {
            throw ValidationException::withMessages([
                'surah_id' => "Surah ini tidak termasuk Juz {$juz}. Silakan pilih juz yang sesuai.",
            ]);
        }

        if ((int) $validated['ayat_akhir'] > $surah->jumlah_ayat) {
            throw ValidationException::withMessages([
                'ayat_akhir' => "Ayat akhir tidak boleh melebihi {$surah->jumlah_ayat}.",
            ]);
        }

        if ((int) $validated['ayat_awal'] > (int) $validated['ayat_akhir']) {
            throw ValidationException::withMessages([
                'ayat_awal' => 'Ayat awal tidak boleh melebihi ayat akhir.',
            ]);
        }

        if ((int) $validated['ayat_awal'] < $item['min'] || (int) $validated['ayat_akhir'] > $item['max']) {
            throw ValidationException::withMessages([
                'ayat_awal' => "Rentang ayat harus berada di antara {$item['min']} dan {$item['max']} untuk Juz {$juz}.",
            ]);
        }

        $validated['juz'] = $juz;
        $validated['tuntas_lanjut'] = $request->boolean('tuntas_lanjut');
        unset($validated['juz_pilih']);

        return $validated;
    }

    private function buildJuzSurahMap(): array
    {
        $surahs = Surah::orderBy('nomor')->get(['id', 'nomor', 'nama_latin', 'jumlah_ayat']);
        $starts = JuzStart::with('surah:id,nomor')->orderBy('juz')->get()
            ->filter(fn ($start) => $start->surah !== null)
            ->values();

        if ($surahs->isEmpty() || $starts->isEmpty()) {
            return [];
        }

        $map = [];

        foreach ($starts as $i => $start) {
            $juz = $start->juz;
            $startSurahNomor = $start->surah->nomor;
            $startAyat = $start->ayat;

            if ($i + 1 < $starts->count()) {
                $next = $starts[$i + 1];

                if ($next->ayat > 1) {
                    $endSurahNomor = $next->surah->nomor;
                    $endAyat = $next->ayat - 1;
                } else {
                    $endSurahNomor = max(1, $next->surah->nomor - 1);
                    $endAyat = (int) ($surahs->firstWhere('nomor', $endSurahNomor)?->jumlah_ayat ?? 0);
                }
            } else {
                $last = $surahs->last();
                $endSurahNomor = $last->nomor;
                $endAyat = $last->jumlah_ayat;
            }

            $items = [];

            foreach ($surahs as $surah) {
                if ($surah->nomor < $startSurahNomor || $surah->nomor > $endSurahNomor) {
                    continue;
                }

                $min = $surah->nomor === $startSurahNomor ? $startAyat : 1;
                $max = $surah->nomor === $endSurahNomor ? $endAyat : $surah->jumlah_ayat;

                if ($max < $min) {
                    continue;
                }

                $items[] = [
                    'id' => $surah->id,
                    'nomor' => $surah->nomor,
                    'nama' => $surah->nama_latin,
                    'jumlah_ayat' => $surah->jumlah_ayat,
                    'min' => $min,
                    'max' => $max,
                ];
            }

            $map[$juz] = $items;
        }

        return $map;
    }
}
