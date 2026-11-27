<?php

namespace Tests\Feature;

use App\Enums\StatusPermintaan;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusPermintaanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function permintaan(StatusPermintaan $status = StatusPermintaan::Diajukan): Permintaan
    {
        return Permintaan::factory()->create([
            'status' => $status,
            'alasan_admin' => null,
            'diproses_oleh' => null,
            'tanggal_diproses' => null,
        ]);
    }

    public function test_admin_dapat_menyetujui_permintaan_diajukan(): void
    {
        $admin = $this->admin();
        $permintaan = $this->permintaan();

        $response = $this->actingAs($admin)->patch(route('admin.permintaan.setujui', $permintaan));

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $permintaan->refresh();
        $this->assertSame(StatusPermintaan::Disetujui, $permintaan->status);
        $this->assertSame($admin->id, $permintaan->diproses_oleh);
        $this->assertNotNull($permintaan->tanggal_diproses);
    }

    public function test_penolakan_wajib_menyertakan_alasan(): void
    {
        $permintaan = $this->permintaan();

        $response = $this->actingAs($this->admin())
            ->patch(route('admin.permintaan.tolak', $permintaan), ['alasan_admin' => '']);

        $response->assertSessionHasErrors('alasan_admin');
        $this->assertSame(StatusPermintaan::Diajukan, $permintaan->refresh()->status);
    }

    public function test_penolakan_menyimpan_alasan_dan_status_ditolak(): void
    {
        $permintaan = $this->permintaan();

        $response = $this->actingAs($this->admin())->patch(route('admin.permintaan.tolak', $permintaan), [
            'alasan_admin' => 'Ciri barang tidak cocok dengan keterangan pemohon.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $permintaan->refresh();
        $this->assertSame(StatusPermintaan::Ditolak, $permintaan->status);
        $this->assertSame('Ciri barang tidak cocok dengan keterangan pemohon.', $permintaan->alasan_admin);
    }

    public function test_admin_menandai_permintaan_disetujui_sebagai_diambil(): void
    {
        $permintaan = $this->permintaan(StatusPermintaan::Disetujui);

        $response = $this->actingAs($this->admin())->patch(route('admin.permintaan.diambil', $permintaan));

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertSame(StatusPermintaan::Diambil, $permintaan->refresh()->status);
    }

    public function test_transisi_ilegal_ditolak_tanpa_mengubah_data(): void
    {
        $permintaan = $this->permintaan(StatusPermintaan::Diambil);

        $response = $this->actingAs($this->admin())->patch(route('admin.permintaan.setujui', $permintaan));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(StatusPermintaan::Diambil, $permintaan->refresh()->status);
    }

    public function test_menyetujui_dua_kali_tidak_mengubah_apa_pun(): void
    {
        $admin = $this->admin();
        $permintaan = $this->permintaan();

        $this->actingAs($admin)->patch(route('admin.permintaan.setujui', $permintaan))->assertSessionHas('status');
        $kedua = $this->actingAs($admin)->patch(route('admin.permintaan.setujui', $permintaan));

        $kedua->assertSessionHas('error');
        $this->assertSame(StatusPermintaan::Disetujui, $permintaan->refresh()->status);
    }

    public function test_pengawas_tidak_dapat_mengubah_status(): void
    {
        $permintaan = $this->permintaan();

        $response = $this->actingAs(User::factory()->pengawas()->create())
            ->patch(route('admin.permintaan.setujui', $permintaan));

        $response->assertForbidden();
        $this->assertSame(StatusPermintaan::Diajukan, $permintaan->refresh()->status);
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $permintaan = $this->permintaan();

        $this->patch(route('admin.permintaan.setujui', $permintaan))
            ->assertRedirect(route('login'));
    }
}
