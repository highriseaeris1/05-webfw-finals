@extends('layouts.panel')

@section('title', 'Kelola Pengguna | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Kelola pengguna</h1>
                <p class="mt-1 text-[13px] text-[#6B7685]">
                    @if ($search !== '')
                        {{ $users->total() }} hasil pencarian untuk "{{ $search }}".
                    @else
                        {{ $totalPengguna }} akun terdaftar: Admin dan Pengawas.
                    @endif
                </p>
            </div>

            <button
                type="button"
                disabled
                title="Fitur tambah pengguna akan tersedia pada tahap berikutnya"
                class="inline-flex h-[41px] cursor-not-allowed items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white opacity-60"
            >
                Tambah Pengguna
            </button>
        </header>

        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="w-full sm:max-w-sm">
                <label for="q" class="sr-only">Cari pengguna</label>
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, username, atau email"
                    class="h-[42px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                >
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="inline-flex h-[42px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Cari
                </button>

                @if ($search !== '')
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex h-[42px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-[#DDE3EC] bg-white">
            <table class="min-w-full text-left text-[13px]">
                <thead class="bg-[#F4F6FA] text-xs font-medium text-[#6B7685]">
                    <tr>
                        <th scope="col" class="px-5 py-2.5">Nama</th>
                        <th scope="col" class="px-5 py-2.5">Username</th>
                        <th scope="col" class="px-5 py-2.5">Email</th>
                        <th scope="col" class="px-5 py-2.5">Peran</th>
                        <th scope="col" class="px-5 py-2.5">Status</th>
                        <th scope="col" class="px-5 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DDE3EC]">
                    @forelse ($users as $managedUser)
                        <tr>
                            <td class="px-5 py-3.5 font-semibold text-[#1B2430]">{{ $managedUser->name }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $managedUser->username ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $managedUser->email }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $managedUser->role->label() }}</td>
                            <td class="px-5 py-3.5">
                                @if ($managedUser->is_active)
                                    <x-badge variant="success">Aktif</x-badge>
                                @else
                                    <x-badge variant="danger">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-[#6B7685]">
                                @if ($managedUser->is(auth()->user()))
                                    Akun Anda
                                @else
                                    &mdash;
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-[#6B7685]">
                                Tidak ada pengguna yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div>
                {{ $users->links() }}
            </div>
        @endif

        <p class="text-xs text-[#6B7685]">
            Akun tidak dihapus agar riwayat tetap tersimpan. Fitur ubah dan aktif/nonaktifkan akun akan tersedia pada tahap berikutnya.
        </p>
    </div>
@endsection
