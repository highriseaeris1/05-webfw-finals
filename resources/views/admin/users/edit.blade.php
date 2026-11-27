@extends('layouts.panel')

@section('title', 'Edit Pengguna | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-[#1F3A5F] underline underline-offset-2"
            >
                <span aria-hidden="true">&larr;</span> Kembali ke daftar pengguna
            </a>
            <h1 class="mt-3 text-[22px] font-semibold leading-tight text-[#1B2430]">Edit pengguna</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Perbarui data akun Admin atau Pengawas.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5 sm:p-6">
                <h2 class="text-base font-semibold text-[#1B2430]">Data akun</h2>
                <p class="mt-1 text-xs text-[#6B7685]">Hanya nama, username, email, dan peran yang dapat diubah.</p>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-medium text-[#6B7685]">Nama lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama lengkap"
                            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('name') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('name')
                            <p id="name-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-xs font-medium text-[#6B7685]">Username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username', $user->username) }}"
                            required
                            autocomplete="username"
                            placeholder="contoh: pengawas2"
                            @error('username') aria-invalid="true" aria-describedby="username-help username-error" @else aria-describedby="username-help" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('username') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        <p id="username-help" class="mt-1 text-xs text-[#6B7685]">Username harus unik.</p>
                        @error('username')
                            <p id="username-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-[#6B7685]">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="email"
                            placeholder="nama@email.com"
                            @error('email') aria-invalid="true" aria-describedby="email-help email-error" @else aria-describedby="email-help" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('email') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        <p id="email-help" class="mt-1 text-xs text-[#6B7685]">Email harus unik.</p>
                        @error('email')
                            <p id="email-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-medium text-[#6B7685]">Peran</label>
                        <select
                            id="role"
                            name="role"
                            required
                            @error('role') aria-invalid="true" aria-describedby="role-help role-error" @else aria-describedby="role-help" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('role') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $defaultRole) === $role->value)>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        <p id="role-help" class="mt-1 text-xs text-[#6B7685]">Menentukan menu dan hak akses akun.</p>
                        @error('role')
                            <p id="role-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-3 sm:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                        >
                            Simpan perubahan
                        </button>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex h-[41px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                        >
                            Batal
                        </a>
                    </div>
                </form>
            </section>

            <aside class="h-fit rounded-2xl border border-[#DDE3EC] bg-white p-5">
                <h2 class="text-base font-semibold text-[#1B2430]">Perbedaan peran</h2>

                <div class="mt-4 flex flex-col gap-4 text-[13px]">
                    <div>
                        <p class="font-semibold text-[#1B2430]">Admin</p>
                        <p class="mt-1 text-[#6B7685]">
                            Mengelola barang, kategori, permintaan pengambilan, umur barang, dan pengguna.
                        </p>
                    </div>

                    <div>
                        <p class="font-semibold text-[#1B2430]">Pengawas</p>
                        <p class="mt-1 text-[#6B7685]">
                            Memantau dashboard dan riwayat pengambilan, menyetujui pemusnahan barang, dan membuat laporan rekap.
                        </p>
                    </div>
                </div>

                <p class="mt-4 border-t border-[#DDE3EC] pt-4 text-xs text-[#6B7685]">
                    Kata sandi dan status aktif tidak diubah dari halaman ini. Akun juga tidak dihapus agar riwayat tetap tersimpan.
                </p>
            </aside>
        </div>
    </div>
@endsection
