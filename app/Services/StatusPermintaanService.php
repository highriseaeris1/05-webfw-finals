<?php

namespace App\Services;

use App\Enums\StatusPermintaan;
use App\Exceptions\TransisiStatusTidakSahException;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatusPermintaanService
{
    public function setujui(Permintaan $permintaan, User $admin, ?string $alasan = null): Permintaan
    {
        return $this->ubah($permintaan, $admin, StatusPermintaan::Disetujui, [
            'alasan_admin' => $alasan ?? $permintaan->alasan_admin,
        ]);
    }

    public function tolak(Permintaan $permintaan, User $admin, string $alasan): Permintaan
    {
        return $this->ubah($permintaan, $admin, StatusPermintaan::Ditolak, [
            'alasan_admin' => $alasan,
        ]);
    }

    public function tandaiDiambil(Permintaan $permintaan, User $admin): Permintaan
    {
        return $this->ubah($permintaan, $admin, StatusPermintaan::Diambil, [
            'alasan_admin' => $permintaan->alasan_admin,
        ]);
    }

    /**
     * @param  array<string, mixed>  $tambahan
     */
    private function ubah(Permintaan $permintaan, User $admin, StatusPermintaan $tujuan, array $tambahan): Permintaan
    {
        return DB::transaction(function () use ($permintaan, $admin, $tujuan, $tambahan): Permintaan {
            // Kunci baris agar dua permintaan bersamaan tidak sama-sama lolos validasi transisi.
            $terkunci = Permintaan::query()->lockForUpdate()->findOrFail($permintaan->getKey());
            $asal = $terkunci->status;

            if (! $asal->bolehBerubahKe($tujuan)) {
                throw TransisiStatusTidakSahException::untuk($terkunci->kode_resi, $asal, $tujuan);
            }

            $terkunci->update([
                'status' => $tujuan,
                'diproses_oleh' => $admin->getKey(),
                'tanggal_diproses' => now(),
                ...$tambahan,
            ]);

            return $terkunci;
        });
    }
}
