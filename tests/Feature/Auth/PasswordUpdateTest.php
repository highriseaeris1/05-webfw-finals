<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman ubah kata sandi memakai @vite; matikan Vite agar tes tidak membutuhkan hasil build.
        $this->withoutVite();
    }

    private function buatPengguna(): User
    {
        return User::factory()->create([
            'email' => 'admin@contoh.id',
            'username' => 'admin.tu',
        ]);
    }

    public function test_halaman_ubah_kata_sandi_dapat_ditampilkan(): void
    {
        $this->actingAs($this->buatPengguna())
            ->get('/kata-sandi')
            ->assertOk()
            ->assertSee('Ubah kata sandi');
    }

    public function test_kata_sandi_dapat_diubah(): void
    {
        $user = $this->buatPengguna();

        $response = $this->actingAs($user)->put('/kata-sandi', [
            'current_password' => 'password',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ]);

        $response->assertRedirect(route('password.edit'));
        $response->assertSessionHas('status', 'Kata sandi berhasil diperbarui.');

        $user->refresh();

        $this->assertNotSame('kata-sandi-baru', $user->password);
        $this->assertTrue(Hash::check('kata-sandi-baru', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
    }

    public function test_kata_sandi_lama_salah_ditolak(): void
    {
        $user = $this->buatPengguna();

        $response = $this->actingAs($user)->from('/kata-sandi')->put('/kata-sandi', [
            'current_password' => 'kata-sandi-salah',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ]);

        $response->assertRedirect('/kata-sandi');
        $response->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_konfirmasi_kata_sandi_tidak_cocok_ditolak(): void
    {
        $user = $this->buatPengguna();

        $response = $this->actingAs($user)->from('/kata-sandi')->put('/kata-sandi', [
            'current_password' => 'password',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-berbeda',
        ]);

        $response->assertRedirect('/kata-sandi');
        $response->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_kata_sandi_baru_minimal_delapan_karakter(): void
    {
        $user = $this->buatPengguna();

        $response = $this->actingAs($user)->from('/kata-sandi')->put('/kata-sandi', [
            'current_password' => 'password',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertRedirect('/kata-sandi');
        $response->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_tamu_diarahkan_ke_login_saat_membuka_halaman_ubah_kata_sandi(): void
    {
        $this->get('/kata-sandi')->assertRedirect(route('login'));
    }

    public function test_tamu_tidak_dapat_mengubah_kata_sandi(): void
    {
        $this->put('/kata-sandi', [
            'current_password' => 'password',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ])->assertRedirect(route('login'));
    }

    public function test_kata_sandi_baru_dapat_digunakan_untuk_login(): void
    {
        $user = $this->buatPengguna();

        $this->actingAs($user)->put('/kata-sandi', [
            'current_password' => 'password',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ])->assertRedirect(route('password.edit'));

        $this->post('/logout')->assertRedirect(route('login'));

        $this->from('/login')->post('/login', [
            'identifier' => 'admin@contoh.id',
            'password' => 'password',
        ])->assertSessionHasErrors('auth');

        $this->assertGuest();

        $this->post('/login', [
            'identifier' => 'admin@contoh.id',
            'password' => 'kata-sandi-baru',
        ])->assertRedirect(route('pengawas.dashboard'));

        $this->assertAuthenticatedAs($user->fresh());
    }
}
