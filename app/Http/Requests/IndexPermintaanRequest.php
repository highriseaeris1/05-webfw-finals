<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPermintaanRequest extends FormRequest
{
    /**
     * Sementara semua permintaan diizinkan karena autentikasi belum tersedia.
     * TODO: Ganti dengan pengecekan Policy setelah fitur login dan peran admin selesai.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['diajukan', 'disetujui', 'diambil', 'ditolak'])],
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
            'q.string' => 'Kata kunci pencarian harus berupa teks.',
            'q.max' => 'Kata kunci pencarian maksimal 100 karakter.',
            'status.in' => 'Status yang dipilih tidak valid.',
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
            'q' => 'kata kunci',
            'status' => 'status',
            'tanggal_awal' => 'tanggal awal',
            'tanggal_akhir' => 'tanggal akhir',
        ];
    }
}
