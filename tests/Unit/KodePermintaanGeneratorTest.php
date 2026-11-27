<?php

namespace Tests\Unit;

use App\Models\Permintaan;
use App\Services\KodePermintaanGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KodePermintaanGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_kode_resi_memakai_tanggal_sesuai_app_timezone(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::create(2026, 11, 26, 23, 30, 0, 'UTC'));

        $kode = app(KodePermintaanGenerator::class)->kodeResiBerikutnya();

        $this->assertSame('LF-20261127-0001', $kode);
    }

    public function test_kode_resi_naik_per_tanggal_dan_mengabaikan_tanggal_lain(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::create(2026, 11, 26, 10, 0, 0, 'Asia/Jakarta'));

        $this->buatPermintaan('LF-20261126-0001', 'REQ-0001');
        $this->buatPermintaan('LF-20261126-0002', 'REQ-0002');
        $this->buatPermintaan('LF-20261125-0007', 'REQ-0003');

        $kode = app(KodePermintaanGenerator::class)->kodeResiBerikutnya();

        $this->assertSame('LF-20261126-0003', $kode);
    }

    public function test_kode_resi_mulai_dari_satu_untuk_tanggal_baru(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::create(2026, 11, 27, 10, 0, 0, 'Asia/Jakarta'));

        $this->buatPermintaan('LF-20261126-0007', 'REQ-0001');

        $kode = app(KodePermintaanGenerator::class)->kodeResiBerikutnya();

        $this->assertSame('LF-20261127-0001', $kode);
    }

    public function test_nomor_permintaan_naik_secara_global(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 26, 10, 0, 0, 'Asia/Jakarta'));

        $this->buatPermintaan('LF-20261126-0001', 'REQ-0001');
        $this->buatPermintaan('LF-20261125-0001', 'REQ-0002');

        $nomor = app(KodePermintaanGenerator::class)->nomorPermintaanBerikutnya();

        $this->assertSame('REQ-0003', $nomor);
    }

    public function test_nomor_permintaan_mulai_dari_satu_saat_tabel_kosong(): void
    {
        $nomor = app(KodePermintaanGenerator::class)->nomorPermintaanBerikutnya();

        $this->assertSame('REQ-0001', $nomor);
    }

    private function buatPermintaan(string $kodeResi, string $nomorPermintaan): Permintaan
    {
        return Permintaan::create([
            'nomor_permintaan' => $nomorPermintaan,
            'kode_resi' => $kodeResi,
            'nama_pemohon' => 'Pemohon Uji',
            'email' => 'pemohon@example.com',
            'npm_nik' => '1234567890123456',
            'foto_identitas' => 'private/identitas/uji.jpg',
            'foto_selfie' => 'private/selfie/uji.jpg',
            'status' => 'diajukan',
            'alasan_admin' => null,
            'tanggal_pengajuan' => now(),
            'diproses_oleh' => null,
            'tanggal_diproses' => null,
        ]);
    }
}
