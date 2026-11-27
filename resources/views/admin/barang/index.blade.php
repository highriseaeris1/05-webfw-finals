@extends('layouts.panel')

@section('title', 'Kelola Barang | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Kelola barang temuan</h1>
                <p class="mt-1 text-[13px] text-[#6B7685]">
                    @if ($filters['q'] !== '' || $filters['kategori_id'] !== null || $filters['status'] !== '')
                        Menampilkan {{ $barang->total() }} barang sesuai filter.
                    @else
                        {{ $totalBarang }} barang temuan tercatat di Tata Usaha.
                    @endif
                </p>
            </div>

            <a
                href="{{ route('admin.barang.create') }}"
                class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
            >
                Tambah Barang
            </a>
        </header>

        <form method="GET" action="{{ route('admin.barang.index') }}" class="grid gap-3 rounded-2xl border border-[#DDE3EC] bg-white p-4 sm:grid-cols-[minmax(0,1fr)_200px_180px_auto]">
            <div>
                <label for="q" class="block text-xs font-medium text-[#6B7685]">Kata kunci</label>
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $filters['q'] }}"
                    placeholder="Cari nama barang, penemu, atau ciri"
                    class="mt-1 h-[40px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                >
            </div>

            <div>
                <label for="kategori_id" class="block text-xs font-medium text-[#6B7685]">Kategori</label>
                <select
                    id="kategori_id"
                    name="kategori_id"
                    class="mt-1 h-[40px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                >
                    <option value="">Semua kategori</option>
                    @foreach ($daftarKategori as $kat)
                        <option value="{{ $kat->id }}" @selected($filters['kategori_id'] === $kat->id)>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs font-medium text-[#6B7685]">Status</label>
                <select
                    id="status"
                    name="status"
                    class="mt-1 h-[40px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                >
                    <option value="">Semua status</option>
                    @foreach ($daftarStatus as $statusItem)
                        <option value="{{ $statusItem->value }}" @selected($filters['status'] === $statusItem->value)>
                            {{ $statusItem->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="inline-flex h-[40px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Filter
                </button>

                @if ($filters['q'] !== '' || $filters['kategori_id'] !== null || $filters['status'] !== '')
                    <a
                        href="{{ route('admin.barang.index') }}"
                        class="inline-flex h-[40px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
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
                        <th scope="col" class="px-5 py-2.5">Barang</th>
                        <th scope="col" class="px-5 py-2.5">Kategori</th>
                        <th scope="col" class="px-5 py-2.5">Penemu</th>
                        <th scope="col" class="px-5 py-2.5">Tanggal ditemukan</th>
                        <th scope="col" class="px-5 py-2.5">Status</th>
                        <th scope="col" class="px-5 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DDE3EC]">
                    @forelse ($barang as $item)
                        <tr>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-[#1B2430]">{{ $item->nama_barang }}</p>
                                <p class="mt-0.5 line-clamp-1 text-xs text-[#6B7685]">{{ $item->deskripsi }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->kategori?->nama_kategori ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->nama_penemu }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->tanggal_ditemukan->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <x-badge :variant="$item->status->badgeVariant()">{{ $item->status->label() }}</x-badge>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a
                                        href="{{ route('admin.barang.edit', $item) }}"
                                        class="inline-flex h-[30px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                                    >
                                        Edit
                                    </a>

                                    @if ($item->status === \App\Enums\StatusBarang::Tersedia)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.barang.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus data barang ini?')"
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
                                    @else
                                        <span class="text-xs text-[#6B7685]">Terkunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-[#6B7685]">
                                Belum ada barang temuan yang tercatat atau cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($barang->hasPages())
            <div>
                {{ $barang->links() }}
            </div>
        @endif

        <p class="text-xs text-[#6B7685]">
            Hanya barang berstatus Tersedia yang dapat dihapus. Barang yang sedang diajukan, sudah diambil, atau dimusnahkan tetap disimpan sebagai arsip.
        </p>
    </div>
@endsection
