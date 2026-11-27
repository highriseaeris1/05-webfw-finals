<?php

namespace Database\Seeders;

use App\Enums\StatusPermintaan;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PermintaanSeeder extends Seeder
{
    /**
     * Isi 30 transaksi contoh dengan sebaran status sesuai kebutuhan demo.
     */
    public function run(): void
    {
        fake()->seed(2026);

        $admin = User::query()->where('role', 'admin')->orderBy('id')->first();

        $sebaran = [
            ...array_fill(0, 8, StatusPermintaan::Diajukan),
            ...array_fill(0, 5, StatusPermintaan::Disetujui),
            ...array_fill(0, 12, StatusPermintaan::Diambil),
            ...array_fill(0, 5, StatusPermintaan::Ditolak),
        ];

        foreach ($sebaran as $index => $status) {
            $nomor = $index + 1;

            // Tanggal tetap agar kode resi tidak berubah saat seeder dijalankan ulang di hari lain.
            $tanggalPengajuan = Carbon::parse('2026-08-01 09:00:00')->addDays($index * 3);
            $sudahDiproses = $status !== StatusPermintaan::Diajukan;

            // firstOrCreate menjaga seeder aman dijalankan berulang tanpa menimpa data lain.
            Permintaan::firstOrCreate(
                ['kode_resi' => sprintf('LF-%s-%04d', $tanggalPengajuan->format('Ymd'), $nomor)],
                [
                    'nomor_permintaan' => sprintf('REQ-%04d', $nomor),
                    'nama_pemohon' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'npm_nik' => fake()->numerify('################'),
                    'foto_identitas' => 'private/identitas/contoh-'.$nomor.'.jpg',
                    'foto_selfie' => 'private/selfie/contoh-'.$nomor.'.jpg',
                    'status' => $status,
                    'alasan_admin' => $status === StatusPermintaan::Ditolak
                        ? 'Ciri barang tidak sesuai dengan data pemohon.'
                        : null,
                    'tanggal_pengajuan' => $tanggalPengajuan,
                    'diproses_oleh' => $sudahDiproses ? $admin?->getKey() : null,
                    'tanggal_diproses' => $sudahDiproses ? $tanggalPengajuan->copy()->addDay() : null,
                ],
            );
        }
    }
}
