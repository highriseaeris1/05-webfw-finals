<?php

namespace App\Models;

use App\Enums\StatusPermintaan;
use Database\Factories\PermintaanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permintaan extends Model
{
    /** @use HasFactory<PermintaanFactory> */
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'nomor_permintaan',
        'kode_resi',
        'nama_pemohon',
        'email',
        'npm_nik',
        'foto_identitas',
        'foto_selfie',
        'status',
        'alasan_admin',
        'tanggal_pengajuan',
        'diproses_oleh',
        'tanggal_diproses',
    ];

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusPermintaan::class,
            'tanggal_pengajuan' => 'datetime',
            'tanggal_diproses' => 'datetime',
        ];
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
