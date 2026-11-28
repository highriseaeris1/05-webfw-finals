<?php

namespace Tests\Feature\Pengawas;

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman panel memakai @vite; matikan Vite agar tes tidak membutuhkan hasil build.
        $this->withoutVite();
    }

    private function pengawas(): User
    {
        return User::factory()->pengawas()->create();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('pengawas.laporan.index'))->assertRedirect(route('login'));

        $this->get(route('pengawas.laporan.pdf', [
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]))->assertRedirect(route('login'));

        $this->get(route('pengawas.laporan.excel', [
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]))->assertRedirect(route('login'));
    }

    public function test_admin_tidak_dapat_mengakses_laporan(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('pengawas.laporan.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('pengawas.laporan.pdf', [
                'tanggal_awal' => '2026-11-01',
                'tanggal_akhir' => '2026-11-30',
            ]))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('pengawas.laporan.excel', [
                'tanggal_awal' => '2026-11-01',
                'tanggal_akhir' => '2026-11-30',
            ]))
            ->assertForbidden();
    }

    public function test_pengawas_dapat_membuka_halaman_laporan(): void
    {
        $this->actingAs($this->pengawas())
            ->get(route('pengawas.laporan.index'))
            ->assertOk()
            ->assertViewIs('pengawas.laporan.index')
            ->assertSee('Laporan rekap');
    }

    public function test_default_periode_memakai_bulan_berjalan(): void
    {
        Permintaan::factory()->create([
            'nama_pemohon' => 'Pemohon Bulan Ini',
            'tanggal_pengajuan' => now()->startOfMonth(),
        ]);
        Permintaan::factory()->create([
            'nama_pemohon' => 'Pemohon Bulan Lalu',
            'tanggal_pengajuan' => now()->subMonthNoOverflow()->endOfMonth(),
        ]);

        $this->actingAs($this->pengawas())
            ->get(route('pengawas.laporan.index'))
            ->assertOk()
            ->assertSee('Pemohon Bulan Ini')
            ->assertDontSee('Pemohon Bulan Lalu');
    }

    public function test_ringkasan_menghitung_jumlah_per_status(): void
    {
        $pengawas = $this->pengawas();
        $tanggal = '2026-11-15 10:00:00';

        Permintaan::factory()->count(2)->create(['status' => 'diajukan', 'tanggal_pengajuan' => $tanggal]);
        Permintaan::factory()->create(['status' => 'disetujui', 'tanggal_pengajuan' => $tanggal]);
        Permintaan::factory()->create(['status' => 'diambil', 'tanggal_pengajuan' => $tanggal]);
        Permintaan::factory()->create(['status' => 'ditolak', 'tanggal_pengajuan' => $tanggal]);

        $response = $this->actingAs($pengawas)->get(route('pengawas.laporan.index', [
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]));

        $response->assertOk();
        $this->assertSame([
            'total' => 5,
            'diajukan' => 2,
            'disetujui' => 1,
            'diambil' => 1,
            'ditolak' => 1,
        ], $response->viewData('ringkasan'));
    }

    public function test_filter_tanggal_inklusif_sampai_akhir_hari(): void
    {
        Permintaan::factory()->create([
            'nama_pemohon' => 'Batas Akhir',
            'tanggal_pengajuan' => '2026-11-10 23:59:59',
        ]);
        Permintaan::factory()->create([
            'nama_pemohon' => 'Di Luar Periode',
            'tanggal_pengajuan' => '2026-11-11 00:00:00',
        ]);

        $this->actingAs($this->pengawas())
            ->get(route('pengawas.laporan.index', [
                'tanggal_awal' => '2026-11-01',
                'tanggal_akhir' => '2026-11-10',
            ]))
            ->assertOk()
            ->assertSee('Batas Akhir')
            ->assertDontSee('Di Luar Periode');
    }

    public function test_validasi_tanggal_akhir_tidak_boleh_mendahului_tanggal_awal(): void
    {
        $this->actingAs($this->pengawas())
            ->from(route('pengawas.laporan.index'))
            ->get(route('pengawas.laporan.index', [
                'tanggal_awal' => '2026-11-20',
                'tanggal_akhir' => '2026-11-10',
            ]))
            ->assertRedirect(route('pengawas.laporan.index'))
            ->assertSessionHasErrors('tanggal_akhir');
    }

    public function test_ekspor_wajib_mengirim_tanggal(): void
    {
        $pengawas = $this->pengawas();

        $this->actingAs($pengawas)
            ->from(route('pengawas.laporan.index'))
            ->get(route('pengawas.laporan.pdf'))
            ->assertRedirect(route('pengawas.laporan.index'))
            ->assertSessionHasErrors(['tanggal_awal', 'tanggal_akhir']);

        $this->actingAs($pengawas)
            ->from(route('pengawas.laporan.index'))
            ->get(route('pengawas.laporan.excel'))
            ->assertRedirect(route('pengawas.laporan.index'))
            ->assertSessionHasErrors(['tanggal_awal', 'tanggal_akhir']);
    }

    public function test_pengawas_dapat_mengunduh_laporan_pdf(): void
    {
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261110-0001',
            'tanggal_pengajuan' => '2026-11-10 10:00:00',
        ]);

        $response = $this->actingAs($this->pengawas())->get(route('pengawas.laporan.pdf', [
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'laporan-permintaan-20261101-20261130.pdf',
            (string) $response->headers->get('content-disposition'),
        );
    }

    public function test_pengawas_dapat_mengunduh_laporan_excel(): void
    {
        Permintaan::factory()->create([
            'kode_resi' => 'LF-20261110-0001',
            'tanggal_pengajuan' => '2026-11-10 10:00:00',
        ]);

        $response = $this->actingAs($this->pengawas())->get(route('pengawas.laporan.excel', [
            'tanggal_awal' => '2026-11-01',
            'tanggal_akhir' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertDownload('laporan-permintaan-20261101-20261130.xlsx');
    }
}
