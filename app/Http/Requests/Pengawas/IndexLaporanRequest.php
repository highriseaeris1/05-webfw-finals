<?php

namespace App\Http\Requests\Pengawas;

use Illuminate\Foundation\Http\FormRequest;

class IndexLaporanRequest extends FormRequest
{
    /**
     * Rute laporan sudah dilindungi middleware peran Pengawas.
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
            'tanggal_awal' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_awal'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_awal.date' => 'Tanggal awal tidak valid.',
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
