<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Rute pembuatan akun sudah dilindungi middleware peran Admin.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi data akun baru.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'role' => ['required', Rule::in([UserRole::Admin->value, UserRole::Pengawas->value])],
        ];
    }

    /**
     * Rapikan input teks agar spasi di awal atau akhir tidak tersimpan.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'username' => is_string($this->input('username')) ? trim($this->input('username')) : $this->input('username'),
            'email' => is_string($this->input('email')) ? trim($this->input('email')) : $this->input('email'),
        ]);
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap tidak valid.',
            'name.max' => 'Nama lengkap maksimal :max karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username tidak valid.',
            'username.max' => 'Username maksimal :max karakter.',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal :max karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi tidak valid.',
            'password.min' => 'Kata sandi minimal :min karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid.',
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
            'name' => 'nama lengkap',
            'username' => 'username',
            'email' => 'email',
            'password' => 'kata sandi',
            'password_confirmation' => 'konfirmasi kata sandi',
            'role' => 'peran',
        ];
    }
}
