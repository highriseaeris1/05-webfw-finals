<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    /**
     * Rute barang sudah dilindungi middleware peran Admin.
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
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'nama_barang' => ['required', 'string', 'max:100'],
            'nama_penemu' => ['required', 'string', 'max:100'],
            'tanggal_ditemukan' => ['required', 'date', 'before_or_equal:today'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            // TODO (W10 - Ibnu): Ubah validasi foto menjadi unggahan gambar ke disk public setelah modul W10 tersedia.
            'foto' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Rapikan spasi teks dan ubah string kosong foto menjadi null.
     */
    protected function prepareForValidation(): void
    {
        $foto = $this->input('foto');

        $this->merge([
            'nama_barang' => is_string($this->input('nama_barang')) ? trim($this->input('nama_barang')) : $this->input('nama_barang'),
            'nama_penemu' => is_string($this->input('nama_penemu')) ? trim($this->input('nama_penemu')) : $this->input('nama_penemu'),
            'deskripsi' => is_string($this->input('deskripsi')) ? trim($this->input('deskripsi')) : $this->input('deskripsi'),
            'foto' => is_string($foto) && trim($foto) === '' ? null : (is_string($foto) ? trim($foto) : $foto),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.integer' => 'Kategori tidak valid.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string' => 'Nama barang tidak valid.',
            'nama_barang.max' => 'Nama barang maksimal :max karakter.',
            'nama_penemu.required' => 'Nama penemu wajib diisi.',
            'nama_penemu.string' => 'Nama penemu tidak valid.',
            'nama_penemu.max' => 'Nama penemu maksimal :max karakter.',
            'tanggal_ditemukan.required' => 'Tanggal ditemukan wajib diisi.',
            'tanggal_ditemukan.date' => 'Format tanggal ditemukan tidak valid.',
            'tanggal_ditemukan.before_or_equal' => 'Tanggal ditemukan tidak boleh melebihi hari ini.',
            'deskripsi.required' => 'Deskripsi barang wajib diisi.',
            'deskripsi.string' => 'Deskripsi barang tidak valid.',
            'deskripsi.max' => 'Deskripsi barang maksimal :max karakter.',
            'foto.string' => 'Path foto tidak valid.',
            'foto.max' => 'Path foto maksimal :max karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kategori_id' => 'kategori',
            'nama_barang' => 'nama barang',
            'nama_penemu' => 'nama penemu',
            'tanggal_ditemukan' => 'tanggal ditemukan',
            'deskripsi' => 'deskripsi barang',
            'foto' => 'foto',
        ];
    }
}
