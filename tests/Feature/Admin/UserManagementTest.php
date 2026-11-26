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

    /**
     * Data formulir edit yang valid agar setiap test hanya mengubah bagian yang diuji.
     *
     * @return array<string, string>
     */
    private function dataPerubahan(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pengguna Diperbarui',
            'username' => 'pengguna.diperbarui',
            'email' => 'pengguna.diperbarui@contoh.id',
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

    public function test_admin_dapat_membuka_form_edit_dengan_data_terisi(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->create([
            'name' => 'Nama Lama',
            'username' => 'username.lama',
            'email' => 'lama@contoh.id',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertSee('Edit pengguna')
            ->assertSee('Nama Lama')
            ->assertSee('username.lama')
            ->assertSee('lama@contoh.id')
            ->assertSee('value="pengawas" selected', false)
            ->assertSee('Simpan perubahan');
    }

    public function test_tombol_edit_tersedia_pada_setiap_baris_pengguna(): void
    {
        $admin = User::factory()->admin()->create();
        $pengawas = User::factory()->pengawas()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(route('admin.users.edit', $admin), false)
            ->assertSee(route('admin.users.edit', $pengawas), false);
    }

    public function test_admin_dapat_memperbarui_data_pengguna_lain(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->create([
            'username' => 'username.lama',
            'email' => 'lama@contoh.id',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $target), $this->dataPerubahan([
                'name' => 'Nama Baru',
                'username' => 'nama.baru',
                'email' => 'nama.baru@contoh.id',
                'role' => UserRole::Admin->value,
            ]));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', 'Pengguna berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nama Baru',
            'username' => 'nama.baru',
            'email' => 'nama.baru@contoh.id',
            'role' => UserRole::Admin->value,
        ]);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Pengguna berhasil diperbarui.');
    }

    public function test_username_dan_email_sendiri_tetap_boleh_disimpan(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Lama',
            'username' => 'admin.tu',
            'email' => 'admin@contoh.id',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => 'Admin Baru',
                'username' => 'admin.tu',
                'email' => 'admin@contoh.id',
                'role' => UserRole::Admin->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru',
            'username' => 'admin.tu',
            'email' => 'admin@contoh.id',
            'role' => UserRole::Admin->value,
        ]);
    }

    public function test_username_dan_email_milik_akun_lain_ditolak_saat_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->create([
            'name' => 'Nama Awal',
            'username' => 'akun.target',
            'email' => 'target@contoh.id',
        ]);

        User::factory()->pengawas()->create([
            'username' => 'sudah.ada',
            'email' => 'sudah.ada@contoh.id',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.edit', $target))
            ->put(route('admin.users.update', $target), $this->dataPerubahan([
                'username' => 'sudah.ada',
                'email' => 'sudah.ada@contoh.id',
            ]));

        $response->assertRedirect(route('admin.users.edit', $target));
        $response->assertSessionHasErrors([
            'username' => 'Username sudah digunakan akun lain.',
            'email' => 'Email sudah digunakan akun lain.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nama Awal',
            'username' => 'akun.target',
            'email' => 'target@contoh.id',
        ]);
    }

    public function test_semua_kolom_wajib_diisi_saat_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.edit', $target))
            ->put(route('admin.users.update', $target), []);

        $response->assertRedirect(route('admin.users.edit', $target));
        $response->assertSessionHasErrors(['name', 'username', 'email', 'role']);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'username' => $target->username,
        ]);
    }

    public function test_peran_di_luar_admin_dan_pengawas_ditolak_saat_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.edit', $target))
            ->put(route('admin.users.update', $target), $this->dataPerubahan([
                'role' => 'pemilik',
            ]));

        $response->assertRedirect(route('admin.users.edit', $target));
        $response->assertSessionHasErrors([
            'role' => 'Peran yang dipilih tidak valid.',
        ]);
    }

    public function test_admin_tidak_dapat_mengubah_perannya_sendiri_menjadi_pengawas(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin.tu',
            'email' => 'admin@contoh.id',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), $this->dataPerubahan([
                'name' => 'Admin Tetap',
                'username' => 'admin.tu',
                'email' => 'admin@contoh.id',
                'role' => UserRole::Pengawas->value,
            ]));

        $response->assertRedirect(route('admin.users.edit', $admin));
        $response->assertSessionHasErrors([
            'role' => 'Anda tidak dapat mengubah peran akun Anda sendiri menjadi Pengawas.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => $admin->name,
            'role' => UserRole::Admin->value,
        ]);
    }

    public function test_kata_sandi_dan_status_aktif_tidak_berubah_saat_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengawas()->inactive()->create([
            'username' => 'akun.target',
            'email' => 'target@contoh.id',
        ]);
        $passwordLama = $target->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), $this->dataPerubahan([
                'name' => 'Nama Baru',
                'username' => 'akun.target',
                'email' => 'target@contoh.id',
            ]))
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame($passwordLama, $target->password);
        $this->assertTrue(Hash::check('password', $target->password));
        $this->assertFalse($target->is_active);
    }

    public function test_tamu_tidak_dapat_membuka_form_dan_mengubah_pengguna(): void
    {
        $target = User::factory()->pengawas()->create();

        $this->get(route('admin.users.edit', $target))->assertRedirect(route('login'));

        $this->put(route('admin.users.update', $target), $this->dataPerubahan())
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'username' => $target->username,
        ]);
    }

    public function test_pengawas_tidak_dapat_membuka_form_dan_mengubah_pengguna(): void
    {
        $pengawas = User::factory()->pengawas()->create();
        $target = User::factory()->pengawas()->create();

        $this->actingAs($pengawas)
            ->get(route('admin.users.edit', $target))
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('admin.users.update', $target), $this->dataPerubahan())
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'username' => $target->username,
        ]);
    }
}
