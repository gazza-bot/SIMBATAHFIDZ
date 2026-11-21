<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JuzStart;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\SimaanSchedule;
use App\Models\Surah;
use App\Models\Wali;
use App\Models\WaliMessage;
use App\Support\HijriDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, ?Santri $santri = null)
    {
        $wali = $this->wali();
        $anakans = $wali ? $wali->santris()->with(['kelompok.ustadz.user', 'angkatan'])->get() : collect();

        if ($anakans->isEmpty()) {
            return view('wali.mutabaah', [
                'title' => "Mutaba'ah Anak",
                'pageTitle' => "Mutaba'ah Santri",
                'wali' => $wali,
                'anakans' => $anakans,
                'selectedSantri' => null,
            ]);
        }

        $activeSantri = $this->resolveActiveSantri($santri, $anakans);
        $metrics = $this->calculateMetrics($activeSantri, $wali);

        return view('wali.mutabaah', array_merge([
            'title' => "Mutaba'ah - {$activeSantri->nama}",
            'pageTitle' => 'Ikhtisar Capaian',
            'wali' => $wali,
            'anakans' => $anakans,
            'selectedSantri' => $activeSantri,
            'hijriDate' => HijriDate::format(now()),
            'semester' => HijriDate::semester(now()),
        ], $metrics));
    }

    public function riwayat(Request $request, ?Santri $santri = null)
    {
        $wali = $this->wali();
        $anakans = $wali ? $wali->santris()->with(['kelompok.ustadz', 'angkatan'])->get() : collect();

        if ($anakans->isEmpty()) {
            return redirect()->route('wali.mutabaah');
        }

        $activeSantri = $this->resolveActiveSantri($santri, $anakans);

        $jenis = $request->query('jenis');
        $validJenis = ['ziyadah', 'murajaah', 'tasmi'];
        if (! in_array($jenis, $validJenis, true)) {
            $jenis = null;
        }

        $setoransQuery = $activeSantri->setorans()
            ->with(['surah', 'ustadz'])
            ->where('status', 'approved')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($jenis) {
            $setoransQuery->where('jenis', $jenis);
        }

        $setorans = $setoransQuery->paginate(15)->withQueryString();

        $absensis = $activeSantri->absensis()
            ->with('ustadz')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->take(30)
            ->get();

        return view('wali.riwayat', [
            'title' => "Riwayat Setoran & Presensi - {$activeSantri->nama}",
            'pageTitle' => 'Riwayat Setoran',
            'wali' => $wali,
            'anakans' => $anakans,
            'selectedSantri' => $activeSantri,
            'setorans' => $setorans,
            'absensis' => $absensis,
            'selectedJenis' => $jenis,
            'hijriDate' => HijriDate::format(now()),
            'semester' => HijriDate::semester(now()),
        ]);
    }

    public function petaJuz(Request $request, ?Santri $santri = null)
    {
        $wali = $this->wali();
        $anakans = $wali ? $wali->santris()->with(['kelompok.ustadz', 'angkatan'])->get() : collect();

        if ($anakans->isEmpty()) {
            return redirect()->route('wali.mutabaah');
        }

        $activeSantri = $this->resolveActiveSantri($santri, $anakans);
        $juzData = $this->buildJuzMap($activeSantri);

        return view('wali.peta-juz', [
            'title' => "Peta Hafalan 30 Juz - {$activeSantri->nama}",
            'pageTitle' => 'Peta 30 Juz',
            'wali' => $wali,
            'anakans' => $anakans,
            'selectedSantri' => $activeSantri,
            'juzStatus' => $juzData['statusMap'],
            'juzCounts' => $juzData['counts'],
            'juzDetails' => $juzData['details'],
            'hijriDate' => HijriDate::format(now()),
            'semester' => HijriDate::semester(now()),
        ]);
    }

    public function storeDoa(Request $request, Santri $santri)
    {
        $wali = $this->waliOrFail();

        if ($santri->wali_id !== $wali->id) {
            abort(403, 'Anda tidak memiliki akses ke santri ini.');
        }

        $validated = $request->validate([
            'pesan' => 'required|string|min:5|max:1000',
        ], [
            'pesan.required' => 'Pesan doa tidak boleh kosong.',
            'pesan.min' => 'Pesan doa minimal 5 karakter.',
            'pesan.max' => 'Pesan doa maksimal 1000 karakter.',
        ]);

        WaliMessage::create([
            'wali_id' => $wali->id,
            'santri_id' => $santri->id,
            'pesan' => $validated['pesan'],
            'dibacakan' => false,
        ]);

        return redirect()
            ->route('wali.mutabaah', ['santri' => $santri->id])
            ->with('success', 'Doa dan pesan kasih berhasil dikirim ke Musyrif.');
    }

    private function wali(): ?Wali
    {
        return Auth::user()?->wali;
    }

    private function waliOrFail(): Wali
    {
        $wali = $this->wali();
        if (! $wali) {
            abort(403, 'Akun ini belum terdaftar sebagai wali santri.');
        }

        return $wali;
    }

    private function resolveActiveSantri(?Santri $santri, Collection $anakans): Santri
    {
        if ($santri) {
            $wali = $this->wali();
            if ($santri->wali_id !== $wali?->id) {
                abort(403, 'Anda tidak memiliki akses ke santri ini.');
            }

            return $santri;
        }

        return $anakans->first();
    }

    private function calculateMetrics(Santri $santri, ?Wali $wali): array
    {
        $targetJuz = 15;
        $juzData = $this->buildJuzMap($santri);
        $capaianJuz = $santri->progress_juz ?? 0;
        $tuntasPersen = min(100, (int) round(($capaianJuz / max(1, $targetJuz)) * 100));

        $sisaHalaman = max(0, ($targetJuz - $capaianJuz) * 20);

        // Setoran Terakhir
        $lastSetoran = $santri->setorans()
            ->with(['surah', 'ustadz'])
            ->where('status', 'approved')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        // Absensi terakhir & mutabaah pekan ini
        $today = Carbon::today();
        $absensiToday = $santri->absensis()
            ->whereDate('tanggal', $today)
            ->latest('id')
            ->first();

        $absensi30 = $santri->absensis()
            ->where('tanggal', '>=', now()->subDays(30))
            ->get();
        $totalSesi = $absensi30->count();
        $hadirSesi = $absensi30->where('status', 'hadir')->count();
        $kehadiranPct = $totalSesi > 0 ? (int) round(($hadirSesi / $totalSesi) * 100) : 100;

        // Rata-rata nilai
        $setorans = $santri->setorans()
            ->where('status', 'approved')
            ->get();
        $avgScore = $this->averageScore($setorans);

        // Murajaah harian estimasi halaman
        $murajaahCount = $setorans->where('jenis', 'murajaah')->count();
        $murajaahPerDay = $murajaahCount > 0 ? min(10, max(2, (int) round($murajaahCount / 3))) : 4;

        // Agenda Tasmi' / Simaan
        $agendaTasmi = SimaanSchedule::where('santri_id', $santri->id)
            ->where('status', 'dijadwalkan')
            ->orderBy('tanggal')
            ->first();

        // Pesan Kasih Orang Tua
        $pesanTerbaru = null;
        if ($wali) {
            $pesanTerbaru = WaliMessage::where('wali_id', $wali->id)
                ->where('santri_id', $santri->id)
                ->latest()
                ->first();
        }

        // WhatsApp link untuk musyrif
        $musyrif = $santri->kelompok?->ustadz;
        $waLink = null;
        if ($musyrif && $musyrif->telepon) {
            $phone = preg_replace('/[^0-9]/', '', $musyrif->telepon);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $text = rawurlencode("Assalamu'alaikum Ust. {$musyrif->nama}, saya wali dari ananda {$santri->nama}. Mohon izin bertanya terkait mutaba'ah tahfidz ananda.");
            $waLink = "https://wa.me/{$phone}?text={$text}";
        }

        return [
            'targetJuz' => $targetJuz,
            'capaianJuz' => $capaianJuz,
            'tuntasPersen' => $tuntasPersen,
            'sisaHalaman' => $sisaHalaman,
            'lastSetoran' => $lastSetoran,
            'absensiToday' => $absensiToday,
            'kehadiranPct' => $kehadiranPct,
            'hadirSesi' => $hadirSesi,
            'totalSesi' => $totalSesi,
            'avgScore' => $avgScore,
            'murajaahPerDay' => $murajaahPerDay,
            'agendaTasmi' => $agendaTasmi,
            'pesanTerbaru' => $pesanTerbaru,
            'waLink' => $waLink,
            'juzStatus' => $juzData['statusMap'],
            'juzCounts' => $juzData['counts'],
            'juzDetails' => $juzData['details'],
        ];
    }

    private function averageScore(Collection $setorans): float
    {
        $scores = $setorans->map(function ($s) {
            if ($s->skor) {
                return (int) $s->skor;
            }

            return match ($s->nilai) {
                'mumtaz' => 95,
                'jayyid_jiddan' => 85,
                'jayyid' => 75,
                'maqbul' => 60,
                default => null,
            };
        })->filter(fn ($v) => $v !== null);

        return $scores->isEmpty() ? 92.5 : round($scores->avg(), 1);
    }

    private function buildJuzMap(Santri $santri): array
    {
        $setorans = $santri->setorans()
            ->where('status', 'approved')
            ->get();

        $maxZiyadah = (int) ($setorans->where('jenis', 'ziyadah')->max('juz') ?? 0);

        $surahs = Surah::orderBy('nomor')->get(['id', 'nomor', 'nama_latin', 'nama_arab', 'jumlah_ayat', 'juz_mulai', 'juz_selesai']);
        $starts = JuzStart::with('surah:id,nomor')->orderBy('juz')->get()
            ->filter(fn ($s) => $s->surah !== null)
            ->values();

        $statusMap = [];
        $details = [];

        for ($juz = 1; $juz <= 30; $juz++) {
            $juzSetorans = $setorans->where('juz', $juz);
            $hasMutqin = $juzSetorans->contains(fn ($s) => $s->is_cover_penuh || $s->nilai === 'mumtaz');

            if ($juz < $maxZiyadah) {
                $status = 'mutqin';
            } elseif ($juz === $maxZiyadah && $maxZiyadah > 0) {
                $status = $hasMutqin ? 'mutqin' : 'ziyadah';
            } elseif ($juz === $maxZiyadah + 1 && $juzSetorans->isNotEmpty()) {
                $status = 'proses';
            } else {
                $status = $juzSetorans->isNotEmpty() ? 'proses' : 'belum';
            }

            $statusMap[$juz] = $status;

            // Cari surah yang masuk di juz ini
            $juzSurahs = $surahs->filter(function ($surah) use ($juz) {
                return $juz >= $surah->juz_mulai && $juz <= $surah->juz_selesai;
            })->values();

            $details[$juz] = [
                'juz' => $juz,
                'status' => $status,
                'surahs' => $juzSurahs->map(fn ($surah) => [
                    'nomor' => $surah->nomor,
                    'nama_latin' => $surah->nama_latin,
                    'nama_arab' => $surah->nama_arab,
                    'jumlah_ayat' => $surah->jumlah_ayat,
                ])->all(),
                'setoran_count' => $juzSetorans->count(),
                'last_setoran' => $juzSetorans->sortByDesc('tanggal')->first(),
            ];
        }

        $counts = [
            'mutqin' => count(array_filter($statusMap, fn ($s) => $s === 'mutqin')),
            'ziyadah' => count(array_filter($statusMap, fn ($s) => $s === 'ziyadah')),
            'proses' => count(array_filter($statusMap, fn ($s) => $s === 'proses')),
            'belum' => count(array_filter($statusMap, fn ($s) => $s === 'belum')),
        ];

        return [
            'statusMap' => $statusMap,
            'counts' => $counts,
            'details' => $details,
        ];
    }
}
