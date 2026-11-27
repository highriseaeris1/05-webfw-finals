<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna dengan pencarian nama, username, atau email.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                // Kelompokkan kondisi pencarian agar tidak bercampur dengan filter lain.
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'totalPengguna' => User::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah pengguna.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => UserRole::cases(),
            'defaultRole' => UserRole::Pengawas->value,
        ]);
    }

    /**
     * Simpan akun baru yang langsung berstatus aktif.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->validated('name'),
            'username' => $request->validated('username'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => $request->validated('role'),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit dengan data akun yang dipilih.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'defaultRole' => $user->role->value,
        ]);
    }

    /**
     * Perbarui data akun tanpa mengubah kata sandi dan status aktif.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'name' => $request->validated('name'),
            'username' => $request->validated('username'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan akun tanpa menghapus data pengguna.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        $hasil = DB::transaction(function () use ($actor, $user): string {
            // Kunci semua Admin aktif dengan urutan tetap agar jumlah Admin aktif
            // tetap konsisten ketika ada dua permintaan status secara bersamaan.
            $adminAktif = User::query()
                ->where('role', UserRole::Admin->value)
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // Kunci baris target sebelum mengubah statusnya agar nilainya tidak berubah di tengah proses.
            $target = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->is_active && $target->is($actor)) {
                return 'akun-sendiri';
            }

            if ($target->is_active && $target->role === UserRole::Admin && $adminAktif->count() <= 1) {
                return 'admin-terakhir';
            }

            $target->update(['is_active' => ! $target->is_active]);

            return 'berhasil';
        });

        if ($hasil === 'akun-sendiri') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($hasil === 'admin-terakhir') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Minimal harus ada satu Admin yang aktif.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Status akun berhasil diperbarui.');
    }
}
