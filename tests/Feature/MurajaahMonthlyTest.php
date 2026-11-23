<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\User;
use App\Models\Ustadz;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MurajaahMonthlyTest extends TestCase
{
    use RefreshDatabase;

    private User $ustadzUser;
    private Ustadz $ustadz;
    private Santri $santriSudah;
    private Santri $santriBelum;
    private Angkatan $angkatan;
    private Kelompok $kelompok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->ustadzUser = User::where('username', 'ustadz')->first();
        $this->ustadz = Ustadz::first();
        $this->angkatan = Angkatan::first();
        $this->kelompok = Kelompok::first();
        $this->surah = Surah::first();

        $this->santriSudah = Santri::create([
            'nis' => 'SN-LAKU',
            'nama' => 'Santri Laku',
            'angkatan_id' => $this->angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'wali_id' => null,
            'status_aktif' => 'aktif',
        ]);

        $this->santriBelum = Santri::create([
            'nis' => 'SN-BELUM',
            'nama' => 'Santri Belum',
            'angkatan_id' => $this->angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'wali_id' => null,
            'status_aktif' => 'aktif',
        ]);

        $this->createMurajaahSetoran($this->santriSudah, '2026-10-15');
    }

    private function createMurajaahSetoran(Santri $santri, string $tanggal): void
    {
        Setoran::create([
            'santri_id' => $santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => $tanggal,
            'jenis' => 'murajaah',
            'juz' => 10,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 20,
            'nilai' => 'jayyid',
            'skor' => 80,
            'status' => 'approved',
        ]);
    }

    public function test_ustadz_see_monthly_list_with_derived_status(): void
    {
        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertOk();

        $response->assertSee('Murajaah Bulanan');
        $response->assertSee('Sudah Murajaah');
        $response->assertSee('Belum Murajaah');
        $response->assertSee('Santri Laku');
        $response->assertSee('Santri Belum');
    }

    public function test_derived_Status_is_sudah_when_have_murajaah_setoran_in_month(): void
    {
        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertOk();

        $response->assertSee('Santri Laku');
        $response->assertSee('Sudah');
    }

    public function test_derived_Status_is_belum_when_no_murajaah_in_month(): void
    {
        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertOk();

        $response->assertSee('Santri Belum');
        $response->assertSee('Belum');
    }

    public function test_month_filter_shows_selected_month(): void
    {
        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.murajaahs.index') . '?bulan=' . Carbon::now()->format('Y-m'))
            ->assertOk();

        $response->assertSee('bulan');
    }

    public function test_ustadz_only_sees_their_own_halaqah_santri(): void
    {
        $santriLain = Santri::create([
            'nis' => 'SN-LAIN',
            'nama' => 'Santri Lain',
            'angkatan_id' => $this->angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'wali_id' => null,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertOk();

        $response->assertSee('Santri Laku');
        $response->assertSee('Santri Belum');
        $response->assertSee('Santri Lain');
    }

    public function test_non_ustadz_cannot_access_murajaah_page(): void
    {
        $waliUser = User::where('username', 'wali')->first();

        $response = $this->actingAs($waliUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertForbidden();
    }

    public function test_wali_cannot_access_ustadz_route(): void
    {
        $waliUser = User::where('username', 'wali')->first();

        $response = $this->actingAs($waliUser)
            ->get(route('ustadz.murajaahs.index'))
            ->assertForbidden();
    }
}