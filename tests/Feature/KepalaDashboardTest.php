<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\KepalaTahfidz;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $kepala;

    private User $ustadzUser;

    private Ustadz $ustadz;

    private Surah $surah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kepala = User::create([
            'name' => 'Kepala Tahfidz',
            'username' => 'kepala-test',
            'password' => 'password',
            'role' => 'kepala_tahfidz',
        ]);
        KepalaTahfidz::create([
            'user_id' => $this->kepala->id,
            'nama' => 'Kepala Tahfidz',
        ]);

        $this->ustadzUser = User::create([
            'name' => 'Ustadz Test',
            'username' => 'ustadz-test',
            'password' => 'password',
            'role' => 'ustadz',
        ]);
        $this->ustadz = Ustadz::create([
            'user_id' => $this->ustadzUser->id,
            'nip' => 'NIP-TEST',
            'nama' => 'Ustadz Test',
        ]);

        $this->surah = Surah::create([
            'nomor' => 1,
            'nama_latin' => 'Al-Fatihah',
            'nama_arab' => 'الفاتحة',
            'jumlah_ayat' => 7,
            'juz_mulai' => 1,
            'juz_selesai' => 30,
            'tempat_turun' => 'Mekkah',
        ]);
    }

    public function test_kepala_can_access_dashboard(): void
    {
        $this->actingAs($this->kepala)
            ->get('/kepala/dashboard')
            ->assertOk();
    }

    public function test_non_kepala_cannot_access_dashboard(): void
    {
        $this->actingAs($this->ustadzUser)
            ->get('/kepala/dashboard')
            ->assertForbidden();
    }

    public function test_juz_distribution_returns_html_payload(): void
    {
        $this->actingAs($this->kepala)
            ->getJson(route('kepala.dashboard.juz-distribution'))
            ->assertOk()
            ->assertJsonStructure(['selected', 'html'])
            ->assertJsonPath('selected', null);
    }

    public function test_juz_distribution_filters_by_angkatan(): void
    {
        $angkatanA = $this->makeAngkatan('2024/2025', 2024);
        $angkatanB = $this->makeAngkatan('2023/2024', 2023);

        $kelompokA = $this->makeKelompok($angkatanA, 'Halaqah A');
        $kelompokB = $this->makeKelompok($angkatanB, 'Halaqah B');

        $santriA1 = $this->makeSantri($kelompokA, 'A-001');
        $santriA2 = $this->makeSantri($kelompokA, 'A-002');
        $santriB1 = $this->makeSantri($kelompokB, 'B-001');

        $this->setProgress($santriA1, 3);
        $this->setProgress($santriA2, 3);
        $this->setProgress($santriB1, 7);

        $response = $this->actingAs($this->kepala)
            ->getJson(route('kepala.dashboard.juz-distribution', ['angkatan' => $angkatanA->id]))
            ->assertOk()
            ->assertJsonPath('selected', $angkatanA->id);

        $html = $response->json('html');

        $this->assertStringContainsString('Juz 1-5: 2 santri', $html);
        $this->assertStringContainsString('Juz 6-10: 0 santri', $html);
    }

    public function test_juz_distribution_falls_back_for_invalid_angkatan(): void
    {
        $angkatan = $this->makeAngkatan('2024/2025', 2024);
        $kelompok = $this->makeKelompok($angkatan, 'Halaqah A');

        $this->setProgress($this->makeSantri($kelompok, 'A-001'), 3);
        $this->setProgress($this->makeSantri($kelompok, 'A-002'), 7);

        $response = $this->actingAs($this->kepala)
            ->getJson(route('kepala.dashboard.juz-distribution', ['angkatan' => 999999]))
            ->assertOk()
            ->assertJsonPath('selected', null);

        $html = $response->json('html');

        $this->assertStringContainsString('Juz 1-5: 1 santri', $html);
        $this->assertStringContainsString('Juz 6-10: 1 santri', $html);
    }

    private function makeAngkatan(string $nama, int $tahun): Angkatan
    {
        return Angkatan::create([
            'nama' => $nama,
            'tahun_mulai' => $tahun,
            'status_aktif' => true,
        ]);
    }

    private function makeKelompok(Angkatan $angkatan, string $nama): Kelompok
    {
        return Kelompok::create([
            'nama' => $nama,
            'ustadz_id' => $this->ustadz->id,
            'angkatan_id' => $angkatan->id,
        ]);
    }

    private function makeSantri(Kelompok $kelompok, string $nis): Santri
    {
        return Santri::create([
            'nis' => $nis,
            'nama' => 'Santri ' . $nis,
            'angkatan_id' => $kelompok->angkatan_id,
            'kelompok_id' => $kelompok->id,
            'status_aktif' => 'aktif',
        ]);
    }

    private function setProgress(Santri $santri, int $juz): void
    {
        Setoran::create([
            'santri_id' => $santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => $juz,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'jayyid',
            'status' => 'approved',
        ]);
    }
}
