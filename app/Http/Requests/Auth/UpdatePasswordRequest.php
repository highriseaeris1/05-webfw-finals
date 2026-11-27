<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Semua pengguna yang sudah login boleh mengubah kata sandinya sendiri.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi ubah kata sandi.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'current_password.string' => 'Kata sandi lama tidak valid.',
            'current_password.current_password' => 'Kata sandi lama tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.string' => 'Kata sandi baru tidak valid.',
            'password.min' => 'Kata sandi baru minimal :min karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ];
    }

    /**
     * Nama atribut yang ditampilkan pada pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'kata sandi lama',
            'password' => 'kata sandi baru',
            'password_confirmation' => 'konfirmasi kata sandi baru',
        ];
    }
}
