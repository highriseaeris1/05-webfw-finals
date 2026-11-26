<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman panel memakai @vite; matikan Vite agar tes tidak membutuhkan hasil build.
        $this->withoutVite();
    }

    /**
     * Data formulir yang valid agar setiap test hanya mengubah bagian yang diuji.
     *
     * @return array<string, string>
     */
    private function dataAkun(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pengawas Baru',
            'username' => 'pengawas.baru',
            'email' => 'pengawas.baru@contoh.id',
            'password' => 'kata-sandi-kuat',
            'password_confirmation' => 'kata-sandi-kuat',
            'role' => UserRole::Pengawas->value,
        ], $overrides);
    }

    public function test_admin_dapat_membuka_halaman_tambah_pengguna(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Data akun baru')
            ->assertSee('Nama lengkap')
            ->assertSee('Username')
            ->assertSee('Email')
            ->assertSee('Kata sandi')
            ->assertSee('Konfirmasi kata sandi')
            ->assertSee('Admin')
            ->assertSee('Pengawas')
            ->assertSee('Simpan akun');
    }

    public function test_tombol_tambah_pengguna_tersedia_di_daftar_pengguna(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Tambah Pengguna')
            ->assertSee('/admin/users/create', false);
    }

    public function test_tamu_tidak_dapat_membuka_form_dan_membuat_pengguna(): void
    {
        $this->get(route('admin.users.create'))->assertRedirect(route('login'));

        $this->post(route('admin.users.store'), $this->dataAkun())->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_pengawas_tidak_dapat_membuka_form_dan_membuat_pengguna(): void
    {
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($pengawas)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->post(route('admin.users.store'), $this->dataAkun())
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_admin_dapat_menambahkan_pengguna_aktif(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), $this->dataAkun());

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', 'Pengguna berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'name' => 'Pengawas Baru',
            'username' => 'pengawas.baru',
            'email' => 'pengawas.baru@contoh.id',
            'role' => UserRole::Pengawas->value,
            'is_active' => true,
        ]);

        $user = User::where('username', 'pengawas.baru')->firstOrFail();

        $this->assertNotSame('kata-sandi-kuat', $user->password);
        $this->assertTrue(Hash::check('kata-sandi-kuat', $user->password));

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Pengguna berhasil ditambahkan.');
    }

    public function test_admin_dapat_menambahkan_akun_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $this->dataAkun([
                'name' => 'Admin Baru',
                'username' => 'admin.baru',
                'email' => 'admin.baru@contoh.id',
                'role' => UserRole::Admin->value,
            ]))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'admin.baru',
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);
    }

    public function test_semua_kolom_wajib_diisi(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), []);

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors(['name', 'username', 'email', 'password', 'role']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_username_dan_email_harus_unik(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory()->pengawas()->create([
            'username' => 'sudah.ada',
            'email' => 'sudah.ada@contoh.id',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->dataAkun([
                'username' => 'sudah.ada',
                'email' => 'sudah.ada@contoh.id',
            ]));

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors([
            'username' => 'Username sudah digunakan akun lain.',
            'email' => 'Email sudah digunakan akun lain.',
        ]);

        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_kata_sandi_minimal_delapan_karakter(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->dataAkun([
                'password' => '1234567',
                'password_confirmation' => '1234567',
            ]));

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors([
            'password' => 'Kata sandi minimal 8 karakter.',
        ]);

        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_konfirmasi_kata_sandi_harus_cocok(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->dataAkun([
                'password_confirmation' => 'kata-sandi-berbeda',
            ]));

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors([
            'password' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_peran_di_luar_admin_dan_pengawas_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->dataAkun([
                'role' => 'pemilik',
            ]));

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors([
            'role' => 'Peran yang dipilih tidak valid.',
        ]);

        $this->assertDatabaseMissing('users', ['username' => 'pengawas.baru']);
    }

    public function test_akun_baru_dapat_login_menggunakan_username(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $this->dataAkun())
            ->assertRedirect(route('admin.users.index'));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post('/login', [
            'identifier' => 'pengawas.baru',
            'password' => 'kata-sandi-kuat',
        ])->assertRedirect(route('pengawas.dashboard'));

        $this->assertAuthenticated();
    }
}
