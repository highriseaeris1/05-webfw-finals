<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TransisiStatusTidakSahException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetujuiPermintaanRequest;
use App\Http\Requests\TandaiDiambilRequest;
use App\Http\Requests\TolakPermintaanRequest;
use App\Models\Permintaan;
use App\Services\StatusPermintaanService;
use Closure;
use Illuminate\Http\RedirectResponse;

class PermintaanStatusController extends Controller
{
    public function __construct(private readonly StatusPermintaanService $statusService) {}

    public function setujui(SetujuiPermintaanRequest $request, Permintaan $permintaan): RedirectResponse
    {
        return $this->jalankan(function () use ($request, $permintaan): void {
            $this->statusService->setujui($permintaan, $request->user(), $request->validated('alasan_admin'));
        }, "Permintaan {$permintaan->kode_resi} disetujui.");
    }

    public function tolak(TolakPermintaanRequest $request, Permintaan $permintaan): RedirectResponse
    {
        return $this->jalankan(function () use ($request, $permintaan): void {
            $this->statusService->tolak($permintaan, $request->user(), $request->validated('alasan_admin'));
        }, "Permintaan {$permintaan->kode_resi} ditolak.");
    }

    public function tandaiDiambil(TandaiDiambilRequest $request, Permintaan $permintaan): RedirectResponse
    {
        return $this->jalankan(function () use ($request, $permintaan): void {
            $this->statusService->tandaiDiambil($permintaan, $request->user());
        }, "Permintaan {$permintaan->kode_resi} ditandai sudah diambil.");
    }

    private function jalankan(Closure $aksi, string $pesan): RedirectResponse
    {
        try {
            $aksi();
        } catch (TransisiStatusTidakSahException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', $pesan);
    }
}
