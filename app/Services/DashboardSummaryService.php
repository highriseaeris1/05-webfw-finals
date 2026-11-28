<?php

namespace App\Services;

use App\Enums\StatusBarang;
use App\Enums\StatusPermintaan;
use App\Enums\UserRole;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Permintaan;
use App\Models\User;

class DashboardSummaryService
{
    /**
     * Susun ringkasan metrik nyata untuk dashboard Admin.
     *
     * @return array<string, mixed>
     */
    public function ringkasanAdmin(): array
    {
        $sekarang = now();
        $awalBulan = $sekarang->copy()->startOfMonth();
        $akhirBulan = $sekarang->copy()->endOfMonth();
        $perStatus = $this->hitungPermintaanPerStatus();

        return [
            'barangTersedia' => Barang::query()->tersedia()->count(),
            'barangLebihSatuTahun' => Barang::query()->tersediaLebihDariTahun(1)->count(),
            'totalBarang' => Barang::query()->count(),
            'totalKategori' => Kategori::query()->count(),
            'permintaanDiajukan' => $perStatus[StatusPermintaan::Diajukan->value],
            'permintaanHariIni' => Permintaan::query()
                ->whereDate('tanggal_pengajuan', $sekarang->toDateString())
                ->count(),
            'terambilBulanIni' => Permintaan::query()
                ->where('status', StatusPermintaan::Diambil->value)
                ->whereBetween('tanggal_diproses', [$awalBulan, $akhirBulan])
                ->count(),
            'permintaanPerStatus' => $perStatus,
            'totalPermintaan' => array_sum($perStatus),
            'totalPengguna' => User::query()->count(),
            'adminAktif' => User::active()->where('role', UserRole::Admin->value)->count(),
            'pengawasAktif' => User::active()->where('role', UserRole::Pengawas->value)->count(),
        ];
    }

    /**
     * Susun ringkasan pemantauan nyata untuk dashboard Pengawas.
     *
     * @return array<string, mixed>
     */
    public function ringkasanPengawas(): array
    {
        $sekarang = now();
        $awalBulan = $sekarang->copy()->startOfMonth();
        $akhirBulan = $sekarang->copy()->endOfMonth();
        $perStatus = $this->hitungPermintaanPerStatus();

        return [
            'usulanPemusnahan' => Barang::query()->tersediaLebihDariTahun(3)->count(),
            'diambilBulanIni' => Permintaan::query()
                ->where('status', StatusPermintaan::Diambil->value)
                ->whereBetween('tanggal_diproses', [$awalBulan, $akhirBulan])
                ->count(),
            'barangMasukBulanIni' => Barang::query()
                ->whereBetween('tanggal_ditemukan', [$awalBulan->toDateString(), $akhirBulan->toDateString()])
                ->count(),
            // TODO (W6 - Dean): Ganti acuan tahun ke tanggal_dimusnahkan setelah alur pemusnahan dibuat.
            'dimusnahkanTahunIni' => Barang::query()
                ->where('status', StatusBarang::Dimusnahkan->value)
                ->whereYear('created_at', $sekarang->year)
                ->count(),
            'permintaanHariIni' => Permintaan::query()
                ->whereDate('tanggal_pengajuan', $sekarang->toDateString())
                ->count(),
            'permintaanPerStatus' => $perStatus,
            'totalPermintaan' => array_sum($perStatus),
            'kandidatPemusnahan' => Barang::query()
                ->with('kategori')
                ->tersediaLebihDariTahun(3)
                ->orderBy('tanggal_ditemukan')
                ->orderBy('id')
                ->limit(5)
                ->get(),
            // TODO (W6 - Dean): Alihkan ke tabel riwayat_pengambilan agar menampilkan daftar per barang.
            'riwayatDiambilTerbaru' => Permintaan::query()
                ->with('pemroses')
                ->where('status', StatusPermintaan::Diambil->value)
                ->orderByDesc('tanggal_diproses')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Agregasi jumlah permintaan untuk keempat status agar nilai nol tetap lengkap.
     *
     * @return array{diajukan: int, disetujui: int, diambil: int, ditolak: int}
     */
    private function hitungPermintaanPerStatus(): array
    {
        $jumlah = Permintaan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $hasil = [
            StatusPermintaan::Diajukan->value => 0,
            StatusPermintaan::Disetujui->value => 0,
            StatusPermintaan::Diambil->value => 0,
            StatusPermintaan::Ditolak->value => 0,
        ];

        foreach ($jumlah as $status => $total) {
            if (array_key_exists($status, $hasil)) {
                $hasil[$status] = (int) $total;
            }
        }

        return $hasil;
    }
}
