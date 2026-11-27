<?php

namespace Tests\Feature;

use App\Models\Permintaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermintaanIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_daftar_permintaan_dapat_dibuka(): void
    {
        Permintaan::factory()->create();

        $response = $this->get(route('admin.permintaan.index'));

        $response->assertOk();
        $response->assertViewIs('admin.permintaan.index');
    }

    public function test_paginasi_menampilkan_sepuluh_data_per_halaman(): void
    {
        Permintaan::factory()->count(25)->create();

        $response = $this->get(route('admin.permintaan.index'));

        $response->assertOk();
        $response->assertViewHas('permintaan', function ($paginator): bool {
            return $paginator->perPage() === 10
                && $paginator->count() === 10
                && $paginator->total() === 25
                && $paginator->lastPage() === 3;
        });

        $halamanKedua = $this->get(route('admin.permintaan.index', ['page' => 2]));

        $halamanKedua->assertOk();
        $halamanKedua->assertViewHas('permintaan', function ($paginator): bool {
            return $paginator->currentPage() === 2
                && $paginator->count() === 10;
        });
    }

    public function test_pencarian_berdasarkan_kode_resi(): void
    {
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261126-0001',
            'nomor_permintaan' => 'REQ-0001',
            'nama_pemohon' => 'Rina Kode',
        ]);
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261126-0002',
            'nomor_permintaan' => 'REQ-0002',
            'nama_pemohon' => 'Dimas Nama',
        ]);

        $response = $this->get(route('admin.permintaan.index', ['q' => 'LF-20261126-0001']));

        $response->assertOk();
        $response->assertSee('Rina Kode');
        $response->assertDontSee('Dimas Nama');
    }

    public function test_pencarian_berdasarkan_nama_pemohon(): void
    {
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261126-0001',
            'nomor_permintaan' => 'REQ-0001',
            'nama_pemohon' => 'Rina Kode',
        ]);
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261126-0002',
            'nomor_permintaan' => 'REQ-0002',
            'nama_pemohon' => 'Dimas Nama',
        ]);

        $response = $this->get(route('admin.permintaan.index', ['q' => 'Dimas']));

        $response->assertOk();
        $response->assertSee('Dimas Nama');
        $response->assertDontSee('Rina Kode');
    }

    public function test_filter_status_hanya_menampilkan_status_yang_dipilih(): void
    {
        Permintaan::factory()->create(['status' => 'diajukan', 'nama_pemohon' => 'Pemohon Diajukan']);
        Permintaan::factory()->create(['status' => 'disetujui', 'nama_pemohon' => 'Pemohon Disetujui']);
        Permintaan::factory()->create(['status' => 'diambil', 'nama_pemohon' => 'Pemohon Diambil']);
        Permintaan::factory()->create(['status' => 'ditolak', 'nama_pemohon' => 'Pemohon Ditolak']);

        $response = $this->get(route('admin.permintaan.index', ['status' => 'disetujui']));

        $response->assertOk();
        $response->assertSee('Pemohon Disetujui');
        $response->assertDontSee('Pemohon Diajukan');
        $response->assertDontSee('Pemohon Diambil');
        $response->assertDontSee('Pemohon Ditolak');
    }

    public function test_filter_rentang_tanggal_pengajuan(): void
    {
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261101-0001',
            'nomor_permintaan' => 'REQ-0001',
            'nama_pemohon' => 'Pemohon Awal',
            'tanggal_pengajuan' => '2026-11-01 10:00:00',
        ]);
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261115-0001',
            'nomor_permintaan' => 'REQ-0002',
            'nama_pemohon' => 'Pemohon Tengah',
            'tanggal_pengajuan' => '2026-11-15 10:00:00',
        ]);
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261130-0001',
            'nomor_permintaan' => 'REQ-0003',
            'nama_pemohon' => 'Pemohon Akhir',
            'tanggal_pengajuan' => '2026-11-30 10:00:00',
        ]);

        $response = $this->get(route('admin.permintaan.index', [
            'tanggal_awal' => '2026-11-10',
            'tanggal_akhir' => '2026-11-20',
        ]));

        $response->assertOk();
        $response->assertSee('Pemohon Tengah');
        $response->assertDontSee('Pemohon Awal');
        $response->assertDontSee('Pemohon Akhir');
    }

    public function test_validasi_tanggal_akhir_harus_setelah_atau_sama_dengan_tanggal_awal(): void
    {
        $response = $this->get(route('admin.permintaan.index', [
            'tanggal_awal' => '2026-11-20',
            'tanggal_akhir' => '2026-11-10',
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('tanggal_akhir');
    }

    public function test_filter_tetap_terbawa_di_tautan_halaman_berikutnya(): void
    {
        Permintaan::factory()->count(25)->create([
            'status' => 'disetujui',
            'nama_pemohon' => 'Pemohon Filter',
            'tanggal_pengajuan' => '2026-11-15 10:00:00',
        ]);

        $response = $this->get(route('admin.permintaan.index', [
            'q' => 'Pemohon',
            'status' => 'disetujui',
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]));

        $response->assertOk();

        $paginator = $response->viewData('permintaan');
        $tautanHalamanDua = $paginator->url(2);

        $this->assertSame(25, $paginator->total());
        $this->assertStringContainsString('page=2', $tautanHalamanDua);
        $this->assertStringContainsString('q=Pemohon', $tautanHalamanDua);
        $this->assertStringContainsString('status=disetujui', $tautanHalamanDua);
        $this->assertStringContainsString('tanggal_awal=2026-11-01', $tautanHalamanDua);
        $this->assertStringContainsString('tanggal_akhir=2026-11-30', $tautanHalamanDua);
    }
}
