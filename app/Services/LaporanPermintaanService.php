<?php

namespace App\Services;

use App\Models\Permintaan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanPermintaanService
{
    /**
     * Hitung jumlah permintaan per status pada rentang tanggal pengajuan.
     *
     * @return array{total: int, diajukan: int, disetujui: int, diambil: int, ditolak: int}
     */
    public function ringkasan(Carbon $tanggalAwal, Carbon $tanggalAkhir): array
    {
        // Query builder dipakai agar nilai status tetap string mentah, bukan enum hasil cast model.
        $jumlah = DB::table('permintaan')
            ->whereBetween('tanggal_pengajuan', [
                $tanggalAwal->copy()->startOfDay(),
                $tanggalAkhir->copy()->endOfDay(),
            ])
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $perStatus = [
            'diajukan' => 0,
            'disetujui' => 0,
            'diambil' => 0,
            'ditolak' => 0,
        ];

        foreach ($jumlah as $status => $total) {
            if (array_key_exists($status, $perStatus)) {
                $perStatus[$status] = (int) $total;
            }
        }

        return ['total' => array_sum($perStatus)] + $perStatus;
    }

    /**
     * Ambil data laporan dengan paginasi untuk ditampilkan di layar.
     */
    public function daftar(Carbon $tanggalAwal, Carbon $tanggalAkhir, int $perHalaman = 15): LengthAwarePaginator
    {
        return $this->query($tanggalAwal, $tanggalAkhir)
            ->with('pemroses')
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->paginate($perHalaman)
            ->withQueryString();
    }

    /**
     * Ambil seluruh data laporan untuk kebutuhan ekspor.
     *
     * @return Collection<int, Permintaan>
     */
    public function semua(Carbon $tanggalAwal, Carbon $tanggalAkhir): Collection
    {
        return $this->query($tanggalAwal, $tanggalAkhir)
            ->with('pemroses')
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Batasi query pada periode tanggal pengajuan secara inklusif.
     */
    private function query(Carbon $tanggalAwal, Carbon $tanggalAkhir): Builder
    {
        return Permintaan::query()->whereBetween('tanggal_pengajuan', [
            $tanggalAwal->copy()->startOfDay(),
            $tanggalAkhir->copy()->endOfDay(),
        ]);
    }
}
