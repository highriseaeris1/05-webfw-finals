<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Http\Requests\Admin\UpdateKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori beserta jumlah barang dan pencarian nama.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $kategori = Kategori::query()
            ->withCount('barang')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.index', [
            'kategori' => $kategori,
            'search' => $search,
            'totalKategori' => Kategori::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah kategori.
     */
    public function create(): View
    {
        return view('admin.kategori.create');
    }

    /**
     * Simpan kategori baru ke basis data.
     */
    public function store(StoreKategoriRequest $request): RedirectResponse
    {
        Kategori::create([
            'nama_kategori' => $request->validated('nama_kategori'),
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit kategori.
     */
    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.edit', [
            'kategori' => $kategori,
        ]);
    }

    /**
     * Perbarui nama kategori.
     */
    public function update(UpdateKategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update([
            'nama_kategori' => $request->validated('nama_kategori'),
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori jika belum dipakai oleh data barang mana pun.
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        $hasil = DB::transaction(function () use ($kategori): string {
            // Kunci baris kategori agar tidak terjadi penghapusan bersamaan dengan input barang baru.
            $terkunci = Kategori::query()
                ->whereKey($kategori->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($terkunci->barang()->exists()) {
                return 'masih-dipakai';
            }

            $terkunci->delete();

            return 'berhasil';
        });

        if ($hasil === 'masih-dipakai') {
            return redirect()
                ->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data barang.');
        }

        return redirect()
            ->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
