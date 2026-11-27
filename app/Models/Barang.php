<?php

namespace App\Models;

use App\Enums\StatusBarang;
use Database\Factories\BarangFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    /** @use HasFactory<BarangFactory> */
    use HasFactory;

    protected $table = 'barang';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'kategori_id',
        'nama_barang',
        'nama_penemu',
        'tanggal_ditemukan',
        'deskripsi',
        'foto',
        'status',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_ditemukan' => 'date',
            'status' => StatusBarang::class,
            'created_at' => 'datetime',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // TODO (W5 - Atika): Tambahkan relasi hasMany ke DetailPermintaan setelah tabel detail_permintaan dibuat.
    // TODO (W6 - Dean): Tambahkan relasi hasMany ke RiwayatPengambilan setelah tabel riwayat_pengambilan dibuat.

    /**
     * Saring barang yang masih tersedia untuk diklaim.
     */
    public function scopeTersedia(Builder $query): void
    {
        $query->where('status', StatusBarang::Tersedia->value);
    }

    /**
     * Saring barang tersedia yang umurnya melewati batas tahun tertentu.
     */
    public function scopeTersediaLebihDariTahun(Builder $query, int $tahun = 1): void
    {
        $query->where('status', StatusBarang::Tersedia->value)
            ->whereDate('tanggal_ditemukan', '<=', now()->subYears($tahun)->toDateString());
    }
}
