<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    /**
     * Rute kategori sudah dilindungi middleware peran Admin.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:50', 'unique:kategori,nama_kategori'],
        ];
    }

    /**
     * Rapikan spasi berlebih sebelum validasi dijalankan.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_kategori' => is_string($this->input('nama_kategori'))
                ? trim($this->input('nama_kategori'))
                : $this->input('nama_kategori'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori tidak valid.',
            'nama_kategori.max' => 'Nama kategori maksimal :max karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_kategori' => 'nama kategori',
        ];
    }
}
