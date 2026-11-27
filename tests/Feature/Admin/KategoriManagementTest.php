<?php

namespace Tests\Feature\Admin;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_tamu_diarahkan_ke_login_dan_pengawas_ditolak(): void
    {
        $kategori = Kategori::factory()->create();
        $pengawas = User::factory()->pengawas()->create();

        $this->get(route('admin.kategori.index'))->assertRedirect(route('login'));
        $this->get(route('admin.kategori.create'))->assertRedirect(route('login'));
        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Dokumen'])->assertRedirect(route('login'));
        $this->get(route('admin.kategori.edit', $kategori))->assertRedirect(route('login'));
        $this->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Baru'])->assertRedirect(route('login'));
        $this->delete(route('admin.kategori.destroy', $kategori))->assertRedirect(route('login'));

        $this->actingAs($pengawas)->get(route('admin.kategori.index'))->assertForbidden();
        $this->actingAs($pengawas)->get(route('admin.kategori.create'))->assertForbidden();
        $this->actingAs($pengawas)->post(route('admin.kategori.store'), ['nama_kategori' => 'Dokumen'])->assertForbidden();
        $this->actingAs($pengawas)->get(route('admin.kategori.edit', $kategori))->assertForbidden();
        $this->actingAs($pengawas)->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Baru'])->assertForbidden();
        $this->actingAs($pengawas)->delete(route('admin.kategori.destroy', $kategori))->assertForbidden();
    }

    public function test_admin_dapat_melihat_dan_mencari_kategori(): void
    {
        $admin = User::factory()->admin()->create();
        Kategori::factory()->create(['nama_kategori' => 'Elektronik']);
        Kategori::factory()->create(['nama_kategori' => 'Dokumen']);

        $this->actingAs($admin)
            ->get(route('admin.kategori.index', ['q' => 'Elek']))
            ->assertOk()
            ->assertSee('Elektronik')
            ->assertDontSee('Dokumen');
    }

    public function test_admin_dapat_menambah_kategori_dan_validasi_duplikat(): void
    {
        $admin = User::factory()->admin()->create();
        Kategori::factory()->create(['nama_kategori' => 'Kunci']);

        $this->actingAs($admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => '  Elektronik  '])
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('status', 'Kategori berhasil ditambahkan.');

        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Elektronik']);

        $this->actingAs($admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => 'Kunci'])
            ->assertSessionHasErrors('nama_kategori');

        $this->actingAs($admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => ''])
            ->assertSessionHasErrors('nama_kategori');
    }

    public function test_admin_dapat_mengedit_kategori_dan_menjaga_keunikan(): void
    {
        $admin = User::factory()->admin()->create();
        $kategoriA = Kategori::factory()->create(['nama_kategori' => 'Elektronik']);
        $kategoriB = Kategori::factory()->create(['nama_kategori' => 'Dokumen']);

        // Menyimpan nama milik kategori itu sendiri tetap diizinkan.
        $this->actingAs($admin)
            ->put(route('admin.kategori.update', $kategoriA), ['nama_kategori' => 'Elektronik'])
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('status', 'Kategori berhasil diperbarui.');

        // Menggunakan nama kategori lain harus ditolak.
        $this->actingAs($admin)
            ->put(route('admin.kategori.update', $kategoriA), ['nama_kategori' => $kategoriB->nama_kategori])
            ->assertSessionHasErrors('nama_kategori');

        $this->actingAs($admin)
            ->put(route('admin.kategori.update', $kategoriA), ['nama_kategori' => 'Aksesoris'])
            ->assertRedirect(route('admin.kategori.index'));

        $this->assertDatabaseHas('kategori', [
            'id' => $kategoriA->id,
            'nama_kategori' => 'Aksesoris',
        ]);
    }

    public function test_admin_dapat_menghapus_kategori_kosong_dan_ditolak_jika_masih_dipakai(): void
    {
        $admin = User::factory()->admin()->create();
        $kategoriKosong = Kategori::factory()->create(['nama_kategori' => 'Kosong']);
        $kategoriTerpakai = Kategori::factory()->create(['nama_kategori' => 'Terpakai']);
        Barang::factory()->create(['kategori_id' => $kategoriTerpakai->id]);

        $this->actingAs($admin)
            ->delete(route('admin.kategori.destroy', $kategoriKosong))
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('status', 'Kategori berhasil dihapus.');

        $this->assertDatabaseMissing('kategori', ['id' => $kategoriKosong->id]);

        $this->actingAs($admin)
            ->delete(route('admin.kategori.destroy', $kategoriTerpakai))
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('kategori', ['id' => $kategoriTerpakai->id]);
    }
}
