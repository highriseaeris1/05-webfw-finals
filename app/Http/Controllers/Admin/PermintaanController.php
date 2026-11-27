<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexPermintaanRequest;
use App\Models\Permintaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class PermintaanController extends Controller
{
    public function index(IndexPermintaanRequest $request): View
    {
        $filters = $request->validated();

        $permintaan = Permintaan::query()
            ->with('pemroses')
            ->when($filters['q'] ?? null, function (Builder $query, string $q): void {
                $query->where(function (Builder $query) use ($q): void {
                    $query->where('kode_resi', 'like', "%{$q}%")
                        ->orWhere('nama_pemohon', 'like', "%{$q}%");
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                $query->where('status', $status);
            })
            ->when($filters['tanggal_awal'] ?? null, function (Builder $query, string $tanggal): void {
                $query->whereDate('tanggal_pengajuan', '>=', $tanggal);
            })
            ->when($filters['tanggal_akhir'] ?? null, function (Builder $query, string $tanggal): void {
                $query->whereDate('tanggal_pengajuan', '<=', $tanggal);
            })
            ->latest('tanggal_pengajuan')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.permintaan.index', compact('permintaan'));
    }
}
