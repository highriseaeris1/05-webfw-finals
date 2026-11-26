<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman login memakai @vite; matikan Vite agar pengujian tidak membutuhkan hasil build.
        $this->withoutVite();
    }

    public function test_halaman_login_dapat_ditampilkan(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk panel staf');
    }

    public function test_halaman_publik_tetap_dapat_diakses_tanpa_login(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_admin_dapat_login_menggunakan_email(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@contoh.id',
            'username' => 'admin.tu',
        ]);

        $response = $this->post('/login', [
            'identifier' => 'admin@contoh.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pengawas_dapat_login_menggunakan_username(): void
    {
        $user = User::factory()->pengawas()->create([
            'email' => 'pengawas@contoh.id',
            'username' => 'pengawas',
        ]);

        $response = $this->post('/login', [
            'identifier' => 'pengawas',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_akun_nonaktif_tidak_dapat_login(): void
    {
        User::factory()->admin()->inactive()->create([
            'email' => 'nonaktif@contoh.id',
            'username' => 'nonaktif',
        ]);

        $response = $this->from('/login')->post('/login', [
            'identifier' => 'nonaktif@contoh.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('auth');
        $this->assertGuest();
    }

    public function test_role_di_luar_admin_dan_pengawas_ditolak(): void
    {
        $user = User::factory()->create([
            'email' => 'pengguna@contoh.id',
            'username' => 'pengguna',
        ]);

        // Simulasikan role lama atau role baru yang belum diizinkan tanpa mengubah enum.
        DB::table('users')->where('id', $user->id)->update(['role' => 'pengguna']);

        $response = $this->from('/login')->post('/login', [
            'identifier' => 'pengguna@contoh.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('auth');
        $this->assertGuest();
    }

    public function test_password_salah_ditolak(): void
    {
        User::factory()->create([
            'email' => 'admin@contoh.id',
            'username' => 'admin.tu',
        ]);

        $response = $this->from('/login')->post('/login', [
            'identifier' => 'admin@contoh.id',
            'password' => 'kata-sandi-salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('auth');
        $this->assertGuest();
    }

    public function test_data_login_wajib_diisi(): void
    {
        $response = $this->from('/login')->post('/login', [
            'identifier' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['identifier', 'password']);
        $this->assertGuest();
    }

    public function test_session_diregenerasi_setelah_login_berhasil(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@contoh.id',
            'username' => 'admin.tu',
        ]);

        $this->get('/login');
        $sessionIdSebelumLogin = session()->getId();

        $this->post('/login', [
            'identifier' => 'admin@contoh.id',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertNotSame($sessionIdSebelumLogin, session()->getId());
    }

    public function test_pengguna_yang_sudah_login_diarahkan_dari_halaman_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect(route('dashboard'));
    }

    public function test_tamu_diarahkan_ke_halaman_login_saat_membuka_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_tombol_keluar_tersedia_di_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Keluar');
    }

    public function test_pengguna_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Anda telah keluar.');

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_tamu_diarahkan_ke_login_saat_mencoba_logout(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
