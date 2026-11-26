<?php

namespace Database\Factories;

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permintaan>
 */
class PermintaanFactory extends Factory
{
    protected $model = Permintaan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalPengajuan = fake()->dateTimeBetween('-30 days', 'now');
        $status = fake()->randomElement(['diajukan', 'disetujui', 'diambil', 'ditolak']);
        $sudahDiproses = in_array($status, ['disetujui', 'diambil', 'ditolak'], true);

        return [
            'nomor_permintaan' => 'REQ-'.fake()->unique()->numerify('####'),
            'kode_resi' => 'LF-'.$tanggalPengajuan->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'nama_pemohon' => fake()->name(),
            'email' => fake()->safeEmail(),
            'npm_nik' => fake()->numerify('################'),
            'foto_identitas' => 'private/identitas/'.fake()->uuid().'.jpg',
            'foto_selfie' => 'private/selfie/'.fake()->uuid().'.jpg',
            'status' => $status,
            'alasan_admin' => $status === 'ditolak' ? fake()->sentence() : null,
            'tanggal_pengajuan' => $tanggalPengajuan,
            'diproses_oleh' => $sudahDiproses ? User::factory() : null,
            'tanggal_diproses' => $sudahDiproses ? fake()->dateTimeBetween($tanggalPengajuan, 'now') : null,
        ];
    }
}
