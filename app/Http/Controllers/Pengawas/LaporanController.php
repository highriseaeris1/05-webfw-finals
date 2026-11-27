<?php

namespace App\Http\Controllers\Pengawas;

use App\Exports\LaporanPermintaanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pengawas\ExportLaporanRequest;
use App\Http\Requests\Pengawas\IndexLaporanRequest;
use App\Services\LaporanPermintaanService;
use Dompdf\Dompdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function __construct(private readonly LaporanPermintaanService $laporan) {}

    /**
     * Tampilkan rekap permintaan pengambilan pada periode yang dipilih.
     */
    public function index(IndexLaporanRequest $request): View
    {
        [$tanggalAwal, $tanggalAkhir] = $this->periode(
            $request->validated('tanggal_awal'),
            $request->validated('tanggal_akhir'),
        );

        return view('pengawas.laporan.index', [
            'ringkasan' => $this->laporan->ringkasan($tanggalAwal, $tanggalAkhir),
            'permintaan' => $this->laporan->daftar($tanggalAwal, $tanggalAkhir),
            'tanggalAwal' => $tanggalAwal->toDateString(),
            'tanggalAkhir' => $tanggalAkhir->toDateString(),
        ]);
    }

    /**
     * Cetak laporan rekap sebagai berkas PDF.
     */
    public function pdf(ExportLaporanRequest $request): Response
    {
        [$tanggalAwal, $tanggalAkhir] = $this->periode(
            $request->validated('tanggal_awal'),
            $request->validated('tanggal_akhir'),
        );

        $pdf = new Dompdf(['isRemoteEnabled' => false]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadHtml(view('pengawas.laporan.pdf', [
            'ringkasan' => $this->laporan->ringkasan($tanggalAwal, $tanggalAkhir),
            'permintaan' => $this->laporan->semua($tanggalAwal, $tanggalAkhir),
            'tanggalAwal' => $tanggalAwal->toDateString(),
            'tanggalAkhir' => $tanggalAkhir->toDateString(),
            'dicetakOleh' => $request->user()->name,
            'dicetakPada' => Carbon::now(),
        ])->render());
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->namaBerkas($tanggalAwal, $tanggalAkhir, 'pdf').'"',
        ]);
    }

    /**
     * Ekspor laporan rekap sebagai berkas Excel.
     */
    public function excel(ExportLaporanRequest $request): BinaryFileResponse
    {
        [$tanggalAwal, $tanggalAkhir] = $this->periode(
            $request->validated('tanggal_awal'),
            $request->validated('tanggal_akhir'),
        );

        return Excel::download(
            new LaporanPermintaanExport($this->laporan->semua($tanggalAwal, $tanggalAkhir)),
            $this->namaBerkas($tanggalAwal, $tanggalAkhir, 'xlsx'),
        );
    }

    /**
     * Gunakan bulan berjalan sebagai periode awal bila pengguna belum memilih tanggal.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periode(?string $tanggalAwal, ?string $tanggalAkhir): array
    {
        $awal = $tanggalAwal ? Carbon::parse($tanggalAwal) : Carbon::now()->startOfMonth();
        $akhir = $tanggalAkhir ? Carbon::parse($tanggalAkhir) : Carbon::now();

        return [$awal->startOfDay(), $akhir->endOfDay()];
    }

    /**
     * Susun nama berkas agar periode laporan langsung terlihat.
     */
    private function namaBerkas(Carbon $tanggalAwal, Carbon $tanggalAkhir, string $ekstensi): string
    {
        return 'laporan-permintaan-'.$tanggalAwal->format('Ymd').'-'.$tanggalAkhir->format('Ymd').'.'.$ekstensi;
    }
}
