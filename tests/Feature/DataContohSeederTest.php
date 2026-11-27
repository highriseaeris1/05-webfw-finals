<?php

namespace Tests\Feature;

use App\Enums\StatusPermintaan;
use App\Enums\UserRole;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataContohSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_menghasilkan_tiga_akun_uji_dan_tiga_puluh_transaksi(): void
    {
        $this->seed();

        $this->assertSame(3, User::query()->whereIn('email', [
            'admin@contoh.id',
            'pengawas@contoh.id',
            'admin2@contoh.id',
        ])->count());

        $this->assertTrue(User::query()->where('role', UserRole::Admin)->where('is_active', true)->exists());
        $this->assertTrue(User::query()->where('role', UserRole::Pengawas)->where('is_active', true)->exists());

        $this->assertSame(30, Permintaan::query()->count());
    }

    public function test_sebaran_status_sesuai_kebutuhan_demo(): void
    {
        $this->seed();

        $this->assertSame(8, Permintaan::query()->where('status', 'diajukan')->count());
        $this->assertSame(5, Permintaan::query()->where('status', 'disetujui')->count());
        $this->assertSame(12, Permintaan::query()->where('status', 'diambil')->count());
        $this->assertSame(5, Permintaan::query()->where('status', 'ditolak')->count());
    }

    public function test_transaksi_yang_sudah_diproses_punya_admin_yang_menangani(): void
    {
        $this->seed();

        $sudahDiproses = Permintaan::query()
            ->where('status', '!=', StatusPermintaan::Diajukan->value)
            ->get();

        $this->assertSame(22, $sudahDiproses->count());
        $this->assertTrue($sudahDiproses->every(fn (Permintaan $item): bool => $item->diproses_oleh !== null));
        $this->assertTrue($sudahDiproses->every(fn (Permintaan $item): bool => $item->tanggal_diproses !== null));
    }

    public function test_permintaan_ditolak_selalu_punya_alasan_admin(): void
    {
        $this->seed();

        $ditolak = Permintaan::query()->where('status', 'ditolak')->get();

        $this->assertTrue($ditolak->every(fn (Permintaan $item): bool => filled($item->alasan_admin)));
    }

    public function test_seeder_aman_dijalankan_berulang(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(30, Permintaan::query()->count());
    }
}
