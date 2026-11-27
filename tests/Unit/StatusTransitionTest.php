<?php

namespace Tests\Unit;

use App\Enums\StatusBarang;
use App\Enums\StatusPermintaan;
use PHPUnit\Framework\TestCase;

class StatusTransitionTest extends TestCase
{
    public function test_permintaan_hanya_boleh_berubah_secara_bertahap(): void
    {
        $this->assertTrue(StatusPermintaan::Diajukan->bolehBerubahKe(StatusPermintaan::Disetujui));
        $this->assertTrue(StatusPermintaan::Diajukan->bolehBerubahKe(StatusPermintaan::Ditolak));
        $this->assertTrue(StatusPermintaan::Disetujui->bolehBerubahKe(StatusPermintaan::Diambil));

        $this->assertFalse(StatusPermintaan::Diajukan->bolehBerubahKe(StatusPermintaan::Diambil));
        $this->assertFalse(StatusPermintaan::Disetujui->bolehBerubahKe(StatusPermintaan::Ditolak));
        $this->assertFalse(StatusPermintaan::Ditolak->bolehBerubahKe(StatusPermintaan::Diambil));
        $this->assertFalse(StatusPermintaan::Diambil->bolehBerubahKe(StatusPermintaan::Disetujui));
    }

    public function test_barang_mengikuti_alur_pengajuan_dan_penolakan(): void
    {
        $this->assertTrue(StatusBarang::Tersedia->bolehBerubahKe(StatusBarang::Diajukan));
        $this->assertTrue(StatusBarang::Diajukan->bolehBerubahKe(StatusBarang::Terambil));
        $this->assertTrue(StatusBarang::Diajukan->bolehBerubahKe(StatusBarang::Tersedia));
        $this->assertTrue(StatusBarang::Tersedia->bolehBerubahKe(StatusBarang::Dimusnahkan));

        $this->assertFalse(StatusBarang::Terambil->bolehBerubahKe(StatusBarang::Tersedia));
        $this->assertFalse(StatusBarang::Dimusnahkan->bolehBerubahKe(StatusBarang::Tersedia));
    }

    public function test_hanya_barang_tersedia_yang_bisa_diklam(): void
    {
        $this->assertTrue(StatusBarang::Tersedia->bisaDiklam());

        foreach ([StatusBarang::Diajukan, StatusBarang::Terambil, StatusBarang::Dimusnahkan] as $status) {
            $this->assertFalse($status->bisaDiklam());
        }
    }

    public function test_setiap_status_punya_label_dan_variant_badge(): void
    {
        foreach (StatusPermintaan::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertNotSame('', $status->badgeVariant());
        }

        foreach (StatusBarang::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertNotSame('', $status->badgeVariant());
        }
    }
}
