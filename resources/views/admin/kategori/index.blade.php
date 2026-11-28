@extends('layouts.panel')

@section('title', 'Kelola Kategori | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Kelola kategori</h1>
                <p class="mt-1 text-[13px] text-[#6B7685]">
                    @if ($search !== '')
                        {{ $kategori->total() }} hasil pencarian untuk "{{ $search }}".
                    @else
                        {{ $totalKategori }} kategori barang temuan terdaftar.
                    @endif
                </p>
            </div>

            <a
                href="{{ route('admin.kategori.create') }}"
                class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
            >
                Tambah Kategori
            </a>
        </header>

        <form method="GET" action="{{ route('admin.kategori.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="w-full sm:max-w-sm">
                <label for="q" class="sr-only">Cari kategori</label>
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari nama kategori"
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
                        href="{{ route('admin.kategori.index') }}"
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
                        <th scope="col" class="px-5 py-2.5">Nama kategori</th>
                        <th scope="col" class="px-5 py-2.5">Jumlah barang</th>
                        <th scope="col" class="px-5 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DDE3EC]">
                    @forelse ($kategori as $item)
                        <tr>
                            <td class="px-5 py-3.5 font-semibold text-[#1B2430]">{{ $item->nama_kategori }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->barang_count }} barang</td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a
                                        href="{{ route('admin.kategori.edit', $item) }}"
                                        class="inline-flex h-[30px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.kategori.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus kategori ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex h-[30px] items-center justify-center rounded-[8px] border border-[#F3C1C1] bg-[#FDE7E7] px-3 text-xs font-medium text-[#B3261E] transition hover:bg-[#FBD5D5] focus:outline-none focus:ring-2 focus:ring-[#B3261E]"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-[#6B7685]">
                                Belum ada kategori yang terdaftar atau cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategori->hasPages())
            <div>
                {{ $kategori->links() }}
            </div>
        @endif

        <p class="text-xs text-[#6B7685]">
            Kategori yang masih digunakan oleh data barang tidak dapat dihapus agar referensi data tetap terjaga.
        </p>
    </div>
@endsection
