<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TolakPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alasan_admin' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alasan_admin.required' => 'Alasan admin wajib diisi saat menolak permintaan.',
            'alasan_admin.string' => 'Alasan admin harus berupa teks.',
            'alasan_admin.min' => 'Alasan admin minimal 5 karakter.',
            'alasan_admin.max' => 'Alasan admin maksimal 1000 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'alasan_admin' => 'alasan admin',
        ];
    }
}
