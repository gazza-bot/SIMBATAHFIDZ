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
use App\Models\WaliMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $waliUser;
    private Wali $wali;
    private User $waliUser2;
    private Wali $wali2;
    private User $ustadzUser;
    private Ustadz $ustadz;
    private Santri $santri1;
    private Santri $santri2;
    private Santri $santriOther;
    private Surah $surah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->waliUser = User::create([
            'name' => 'Bapak Fauzi',
            'username' => 'wali-test',
            'password' => 'password',
            'role' => 'wali',
        ]);
        $this->wali = Wali::create([
            'user_id' => $this->waliUser->id,
            'nama' => 'Bapak Fauzi',
            'telepon' => '081234567890',
            'alamat' => 'Ponorogo',
        ]);

        $this->waliUser2 = User::create([
            'name' => 'Bapak Ridwan',
            'username' => 'wali-other',
            'password' => 'password',
            'role' => 'wali',
        ]);
        $this->wali2 = Wali::create([
            'user_id' => $this->waliUser2->id,
            'nama' => 'Bapak Ridwan',
            'telepon' => '089988776655',
            'alamat' => 'Madiun',
        ]);

        $this->ustadzUser = User::create([
            'name' => 'Ust. Abdullah',
            'username' => 'ustadz-test',
            'password' => 'password',
            'role' => 'ustadz',
        ]);
        $this->ustadz = Ustadz::create([
            'user_id' => $this->ustadzUser->id,
            'nip' => 'NIP-12345',
            'nama' => 'Ust. Abdullah',
            'telepon' => '081298765432',
        ]);

        $angkatan = Angkatan::create([
            'nama' => 'Kelas 8B',
            'tahun_mulai' => 2024,
            'status_aktif' => true,
        ]);

        $kelompok = Kelompok::create([
            'nama' => 'Halaqah Imam Bukhari',
            'ustadz_id' => $this->ustadz->id,
            'angkatan_id' => $angkatan->id,
        ]);

        $this->santri1 = Santri::create([
            'nis' => 'SN-001',
            'nama' => 'Fatih Rayyan',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $kelompok->id,
            'wali_id' => $this->wali->id,
            'status_aktif' => 'aktif',
        ]);

        $this->santri2 = Santri::create([
            'nis' => 'SN-002',
            'nama' => 'Fawwaz Rayyan',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $kelompok->id,
            'wali_id' => $this->wali->id,
            'status_aktif' => 'aktif',
        ]);

        $this->santriOther = Santri::create([
            'nis' => 'SN-999',
            'nama' => 'Salman Al-Farisi',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $kelompok->id,
            'wali_id' => $this->wali2->id,
            'status_aktif' => 'aktif',
        ]);

        $this->surah = Surah::create([
            'nomor' => 19,
            'nama_latin' => 'Maryam',
            'nama_arab' => 'مريم',
            'jumlah_ayat' => 98,
            'juz_mulai' => 16,
            'juz_selesai' => 16,
            'tempat_turun' => 'Mekkah',
        ]);

        Setoran::create([
            'santri_id' => $this->santri1->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 16,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 25,
            'nilai' => 'mumtaz',
            'skor' => 96,
            'catatan' => 'Makhraj huruf fasih, tajwid tertib.',
            'status' => 'approved',
        ]);
    }

    public function test_wali_can_access_mutabaah_dashboard(): void
    {
        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.mutabaah'))
            ->assertOk();

        $response->assertSee('Bapak Fauzi');
        $response->assertSee('Fatih Rayyan');
        $response->assertSee('Halaqah Imam Bukhari');
        $response->assertSee('QS. Maryam: Ayat 1-25');
        $response->assertSee('Peta Hafalan 30 Juz');
    }

    public function test_wali_can_switch_between_children(): void
    {
        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.mutabaah', ['santri' => $this->santri2->id]))
            ->assertOk();

        $response->assertSee('Fawwaz Rayyan');
    }

    public function test_wali_cannot_access_other_wali_child(): void
    {
        $this->actingAs($this->waliUser)
            ->get(route('wali.mutabaah', ['santri' => $this->santriOther->id]))
            ->assertForbidden();
    }

    public function test_wali_can_submit_prayer_message(): void
    {
        $response = $this->actingAs($this->waliUser)
            ->post(route('wali.doa.store', ['santri' => $this->santri1->id]), [
                'pesan' => 'Semoga ananda Fatih istiqomah dan dimudahkan Allah dalam menghafal Al-Quran.',
            ]);

        $response->assertRedirect(route('wali.mutabaah', ['santri' => $this->santri1->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wali_messages', [
            'wali_id' => $this->wali->id,
            'santri_id' => $this->santri1->id,
            'pesan' => 'Semoga ananda Fatih istiqomah dan dimudahkan Allah dalam menghafal Al-Quran.',
        ]);
    }

    public function test_wali_can_access_riwayat_page(): void
    {
        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.riwayat', ['santri' => $this->santri1->id]))
            ->assertOk();

        $response->assertSee('Riwayat Setoran & Presensi');
        $response->assertSee('QS. Maryam: Ayat 1-25');
    }

    public function test_wali_can_access_peta_juz_page(): void
    {
        $response = $this->actingAs($this->waliUser)
            ->get(route('wali.peta-juz', ['santri' => $this->santri1->id]))
            ->assertOk();

        $response->assertSee('Peta Hafalan 30 Juz');
        $response->assertSee('Grid 30 Juz');
    }

    public function test_non_wali_cannot_access_mutabaah(): void
    {
        $this->actingAs($this->ustadzUser)
            ->get(route('wali.mutabaah'))
            ->assertForbidden();
    }
}
