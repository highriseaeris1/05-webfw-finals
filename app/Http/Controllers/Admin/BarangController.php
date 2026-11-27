<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusBarang;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBarangRequest;
use App\Http\Requests\Admin\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar barang temuan dengan pencarian serta filter kategori dan status.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $kategoriId = $request->query('kategori_id');
        $statusInput = trim((string) $request->query('status', ''));

        $statusValid = StatusBarang::tryFrom($statusInput)?->value ?? '';
        $kategoriValid = is_numeric($kategoriId) ? (int) $kategoriId : null;

        $barang = Barang::query()
            ->with('kategori')
            ->when($search !== '', function ($query) use ($search): void {
                // Kelompokkan pencarian teks agar tidak mengganggu filter kategori atau status.
                $query->where(function ($query) use ($search): void {
                    $query->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('nama_penemu', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($kategoriValid !== null, function ($query) use ($kategoriValid): void {
                $query->where('kategori_id', $kategoriValid);
            })
            ->when($statusValid !== '', function ($query) use ($statusValid): void {
                $query->where('status', $statusValid);
            })
            ->latest('tanggal_ditemukan')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang.index', [
            'barang' => $barang,
            'daftarKategori' => Kategori::query()->orderBy('nama_kategori')->get(),
            'daftarStatus' => StatusBarang::cases(),
            'filters' => [
                'q' => $search,
                'kategori_id' => $kategoriValid,
                'status' => $statusValid,
            ],
            'totalBarang' => Barang::count(),
        ]);
    }

    /**
     * Tampilkan formulir pencatatan barang temuan baru.
     */
    public function create(): View
    {
        return view('admin.barang.create', [
            'daftarKategori' => Kategori::query()->orderBy('nama_kategori')->get(),
        ]);
    }

    /**
     * Simpan barang temuan baru dengan status awal tersedia.
     */
    public function store(StoreBarangRequest $request): RedirectResponse
    {
        Barang::create([
            ...$request->validated(),
            'status' => StatusBarang::Tersedia->value,
        ]);

        return redirect()
            ->route('admin.barang.index')
            ->with('status', 'Barang temuan berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit data barang temuan.
     */
    public function edit(Barang $barang): View
    {
        return view('admin.barang.edit', [
            'barang' => $barang,
            'daftarKategori' => Kategori::query()->orderBy('nama_kategori')->get(),
        ]);
    }

    /**
     * Perbarui data master barang tanpa mengubah status transaksi.
     */
    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $barang->update($request->validated());

        return redirect()
            ->route('admin.barang.index')
            ->with('status', 'Data barang berhasil diperbarui.');
    }

    /**
     * Hapus barang hanya jika masih berstatus tersedia dan belum masuk proses klaim/riwayat.
     */
    public function destroy(Barang $barang): RedirectResponse
    {
        $hasil = DB::transaction(function () use ($barang): string {
            // Kunci baris barang agar tidak bentrok dengan pengajuan klaim yang masuk bersamaan.
            $terkunci = Barang::query()
                ->whereKey($barang->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // TODO (W5/W6): Tambahkan pengecekan relasi detail_permintaan dan riwayat_pengambilan sebelum menghapus barang.
            if ($terkunci->status !== StatusBarang::Tersedia) {
                return 'status-terkunci';
            }

            $terkunci->delete();

            return 'berhasil';
        });

        if ($hasil === 'status-terkunci') {
            return redirect()
                ->route('admin.barang.index')
                ->with('error', 'Barang yang sedang diajukan, sudah diambil, atau dimusnahkan tidak dapat dihapus.');
        }

        return redirect()
            ->route('admin.barang.index')
            ->with('status', 'Data barang berhasil dihapus.');
    }
}
