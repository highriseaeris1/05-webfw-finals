<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetujuiPermintaanRequest extends FormRequest
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
            'alasan_admin' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alasan_admin.string' => 'Alasan admin harus berupa teks.',
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
