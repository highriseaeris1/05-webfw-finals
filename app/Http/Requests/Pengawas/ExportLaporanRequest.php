<?php

namespace App\Http\Requests\Pengawas;

use Illuminate\Foundation\Http\FormRequest;

class ExportLaporanRequest extends FormRequest
{
    /**
     * Rute ekspor sudah dilindungi middleware peran Pengawas.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_awal.required' => 'Tanggal awal wajib diisi sebelum mencetak laporan.',
            'tanggal_awal.date' => 'Tanggal awal tidak valid.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi sebelum mencetak laporan.',
            'tanggal_akhir.date' => 'Tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tanggal_awal' => 'tanggal awal',
            'tanggal_akhir' => 'tanggal akhir',
        ];
    }
}
