<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Semua pengunjung boleh mencoba login; pembatasan dilakukan saat verifikasi kredensial.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi dasar untuk formulir login.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Rapikan input agar spasi di awal atau akhir tidak menggagalkan login.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('identifier'))) {
            $this->merge([
                'identifier' => trim($this->input('identifier')),
            ]);
        }
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia untuk formulir login.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'identifier.required' => 'Email atau username wajib diisi.',
            'identifier.string' => 'Email atau username tidak valid.',
            'identifier.max' => 'Email atau username terlalu panjang.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi tidak valid.',
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
            'identifier' => 'email atau username',
            'password' => 'kata sandi',
        ];
    }

    /**
     * Verifikasi kredensial lalu pastikan akun aktif dan berperan staf.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $identifier = (string) $this->input('identifier');

        // Email mengandung "@"; selain itu diperlakukan sebagai username.
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $identifier, 'password' => (string) $this->input('password')])) {
            throw ValidationException::withMessages([
                'auth' => 'Email/username atau kata sandi salah.',
            ]);
        }

        $user = Auth::user();

        if (! $user->isActive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'auth' => 'Akun ini tidak aktif. Hubungi Admin Tata Usaha.',
            ]);
        }

        // Ambil nilai mentah agar akun dengan role di luar enum tetap ditolak dengan pesan yang jelas.
        $role = $user->getRawOriginal('role');
        $allowedRoles = [UserRole::Admin->value, UserRole::Pengawas->value];

        if (! in_array($role, $allowedRoles, true)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'auth' => 'Akun ini tidak memiliki akses ke panel staf.',
            ]);
        }
    }
}
