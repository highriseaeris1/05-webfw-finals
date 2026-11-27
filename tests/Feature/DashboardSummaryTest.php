<?php

namespace Tests\Feature;

use App\Enums\StatusPermintaan;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_dashboard_admin_menampilkan_angka_ringkasan_nyata(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->pengawas()->create();
        User::factory()->inactive()->admin()->create();

        $kategori = Kategori::factory()->create(['nama_kategori' => 'Elektronik']);
        Barang::factory()->tersedia()->create(['kategori_id' => $kategori->id, 'tanggal_ditemukan' => now()->subDays(5)->toDateString()]);
        Barang::factory()->tersedia()->berumurTahun(2)->create(['kategori_id' => $kategori->id]);
        Barang::factory()->diajukan()->create(['kategori_id' => $kategori->id]);

        Permintaan::factory()->create([
            'status' => StatusPermintaan::Diajukan->value,
            'tanggal_pengajuan' => now(),
            'diproses_oleh' => null,
            'tanggal_diproses' => null,
        ]);
        Permintaan::factory()->create([
            'status' => StatusPermintaan::Disetujui->value,
            'tanggal_pengajuan' => now()->subDays(2),
            'diproses_oleh' => $admin->id,
            'tanggal_diproses' => now()->subDay(),
        ]);
        Permintaan::factory()->create([
            'status' => StatusPermintaan::Diambil->value,
            'tanggal_pengajuan' => now()->subDays(3),
            'diproses_oleh' => $admin->id,
            'tanggal_diproses' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('barangTersedia', 2)
            ->assertViewHas('barangLebihSatuTahun', 1)
            ->assertViewHas('totalBarang', 3)
            ->assertViewHas('totalKategori', 1)
            ->assertViewHas('permintaanDiajukan', 1)
            ->assertViewHas('permintaanHariIni', 1)
            ->assertViewHas('terambilBulanIni', 1)
            ->assertViewHas('totalPermintaan', 3)
            ->assertViewHas('totalPengguna', 3)
            ->assertViewHas('adminAktif', 1)
            ->assertViewHas('pengawasAktif', 1);
    }

    public function test_dashboard_pengawas_menampilkan_angka_nyata_dan_form_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $pengawas = User::factory()->pengawas()->create();
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Dokumen']);

        // Barang kandidat pemusnahan (> 3 tahun) dan barang masuk bulan ini.
        Barang::factory()->tersedia()->berumurTahun(4)->create([
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Dompet Lama',
        ]);
        Barang::factory()->tersedia()->create([
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kartu Akses Baru',
            'tanggal_ditemukan' => now()->startOfMonth()->toDateString(),
        ]);
        Barang::factory()->dimusnahkan()->create([
            'kategori_id' => $kategori->id,
            'tanggal_ditemukan' => now()->subYears(4)->toDateString(),
            'created_at' => now(),
        ]);

        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261127-0001',
            'nama_pemohon' => 'Nadia Putri',
            'status' => StatusPermintaan::Diambil->value,
            'tanggal_pengajuan' => now(),
            'diproses_oleh' => $admin->id,
            'tanggal_diproses' => now(),
        ]);

        $response = $this->actingAs($pengawas)->get(route('pengawas.dashboard'));

        $response->assertOk()
            ->assertViewHas('usulanPemusnahan', 1)
            ->assertViewHas('diambilBulanIni', 1)
            ->assertViewHas('barangMasukBulanIni', 1)
            ->assertViewHas('dimusnahkanTahunIni', 1)
            ->assertViewHas('permintaanHariIni', 1)
            ->assertSee('Dompet Lama')
            ->assertSee('LF-20261127-0001')
            ->assertSee('Nadia Putri')
            ->assertSee('Laporan rekap')
            ->assertSee(route('pengawas.laporan.pdf'), false)
            ->assertSee(route('pengawas.laporan.excel'), false);
    }
}
