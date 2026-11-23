<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\User;
use App\Models\Ustadz;
use App\Models\Wali;
use App\Support\JuzCoverage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JuzCoverageTest extends TestCase
{
    use RefreshDatabase;

    private User $waliUser;
    private Wali $wali;
    private User $ustadzUser;
    private Ustadz $ustadz;
    private Santri $santri;
    private Angkatan $angkatan;
    private Kelompok $kelompok;
    private JuzCoverage $juzCoverage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->juzCoverage = app(JuzCoverage::class);

        $this->ustadzUser = User::where('username', 'ustadz')->first();
        $this->ustadz = Ustadz::first();
        $this->angkatan = Angkatan::first();
        $this->kelompok = Kelompok::first();

        $this->waliUser = User::create([
            'name' => 'Bapak Test',
            'username' => 'wali-coverage',
            'password' => 'password',
            'role' => 'wali',
        ]);
        $this->wali = Wali::create([
            'user_id' => $this->waliUser->id,
            'nama' => 'Bapak Test',
            'telepon' => '081234567890',
            'alamat' => 'Ponorogo',
        ]);

        $this->santri = Santri::create([
            'nis' => 'SN-TEST',
            'nama' => 'Santri Test',
            'angkatan_id' => $this->angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'wali_id' => $this->wali->id,
            'status_aktif' => 'aktif',
        ]);
    }

    public function test_full_cover_juz_returns_mutqin(): void
    {
        // Juz 1 spans Surah 1 (Al-Fatihah, 7 ayat) and Surah 2 (Al-Baqarah, up to ayat 141)
        // Juz 2 starts at Surah 2, ayat 142
        $surah1 = Surah::find(1);
        $surah2 = Surah::find(2);
        $this->assertNotNull($surah1);
        $this->assertNotNull($surah2);

        // Full cover of Juz 1: Surah 1 (1-7) + Surah 2 (1-141)
        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah1->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah1->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah2->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 141,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('mutqin', $result[1]['status']);
        $this->assertEquals(100, $result[1]['percent']);
        $expectedTotal = $surah1->jumlah_ayat + 141;
        $this->assertEquals($expectedTotal, $result[1]['covered_ayat']);
        $this->assertArrayHasKey('surahs', $result[1]);
        $this->assertNotEmpty($result[1]['surahs']);
        foreach ($result[1]['surahs'] as $surahDetail) {
            $this->assertArrayHasKey('surah_jumlah_ayat', $surahDetail);
            $this->assertIsInt($surahDetail['surah_jumlah_ayat']);
            $this->assertGreaterThan(0, $surahDetail['surah_jumlah_ayat']);
        }
    }

    public function test_partial_cover_juz_returns_parsial_with_correct_percent(): void
    {
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();
        $this->assertNotNull($surah);

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => (int) round($surah->jumlah_ayat / 2),
            'nilai' => 'jayyid',
            'skor' => 80,
            'status' => 'approved',
        ]);

        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('parsial', $result[1]['status']);
        $this->assertGreaterThan(0, $result[1]['percent']);
        $this->assertLessThan(100, $result[1]['percent']);
        $this->assertEquals((int) round($surah->jumlah_ayat / 2), $result[1]['covered_ayat']);
    }

    public function test_juz_spanning_two_surahs_juz_2_correct_required_ranges(): void
    {
        $juz2Start = \App\Models\JuzStart::where('juz', 2)->first();
        $this->assertNotNull($juz2Start, 'Juz 2 start required');

        $surah2 = Surah::find($juz2Start->surah_id);
        $this->assertNotNull($surah2);

        $nextStart = \App\Models\JuzStart::where('juz', 3)->first();
        $endAyat = $nextStart ? $nextStart->ayat - 1 : $surah2->jumlah_ayat;

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 2,
            'surah_id' => $surah2->id,
            'ayat_awal' => $juz2Start->ayat,
            'ayat_akhir' => $endAyat,
            'nilai' => 'mumtaz',
            'skor' => 95,
            'status' => 'approved',
        ]);

        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('mutqin', $result[2]['status']);
        $this->assertEquals(100, $result[2]['percent']);
        $requiredCount = $endAyat - $juz2Start->ayat + 1;
        $this->assertEquals($requiredCount, $result[2]['covered_ayat']);
    }

    public function test_empty_juz_returns_belum(): void
    {
        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('belum', $result[1]['status']);
        $this->assertEquals(0, $result[1]['percent']);
        $this->assertEquals(0, $result[1]['covered_ayat']);
    }

    public function test_pending_setoran_ignored(): void
    {
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'pending',
        ]);

        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('belum', $result[1]['status']);
        $this->assertEquals(0, $result[1]['percent']);
    }

    public function test_murajaah_setoran_ignored(): void
    {
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'murajaah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        $result = $this->juzCoverage->forSantri($this->santri);

        $this->assertEquals('belum', $result[1]['status']);
        $this->assertEquals(0, $result[1]['percent']);
    }

    public function test_wali_can_see_own_santri_peta_juz(): void
    {
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();
        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.peta-juz', ['santri' => $this->santri->id]))
            ->assertOk();

        $response->assertSee('Peta Hafalan 30 Juz');
        $response->assertSee('Mutqin');
    }

    public function test_wali_cannot_see_other_santri_peta_juz(): void
    {
        $otherWaliUser = User::create([
            'name' => 'Other Wali',
            'username' => 'wali-other-2',
            'password' => 'password',
            'role' => 'wali',
        ]);
        $otherWali = Wali::create([
            'user_id' => $otherWaliUser->id,
            'nama' => 'Other Wali',
            'telepon' => '0899999999',
            'alamat' => 'Test',
        ]);
        $otherSantri = Santri::create([
            'nis' => 'SN-OTHER',
            'nama' => 'Other Santri',
            'angkatan_id' => $this->angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'wali_id' => $otherWali->id,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.peta-juz', ['santri' => $otherSantri->id]))
            ->assertForbidden();
    }

    public function test_admin_can_access_santri_show_with_peta_juz(): void
    {
        $admin = User::where('username', 'admin')->first();
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.santris.show', $this->santri))
            ->assertOk();

        $response->assertSee('Peta Hafalan 30 Juz');
        $response->assertSee('Mutqin');
    }

    public function test_ustadz_can_access_rapor_with_peta_juz(): void
    {
        $surah = Surah::where('juz_mulai', 1)->where('juz_selesai', 1)->first();

        Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => $surah->jumlah_ayat,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.rapor.index', ['santri_id' => $this->santri->id]))
            ->assertOk();

        $response->assertSee('Peta Hafalan 30 Juz');
        $response->assertSee('Mutqin');
    }

    public function test_kepala_can_access_peta_hafalan_page(): void
    {
        $kepalaUser = User::where('username', 'kepala')->first();

        $response = $this->actingAs($kepalaUser)
            ->get(route('kepala.peta-hafalan'))
            ->assertOk();

        $response->assertSee('Peta Hafalan 30 Juz');
        $response->assertSee('Filter');
    }
}