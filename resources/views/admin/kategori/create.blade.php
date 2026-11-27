@extends('layouts.panel')

@section('title', 'Tambah Kategori | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <a
                href="{{ route('admin.kategori.index') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-[#1F3A5F] underline underline-offset-2"
            >
                <span aria-hidden="true">&larr;</span> Kembali ke daftar kategori
            </a>
            <h1 class="mt-3 text-[22px] font-semibold leading-tight text-[#1B2430]">Tambah kategori</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Tambahkan kategori baru untuk mengelompokkan barang temuan.</p>
        </div>

        <section class="max-w-xl rounded-2xl border border-[#DDE3EC] bg-white p-5 sm:p-6">
            <h2 class="text-base font-semibold text-[#1B2430]">Data kategori</h2>
            <p class="mt-1 text-xs text-[#6B7685]">Nama kategori wajib diisi dan harus unik.</p>

            <form method="POST" action="{{ route('admin.kategori.store') }}" class="mt-5 flex flex-col gap-4">
                @csrf

                <div>
                    <label for="nama_kategori" class="block text-xs font-medium text-[#6B7685]">Nama kategori</label>
                    <input
                        id="nama_kategori"
                        name="nama_kategori"
                        type="text"
                        value="{{ old('nama_kategori') }}"
                        required
                        autofocus
                        maxlength="50"
                        placeholder="contoh: Elektronik"
                        @error('nama_kategori') aria-invalid="true" aria-describedby="nama_kategori-error" @enderror
                        class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('nama_kategori') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                    >
                    @error('nama_kategori')
                        <p id="nama_kategori-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Simpan kategori
                    </button>
                    <a
                        href="{{ route('admin.kategori.index') }}"
                        class="inline-flex h-[41px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
