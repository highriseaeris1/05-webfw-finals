<?php

namespace App\Exports;

use App\Models\Permintaan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPermintaanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    private int $nomor = 0;

    /**
     * @param  Collection<int, Permintaan>  $permintaan
     */
    public function __construct(private readonly Collection $permintaan) {}

    /**
     * @return Collection<int, Permintaan>
     */
    public function collection(): Collection
    {
        return $this->permintaan;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Resi',
            'Nomor Permintaan',
            'Nama Pemohon',
            'Email',
            'NPM/NIK',
            'Status',
            'Tanggal Pengajuan',
            'Diproses Oleh',
            'Tanggal Diproses',
            'Alasan Admin',
        ];
    }

    /**
     * @param  Permintaan  $permintaan
     * @return array<int, mixed>
     */
    public function map($permintaan): array
    {
        $this->nomor++;

        return [
            $this->nomor,
            $permintaan->kode_resi,
            $permintaan->nomor_permintaan,
            $permintaan->nama_pemohon,
            $permintaan->email,
            $permintaan->npm_nik,
            ucfirst($permintaan->status),
            $permintaan->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-',
            $permintaan->pemroses?->name ?? '-',
            $permintaan->tanggal_diproses?->format('d/m/Y H:i') ?? '-',
            $permintaan->alasan_admin ?? '-',
        ];
    }
}
