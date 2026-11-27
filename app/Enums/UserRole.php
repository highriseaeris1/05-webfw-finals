<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Pengawas = 'pengawas';

    /**
     * Label peran untuk ditampilkan di antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Pengawas => 'Pengawas',
        };
    }
}
