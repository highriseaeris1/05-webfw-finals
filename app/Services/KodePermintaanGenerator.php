<?php

namespace App\Services;

use App\Models\Permintaan;
use Illuminate\Support\Facades\DB;

class KodePermintaanGenerator
{
    public function kodeResiBerikutnya(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'LF-'.now(config('app.timezone'))->format('Ymd').'-';

            // Kunci baris terakhir agar permintaan lain menunggu dan membaca urutan terbaru.
            $terakhir = Permintaan::query()
                ->where('kode_resi', 'like', $prefix.'%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $urutan = $terakhir
                ? ((int) substr(strrchr($terakhir->kode_resi, '-'), 1)) + 1
                : 1;

            return $prefix.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
        });
    }

    public function nomorPermintaanBerikutnya(): string
    {
        return DB::transaction(function (): string {
            // Kunci baris terakhir agar nomor global tidak dihitung ganda.
            $terakhir = Permintaan::query()
                ->where('nomor_permintaan', 'like', 'REQ-%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $urutan = $terakhir
                ? ((int) substr(strrchr($terakhir->nomor_permintaan, '-'), 1)) + 1
                : 1;

            return 'REQ-'.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
        });
    }
}
