<?php

namespace App\Enums;

enum StatusBarang: string
{
    case Tersedia = 'tersedia';
    case Diajukan = 'diajukan';
    case Terambil = 'terambil';
    case Dimusnahkan = 'dimusnahkan';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Diajukan => 'Diajukan',
            self::Terambil => 'Terambil',
            self::Dimusnahkan => 'Dimusnahkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Tersedia => 'success',
            self::Diajukan => 'warning',
            self::Terambil => 'neutral',
            self::Dimusnahkan => 'neutral',
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
            self::Tersedia => [self::Diajukan, self::Dimusnahkan],
            self::Diajukan => [self::Terambil, self::Tersedia],
            self::Terambil, self::Dimusnahkan => [],
        };
    }

    public function bisaDiklam(): bool
    {
        return $this === self::Tersedia;
    }
}
