<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Buat akun demo hanya jika email-nya belum terdaftar.
     */
    public function run(): void
    {
        $accounts = [
            [
                'email' => 'admin@contoh.id',
                'name' => 'Admin TU',
                'username' => 'admin.tu',
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
            [
                'email' => 'pengawas@contoh.id',
                'name' => 'Pengawas Fasilkom',
                'username' => 'pengawas',
                'role' => UserRole::Pengawas,
                'is_active' => true,
            ],
            [
                'email' => 'admin2@contoh.id',
                'name' => 'Petugas TU 2',
                'username' => 'admin2',
                'role' => UserRole::Admin,
                'is_active' => false,
            ],
        ];

        foreach ($accounts as $account) {
            // firstOrCreate tidak menimpa akun yang sudah ada dan aman dijalankan berulang.
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make('password'),
                    'role' => $account['role'],
                    'is_active' => $account['is_active'],
                ],
            );
        }
    }
}
