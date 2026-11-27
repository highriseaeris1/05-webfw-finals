<?php

namespace App\Enums;

enum StatusPermintaan: string
{
    case Diajukan = 'diajukan';
    case Disetujui = 'disetujui';
    case Diambil = 'diambil';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Disetujui => 'Disetujui',
            self::Diambil => 'Diambil',
            self::Ditolak => 'Ditolak',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Diajukan => 'warning',
            self::Disetujui => 'info',
            self::Diambil => 'neutral',
            self::Ditolak => 'danger',
        };
    }

    public function bolehBerubahKe(self $tujuan): bool
    {
        return in_array($tujuan, $this->transisiSah(), true);
    }

    /**
     * Aturan transisi mengikuti dokumen Use Case dan Struktur Data halaman 7.
     *
     * @return array<int, self>
     */
    public function transisiSah(): array
    {
        return match ($this) {
            self::Diajukan => [self::Disetujui, self::Ditolak],
            self::Disetujui => [self::Diambil],
            self::Diambil, self::Ditolak => [],
        };
    }

    public function masihAktif(): bool
    {
        return in_array($this, [self::Diajukan, self::Disetujui], true);
    }
}
