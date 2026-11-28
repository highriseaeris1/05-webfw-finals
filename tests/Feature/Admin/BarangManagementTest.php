<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusBarang;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_tamu_diarahkan_ke_login_dan_pengawas_ditolak(): void
    {
        $barang = Barang::factory()->create();
        $pengawas = User::factory()->pengawas()->create();

        $this->get(route('admin.barang.index'))->assertRedirect(route('login'));
        $this->get(route('admin.barang.create'))->assertRedirect(route('login'));
        $this->post(route('admin.barang.store'), [])->assertRedirect(route('login'));
        $this->get(route('admin.barang.edit', $barang))->assertRedirect(route('login'));
        $this->put(route('admin.barang.update', $barang), [])->assertRedirect(route('login'));
        $this->delete(route('admin.barang.destroy', $barang))->assertRedirect(route('login'));

        $this->actingAs($pengawas)->get(route('admin.barang.index'))->assertForbidden();
        $this->actingAs($pengawas)->get(route('admin.barang.create'))->assertForbidden();
        $this->actingAs($pengawas)->post(route('admin.barang.store'), [])->assertForbidden();
        $this->actingAs($pengawas)->get(route('admin.barang.edit', $barang))->assertForbidden();
        $this->actingAs($pengawas)->put(route('admin.barang.update', $barang), [])->assertForbidden();
        $this->actingAs($pengawas)->delete(route('admin.barang.destroy', $barang))->assertForbidden();
    }

    public function test_admin_dapat_mencari_dan_memfilter_barang(): void
    {
        $admin = User::factory()->admin()->create();
        $elektronik = Kategori::factory()->create(['nama_kategori' => 'Elektronik']);
        $dokumen = Kategori::factory()->create(['nama_kategori' => 'Dokumen']);

        Barang::factory()->tersedia()->create([
            'kategori_id' => $elektronik->id,
            'nama_barang' => 'Charger Asus',
            'nama_penemu' => 'Budi',
        ]);
        Barang::factory()->diajukan()->create([
            'kategori_id' => $elektronik->id,
            'nama_barang' => 'Powerbank Anker',
            'nama_penemu' => 'Siti',
        ]);
        Barang::factory()->tersedia()->create([
            'kategori_id' => $dokumen->id,
            'nama_barang' => 'KTM Fasilkom',
            'nama_penemu' => 'Andi',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.barang.index', [
                'q' => 'Charger',
                'kategori_id' => $elektronik->id,
                'status' => StatusBarang::Tersedia->value,
            ]))
            ->assertOk()
            ->assertSee('Charger Asus')
            ->assertDontSee('Powerbank Anker')
            ->assertDontSee('KTM Fasilkom');
    }

    public function test_admin_dapat_menambah_barang_dan_validasi_input_dijalankan(): void
    {
        $admin = User::factory()->admin()->create();
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Elektronik']);

        $this->actingAs($admin)
            ->post(route('admin.barang.store'), [
                'kategori_id' => $kategori->id,
                'nama_barang' => 'Flashdisk Sandisk 32GB',
                'nama_penemu' => 'Raka Pratama',
                'tanggal_ditemukan' => now()->toDateString(),
                'deskripsi' => 'Warna hitam merah di ruang lab 2.',
                'foto' => '',
            ])
            ->assertRedirect(route('admin.barang.index'))
            ->assertSessionHas('status', 'Barang temuan berhasil ditambahkan.');

        $this->assertDatabaseHas('barang', [
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Flashdisk Sandisk 32GB',
            'status' => StatusBarang::Tersedia->value,
            'foto' => null,
        ]);

        // Tanggal di masa depan dan kolom wajib kosong harus gagal validasi.
        $this->actingAs($admin)
            ->post(route('admin.barang.store'), [
                'kategori_id' => '',
                'nama_barang' => '',
                'nama_penemu' => '',
                'tanggal_ditemukan' => now()->addDay()->toDateString(),
                'deskripsi' => '',
            ])
            ->assertSessionHasErrors([
                'kategori_id',
                'nama_barang',
                'nama_penemu',
                'tanggal_ditemukan',
                'deskripsi',
            ]);
    }

    public function test_admin_dapat_mengedit_barang_tanpa_mengubah_status(): void
    {
        $admin = User::factory()->admin()->create();
        $kategoriLama = Kategori::factory()->create(['nama_kategori' => 'Lainnya']);
        $kategoriBaru = Kategori::factory()->create(['nama_kategori' => 'Kunci']);
        $barang = Barang::factory()->diajukan()->create([
            'kategori_id' => $kategoriLama->id,
            'nama_barang' => 'Kunci Motor',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.barang.update', $barang), [
                'kategori_id' => $kategoriBaru->id,
                'nama_barang' => 'Kunci Motor Honda',
                'nama_penemu' => 'Bagas',
                'tanggal_ditemukan' => now()->subDay()->toDateString(),
                'deskripsi' => 'Gantungan kulit warna cokelat.',
                'status' => StatusBarang::Terambil->value,
            ])
            ->assertRedirect(route('admin.barang.index'))
            ->assertSessionHas('status', 'Data barang berhasil diperbarui.');

        $barang->refresh();
        $this->assertSame($kategoriBaru->id, $barang->kategori_id);
        $this->assertSame('Kunci Motor Honda', $barang->nama_barang);
        $this->assertSame(StatusBarang::Diajukan, $barang->status);
    }

    public function test_hanya_barang_tersedia_yang_dapat_dihapus(): void
    {
        $admin = User::factory()->admin()->create();
        $tersedia = Barang::factory()->tersedia()->create();
        $diajukan = Barang::factory()->diajukan()->create();
        $terambil = Barang::factory()->terambil()->create();
        $dimusnahkan = Barang::factory()->dimusnahkan()->create();

        $this->actingAs($admin)
            ->delete(route('admin.barang.destroy', $tersedia))
            ->assertRedirect(route('admin.barang.index'))
            ->assertSessionHas('status', 'Data barang berhasil dihapus.');

        $this->assertDatabaseMissing('barang', ['id' => $tersedia->id]);

        foreach ([$diajukan, $terambil, $dimusnahkan] as $terkunci) {
            $this->actingAs($admin)
                ->delete(route('admin.barang.destroy', $terkunci))
                ->assertRedirect(route('admin.barang.index'))
                ->assertSessionHas('error');

            $this->assertDatabaseHas('barang', ['id' => $terkunci->id]);
        }
    }
}
