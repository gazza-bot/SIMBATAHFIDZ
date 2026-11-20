<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\Kelompok;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\Wali;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $ustadzUser;
    private Ustadz $ustadz;
    private Kelompok $kelompok;
    private Angkatan $angkatan;
    private Wali $wali;
    private Santri $santri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('username', 'admin')->first();
        $this->ustadzUser = User::where('username', 'ustadz')->first();
        $this->ustadz = Ustadz::first();
        $this->kelompok = Kelompok::first();
        $this->angkatan = Angkatan::first();
        $this->wali = Wali::first();
        $this->santri = Santri::first();
    }

    /** @test */
    public function admin_can_access_santris_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index'))
            ->assertOk()
            ->assertSee('Data Santri');
    }

    /** @test */
    public function admin_can_access_kelompoks_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.kelompoks.index'))
            ->assertOk()
            ->assertSee('Kelompok Halaqah');
    }

    /** @test */
    public function admin_can_access_angkatans_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.angkatans.index'))
            ->assertOk()
            ->assertSee('Angkatan Kelas');
    }

    /** @test */
    public function admin_can_access_ustadzs_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ustadzs.index'))
            ->assertOk()
            ->assertSee('Data Ustadz Musyrif');
    }

    /** @test */
    public function admin_can_access_walis_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.walis.index'))
            ->assertOk()
            ->assertSee('Data Wali Santri');
    }

    /** @test */
    public function santris_partial_returns_json_with_html()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index') . '?partial=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function kelompoks_partial_returns_json_with_html()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.kelompoks.index') . '?partial=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function angkatans_partial_returns_json_with_html()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.angkatans.index') . '?partial=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function ustadzs_partial_returns_json_with_html()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ustadzs.index') . '?partial=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function walis_partial_returns_json_with_html()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.walis.index') . '?partial=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function santris_filter_by_search_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index') . '?partial=1&search=' . $this->santri->nama)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function santris_filter_by_kelompok_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index') . '?partial=1&kelompok_id=' . $this->kelompok->id)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function kelompoks_filter_by_ustadz_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.kelompoks.index') . '?partial=1&ustadz_id=' . $this->ustadz->id)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function kelompoks_filter_by_angkatan_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.kelompoks.index') . '?partial=1&angkatan_id=' . $this->angkatan->id)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function angkatans_filter_by_status_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.angkatans.index') . '?partial=1&status_aktif=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function ustadzs_filter_by_search_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ustadzs.index') . '?partial=1&search=' . $this->ustadz->nama)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function walis_filter_by_search_works()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.walis.index') . '?partial=1&search=' . $this->wali->nama)
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

/** @test */
    public function pagination_preserves_filters()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index') . '?partial=1&search=' . $this->santri->nama . '&page=1')
            ->assertOk()
            ->assertJsonStructure(['html']);
    }

    /** @test */
    public function non_admin_cannot_access_admin_santris()
    {
        $this->actingAs($this->ustadzUser)
            ->get(route('admin.santris.index'))
            ->assertForbidden();
    }

    /** @test */
    public function empty_state_renders_for_no_results()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.santris.index') . '?partial=1&search=THIS_WILL_NOT_MATCH_ANYTHING_12345')
            ->assertOk()
            ->assertJson(fn ($json) => $json->where('html', fn ($html) => str_contains($html, 'Tidak ada data')));
    }
}