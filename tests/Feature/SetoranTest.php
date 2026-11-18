<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\JuzStart;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetoranTest extends TestCase
{
    use RefreshDatabase;

    private User $ustadzUser;

    private Ustadz $ustadz;

    private Kelompok $kelompok;

    private Santri $santri;

    private Surah $surah;

    protected function setUp(): void
    {
        parent::setUp();

        $angkatan = Angkatan::create([
            'nama' => '2025/2026',
            'tahun_mulai' => 2025,
            'status_aktif' => true,
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

        $this->kelompok = Kelompok::create([
            'nama' => 'Halaqah Satu',
            'ustadz_id' => $this->ustadz->id,
            'angkatan_id' => $angkatan->id,
        ]);

        $this->santri = Santri::create([
            'nis' => 'S-001',
            'nama' => 'Ahmad Santri',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $this->kelompok->id,
            'status_aktif' => 'aktif',
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
    }

    public function test_ustadz_can_store_setoran_and_it_is_auto_approved(): void
    {
        $response = $this->actingAs($this->ustadzUser)->post(route('ustadz.setorans.store'), [
            'santri_id' => $this->santri->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'surah_id' => $this->surah->id,
            'juz_pilih' => 1,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'mumtaz',
            'skor' => 95,
            'tuntas_lanjut' => 1,
            'catatan' => 'Bacaan lancar.',
        ]);

        $setoran = Setoran::firstOrFail();

        $response->assertRedirect(route('ustadz.setorans.sukses', $setoran));

        $this->assertDatabaseHas('setorans', [
            'id' => $setoran->id,
            'santri_id' => $this->santri->id,
            'status' => 'approved',
            'is_draft' => false,
            'validated_by' => $this->ustadzUser->id,
            'juz' => 1,
            'tuntas_lanjut' => true,
        ]);

        $this->assertTrue($setoran->fresh()->is_cover_penuh);
    }

    public function test_ustadz_cannot_store_setoran_for_santri_outside_their_group(): void
    {
        $otherUstadzUser = User::create([
            'name' => 'Ustadz Dua',
            'username' => 'ustadz-dua',
            'password' => 'password',
            'role' => 'ustadz',
        ]);

        $otherUstadz = Ustadz::create([
            'user_id' => $otherUstadzUser->id,
            'nip' => 'NIP-002',
            'nama' => 'Ustadz Dua',
        ]);

        $otherKelompok = Kelompok::create([
            'nama' => 'Halaqah Dua',
            'ustadz_id' => $otherUstadz->id,
            'angkatan_id' => $this->kelompok->angkatan_id,
        ]);

        $otherSantri = Santri::create([
            'nis' => 'S-002',
            'nama' => 'Santri Lain',
            'angkatan_id' => $this->kelompok->angkatan_id,
            'kelompok_id' => $otherKelompok->id,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->ustadzUser)->post(route('ustadz.setorans.store'), [
            'santri_id' => $otherSantri->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'surah_id' => $this->surah->id,
            'juz_pilih' => 1,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'mumtaz',
        ]);

        $response->assertSessionHasErrors('santri_id');
        $this->assertDatabaseCount('setorans', 0);
    }

    public function test_surah_must_match_the_selected_juz(): void
    {
        $otherSurah = Surah::create([
            'nomor' => 2,
            'nama_latin' => 'Al-Baqarah',
            'nama_arab' => 'البقرة',
            'jumlah_ayat' => 286,
            'juz_mulai' => 1,
            'juz_selesai' => 3,
            'tempat_turun' => 'Madinah',
        ]);

        JuzStart::create([
            'juz' => 2,
            'surah_id' => $otherSurah->id,
            'ayat' => 1,
        ]);

        $response = $this->actingAs($this->ustadzUser)->post(route('ustadz.setorans.store'), [
            'santri_id' => $this->santri->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'surah_id' => $otherSurah->id,
            'juz_pilih' => 1,
            'ayat_awal' => 1,
            'ayat_akhir' => 5,
            'nilai' => 'jayyid',
        ]);

        $response->assertSessionHasErrors('surah_id');
        $this->assertDatabaseCount('setorans', 0);
    }

    public function test_ayat_range_cannot_exceed_the_surah_or_juz_portion(): void
    {
        $response = $this->actingAs($this->ustadzUser)->post(route('ustadz.setorans.store'), [
            'santri_id' => $this->santri->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'surah_id' => $this->surah->id,
            'juz_pilih' => 1,
            'ayat_awal' => 1,
            'ayat_akhir' => 8,
            'nilai' => 'jayyid',
        ]);

        $response->assertSessionHasErrors('ayat_akhir');
        $this->assertDatabaseCount('setorans', 0);
    }

    public function test_ustadz_cannot_open_success_page_for_another_groups_setoran(): void
    {
        $otherUstadzUser = User::create([
            'name' => 'Ustadz Tiga',
            'username' => 'ustadz-tiga',
            'password' => 'password',
            'role' => 'ustadz',
        ]);

        $otherUstadz = Ustadz::create([
            'user_id' => $otherUstadzUser->id,
            'nip' => 'NIP-003',
            'nama' => 'Ustadz Tiga',
        ]);

        $otherKelompok = Kelompok::create([
            'nama' => 'Halaqah Tiga',
            'ustadz_id' => $otherUstadz->id,
            'angkatan_id' => $this->kelompok->angkatan_id,
        ]);

        $otherSantri = Santri::create([
            'nis' => 'S-003',
            'nama' => 'Santri Tiga',
            'angkatan_id' => $this->kelompok->angkatan_id,
            'kelompok_id' => $otherKelompok->id,
            'status_aktif' => 'aktif',
        ]);

        $setoran = Setoran::create([
            'santri_id' => $otherSantri->id,
            'ustadz_id' => $otherUstadz->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'jayyid',
            'status' => 'approved',
        ]);

        $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.setorans.sukses', $setoran))
            ->assertForbidden();
    }

    public function test_approved_setoran_can_still_be_edited_and_deleted_by_its_ustadz(): void
    {
        $setoran = Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'jayyid',
            'status' => 'approved',
            'validated_by' => $this->ustadzUser->id,
            'validated_at' => now(),
            'is_draft' => false,
        ]);

        $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.setorans.edit', $setoran))
            ->assertOk();

        $this->actingAs($this->ustadzUser)
            ->delete(route('ustadz.setorans.destroy', $setoran))
            ->assertRedirect(route('ustadz.setorans.index'));

        $this->assertDatabaseMissing('setorans', ['id' => $setoran->id]);
    }

    public function test_index_only_lists_setorans_from_the_ustadz_group(): void
    {
        Setoran::create([
            'santri_id' => $this->santri->id,
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
        ]);

        $otherUstadzUser = User::create([
            'name' => 'Ustadz Empat',
            'username' => 'ustadz-empat',
            'password' => 'password',
            'role' => 'ustadz',
        ]);

        $otherUstadz = Ustadz::create([
            'user_id' => $otherUstadzUser->id,
            'nip' => 'NIP-004',
            'nama' => 'Ustadz Empat',
        ]);

        $otherKelompok = Kelompok::create([
            'nama' => 'Halaqah Empat',
            'ustadz_id' => $otherUstadz->id,
            'angkatan_id' => $this->kelompok->angkatan_id,
        ]);

        $otherSantri = Santri::create([
            'nis' => 'S-004',
            'nama' => 'Santri Empat',
            'angkatan_id' => $this->kelompok->angkatan_id,
            'kelompok_id' => $otherKelompok->id,
            'status_aktif' => 'aktif',
        ]);

        Setoran::create([
            'santri_id' => $otherSantri->id,
            'ustadz_id' => $otherUstadz->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'jayyid',
            'status' => 'approved',
            'is_draft' => false,
        ]);

        $response = $this->actingAs($this->ustadzUser)->get(route('ustadz.setorans.index'));

        $response->assertOk();
        $response->assertSee($this->santri->nama);
        $response->assertDontSee($otherSantri->nama);
    }

    public function test_update_revalidates_and_keeps_the_setoran_approved(): void
    {
        $setoran = Setoran::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'tanggal' => today()->toDateString(),
            'jenis' => 'ziyadah',
            'juz' => 1,
            'surah_id' => $this->surah->id,
            'ayat_awal' => 1,
            'ayat_akhir' => 7,
            'nilai' => 'jayyid',
            'status' => 'approved',
            'is_draft' => false,
        ]);

        $response = $this->actingAs($this->ustadzUser)
            ->put(route('ustadz.setorans.update', $setoran), [
                'santri_id' => $this->santri->id,
                'tanggal' => today()->toDateString(),
                'jenis' => 'murajaah',
                'surah_id' => $this->surah->id,
                'juz_pilih' => 1,
                'ayat_awal' => 1,
                'ayat_akhir' => 3,
                'nilai' => 'jayyid_jiddan',
                'skor' => 80,
                'tuntas_lanjut' => 1,
            ]);

        $response->assertRedirect(route('ustadz.setorans.index'));

        $this->assertDatabaseHas('setorans', [
            'id' => $setoran->id,
            'jenis' => 'murajaah',
            'ayat_akhir' => 3,
            'nilai' => 'jayyid_jiddan',
            'skor' => 80,
            'tuntas_lanjut' => true,
            'status' => 'approved',
            'validated_by' => $this->ustadzUser->id,
        ]);
    }

    public function test_create_page_renders_with_selected_santri_and_without_draft_action(): void
    {
        $response = $this->actingAs($this->ustadzUser)->get(route('ustadz.setorans.create'));

        $response->assertOk();
        $response->assertSee($this->santri->nama);
        $response->assertDontSee('Simpan Draft');
    }

    public function test_removed_validation_route_returns_not_found(): void
    {
        $this->actingAs($this->ustadzUser)
            ->get('/ustadz/setorans/validasi')
            ->assertNotFound();
    }
}
