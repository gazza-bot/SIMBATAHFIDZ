<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\JuzStart;
use App\Models\KepalaTahfidz;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPdfExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $ustadzUser;

    private Ustadz $ustadz;

    private Angkatan $angkatan;

    private Surah $surah;

    private Santri $santriAktif;

    private Santri $santriLulus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Satu',
            'username' => 'admin-satu',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->ustadzUser = User::create([
            'name' => 'Ustadz Satu',
            'username' => 'ustadz-satu',
            'password' => 'password',
            'role' => 'ustadz',
        ]);

        $this->ustadz = Ustadz::create([
            'user_id' => $this->ustadzUser->id,
            'nip' => 'NIP-001',
            'nama' => 'Ustadz Satu',
        ]);

        $kepalaUser = User::create([
            'name' => 'Kepala Satu',
            'username' => 'kepala-satu',
            'password' => 'password',
            'role' => 'kepala_tahfidz',
        ]);

        KepalaTahfidz::create([
            'user_id' => $kepalaUser->id,
            'nama' => 'Ust. Kepala Tahfidz',
        ]);

        $this->angkatan = Angkatan::create([
            'nama' => '2025/2026',
            'tahun_mulai' => 2025,
            'status_aktif' => true,
        ]);

        $this->santriAktif = Santri::create([
            'nis' => 'S-001',
            'nama' => 'Ahmad Santri',
            'angkatan_id' => $this->angkatan->id,
            'status_aktif' => 'aktif',
        ]);

        $this->santriLulus = Santri::create([
            'nis' => 'S-002',
            'nama' => 'Bilal Lulus',
            'angkatan_id' => $this->angkatan->id,
            'status_aktif' => 'lulus',
        ]);

        $this->surah = Surah::create([
            'nomor' => 1,
            'nama_latin' => 'Al-Fatihah',
            'nama_arab' => 'الفاتحة',
            'jumlah_ayat' => 7,
            'juz_mulai' => 1,
            'juz_selesai' => 1,
            'tempat_turun' => 'Mekkah',
        ]);

        JuzStart::create([
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat' => 1,
        ]);

        Setoran::create([
            'santri_id' => $this->santriAktif->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'mumtaz',
            'status' => 'approved',
            'is_draft' => false,
            'validated_by' => $this->admin->id,
            'validated_at' => now(),
        ]);
    }

    public function test_admin_can_download_rapor_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('santris.rapor.pdf', $this->santriAktif));

        $response->assertOk();
        $response->assertDownload('rapor_S-001_lengkap.pdf');
    }

    public function test_rapor_pdf_renders_without_setoran(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('santris.rapor.pdf', $this->santriLulus));

        $response->assertOk();
        $response->assertDownload('rapor_S-002_lengkap.pdf');
    }

    public function test_admin_can_download_sertifikat_for_lulus_santri(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('santris.sertifikat.pdf', $this->santriLulus));

        $response->assertOk();
        $response->assertDownload('sertifikat_khatam_S-002.pdf');
    }

    public function test_sertifikat_is_blocked_for_non_lulus_santri(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('santris.sertifikat.pdf', $this->santriAktif));

        $response->assertRedirect(route('admin.santris.show', $this->santriAktif));
        $response->assertSessionHas('error');
    }

    public function test_non_admin_cannot_download_pdf_exports(): void
    {
        $this->actingAs($this->ustadzUser)
            ->get(route('santris.rapor.pdf', $this->santriAktif))
            ->assertForbidden();

        $this->actingAs($this->ustadzUser)
            ->get(route('santris.sertifikat.pdf', $this->santriLulus))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_for_pdf_exports(): void
    {
        $this->get(route('santris.rapor.pdf', $this->santriAktif))
            ->assertRedirect(route('login'));
    }
}
