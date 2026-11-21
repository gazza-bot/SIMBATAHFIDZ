<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\User;
use App\Models\Ustadz;
use App\Models\Wali;
use App\Models\WaliMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UstadzPesanWaliTest extends TestCase
{
    use RefreshDatabase;

    private User $ustadzUser;
    private Ustadz $ustadz;
    private User $otherUstadzUser;
    private Ustadz $otherUstadz;
    private User $waliUser;
    private Wali $wali;
    private Santri $santri;
    private Santri $otherSantri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ustadzUser = User::create([
            'name' => 'Ust. Rayhan',
            'username' => 'ustadz-rayhan',
            'password' => 'password',
            'role' => 'ustadz',
        ]);
        $this->ustadz = Ustadz::create([
            'user_id' => $this->ustadzUser->id,
            'nip' => 'NIP-001',
            'nama' => 'Ust. Rayhan',
        ]);

        $this->otherUstadzUser = User::create([
            'name' => 'Ust. Salman',
            'username' => 'ustadz-salman',
            'password' => 'password',
            'role' => 'ustadz',
        ]);
        $this->otherUstadz = Ustadz::create([
            'user_id' => $this->otherUstadzUser->id,
            'nip' => 'NIP-002',
            'nama' => 'Ust. Salman',
        ]);

        $this->waliUser = User::create([
            'name' => 'Bapak Fauzi',
            'username' => 'wali-fauzi',
            'password' => 'password',
            'role' => 'wali',
        ]);
        $this->wali = Wali::create([
            'user_id' => $this->waliUser->id,
            'nama' => 'Bapak Fauzi',
        ]);

        $angkatan = Angkatan::create([
            'nama' => 'Kelas 8B',
            'tahun_mulai' => 2024,
            'status_aktif' => true,
        ]);

        $kelompok = Kelompok::create([
            'nama' => 'Halaqah Rayhan',
            'ustadz_id' => $this->ustadz->id,
            'angkatan_id' => $angkatan->id,
        ]);

        $otherKelompok = Kelompok::create([
            'nama' => 'Halaqah Salman',
            'ustadz_id' => $this->otherUstadz->id,
            'angkatan_id' => $angkatan->id,
        ]);

        $this->santri = Santri::create([
            'nis' => 'SN-101',
            'nama' => 'Fatih Rayyan',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $kelompok->id,
            'wali_id' => $this->wali->id,
            'status_aktif' => 'aktif',
        ]);

        $this->otherSantri = Santri::create([
            'nis' => 'SN-102',
            'nama' => 'Zaidan Al-Farisi',
            'angkatan_id' => $angkatan->id,
            'kelompok_id' => $otherKelompok->id,
            'wali_id' => $this->wali->id,
            'status_aktif' => 'aktif',
        ]);
    }

    public function test_ustadz_can_see_doa_badge_in_dashboard(): void
    {
        $message = WaliMessage::create([
            'wali_id' => $this->wali->id,
            'santri_id' => $this->santri->id,
            'pesan' => 'Semoga dimudahkan murajaah juz 15 hari ini.',
            'dibacakan' => false,
        ]);

        $response = $this->actingAs($this->ustadzUser)
            ->get(route('ustadz.dashboard'))
            ->assertOk();

        $response->assertSee('Doa Baru');
        $response->assertSee('Fatih Rayyan');
    }

    public function test_ustadz_can_mark_doa_as_dibacakan(): void
    {
        $message = WaliMessage::create([
            'wali_id' => $this->wali->id,
            'santri_id' => $this->santri->id,
            'pesan' => 'Semoga ananda istiqomah dalam kebaikan.',
            'dibacakan' => false,
        ]);

        $response = $this->actingAs($this->ustadzUser)
            ->post(route('ustadz.pesan-wali.bacakan', $message->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wali_messages', [
            'id' => $message->id,
            'dibacakan' => true,
        ]);

        $this->assertNotNull($message->fresh()->dibacakan_at);
    }

    public function test_ustadz_cannot_mark_doa_for_santri_in_another_group(): void
    {
        $message = WaliMessage::create([
            'wali_id' => $this->wali->id,
            'santri_id' => $this->otherSantri->id,
            'pesan' => 'Semoga sukses selalu.',
            'dibacakan' => false,
        ]);

        $this->actingAs($this->ustadzUser)
            ->post(route('ustadz.pesan-wali.bacakan', $message->id))
            ->assertForbidden();
    }
}
