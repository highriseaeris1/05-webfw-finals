<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Dashboard dan halaman 403 memakai @vite; matikan Vite agar tes tidak membutuhkan hasil build.
        $this->withoutVite();
    }

    public function test_admin_dapat_membuka_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard admin');
    }

    public function test_pengawas_dapat_membuka_dashboard_pengawas(): void
    {
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($pengawas)
            ->get(route('pengawas.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard pengawas');
    }

    public function test_admin_tidak_dapat_membuka_dashboard_pengawas(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('pengawas.dashboard'))
            ->assertForbidden();
    }

    public function test_pengawas_tidak_dapat_membuka_dashboard_admin(): void
    {
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($pengawas)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_pengunjung_diarahkan_ke_login_saat_membuka_halaman_staf(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('pengawas.dashboard'))->assertRedirect(route('login'));
        $this->get(route('password.edit'))->assertRedirect(route('login'));
    }

    public function test_halaman_utama_tetap_dapat_diakses_tanpa_login(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_halaman_403_ditampilkan_saat_akses_ditolak(): void
    {
        // Pastikan yang diuji adalah halaman error kustom, bukan halaman debug Laravel.
        config(['app.debug' => false]);

        $admin = User::factory()->admin()->create();
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($pengawas)
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('Akses ditolak')
            ->assertSee('Kembali ke panel');

        $this->actingAs($admin)
            ->get(route('pengawas.dashboard'))
            ->assertForbidden()
            ->assertSee('Akses ditolak')
            ->assertSee('Kembali ke panel');
    }

    public function test_menu_panel_sesuai_peran_yang_login(): void
    {
        $admin = User::factory()->admin()->create();
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.users.index'), false)
            ->assertDontSee('Pemusnahan');

        $this->actingAs($pengawas)
            ->get(route('pengawas.dashboard'))
            ->assertOk()
            ->assertSee('Pemusnahan')
            ->assertDontSee(route('admin.users.index'), false);
    }
}
