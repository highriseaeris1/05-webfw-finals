@extends('layouts.panel')

@section('title', 'Tambah Barang | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <a
                href="{{ route('admin.barang.index') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-[#1F3A5F] underline underline-offset-2"
            >
                <span aria-hidden="true">&larr;</span> Kembali ke daftar barang
            </a>
            <h1 class="mt-3 text-[22px] font-semibold leading-tight text-[#1B2430]">Tambah barang temuan</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Catat barang temuan yang diserahkan ke Tata Usaha. Status awal otomatis Tersedia.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5 sm:p-6">
                <h2 class="text-base font-semibold text-[#1B2430]">Data barang temuan</h2>
                <p class="mt-1 text-xs text-[#6B7685]">Kolom bertanda wajib harus diisi lengkap.</p>

                @if ($daftarKategori->isEmpty())
                    <div class="mt-4 rounded-[10px] border border-[#DDE3EC] bg-[#F4F6FA] p-4 text-xs text-[#1B2430]">
                        Belum ada kategori barang.
                        <a href="{{ route('admin.kategori.create') }}" class="font-medium text-[#1F3A5F] underline underline-offset-2">
                            Tambah kategori terlebih dahulu
                        </a>
                        sebelum mencatat barang temuan.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.barang.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf

                    <div>
                        <label for="kategori_id" class="block text-xs font-medium text-[#6B7685]">Kategori</label>
                        <select
                            id="kategori_id"
                            name="kategori_id"
                            required
                            @error('kategori_id') aria-invalid="true" aria-describedby="kategori_id-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('kategori_id') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach ($daftarKategori as $kat)
                                <option value="{{ $kat->id }}" @selected((string) old('kategori_id') === (string) $kat->id)>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <p id="kategori_id-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_barang" class="block text-xs font-medium text-[#6B7685]">Nama barang</label>
                        <input
                            id="nama_barang"
                            name="nama_barang"
                            type="text"
                            value="{{ old('nama_barang') }}"
                            required
                            maxlength="100"
                            placeholder="contoh: Charger laptop Lenovo"
                            @error('nama_barang') aria-invalid="true" aria-describedby="nama_barang-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('nama_barang') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('nama_barang')
                            <p id="nama_barang-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_penemu" class="block text-xs font-medium text-[#6B7685]">Nama penemu</label>
                        <input
                            id="nama_penemu"
                            name="nama_penemu"
                            type="text"
                            value="{{ old('nama_penemu') }}"
                            required
                            maxlength="100"
                            placeholder="Nama penemu atau pelapor"
                            @error('nama_penemu') aria-invalid="true" aria-describedby="nama_penemu-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('nama_penemu') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('nama_penemu')
                            <p id="nama_penemu-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_ditemukan" class="block text-xs font-medium text-[#6B7685]">Tanggal ditemukan</label>
                        <input
                            id="tanggal_ditemukan"
                            name="tanggal_ditemukan"
                            type="date"
                            value="{{ old('tanggal_ditemukan', now()->toDateString()) }}"
                            required
                            @error('tanggal_ditemukan') aria-invalid="true" aria-describedby="tanggal_ditemukan-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('tanggal_ditemukan') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('tanggal_ditemukan')
                            <p id="tanggal_ditemukan-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="foto" class="block text-xs font-medium text-[#6B7685]">Path foto (opsional sementara)</label>
                        <input
                            id="foto"
                            name="foto"
                            type="text"
                            value="{{ old('foto') }}"
                            maxlength="255"
                            placeholder="contoh: barang/contoh.jpg (unggahan berkas menyusul di W10)"
                            @error('foto') aria-invalid="true" aria-describedby="foto-help foto-error" @else aria-describedby="foto-help" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('foto') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        <p id="foto-help" class="mt-1 text-xs text-[#6B7685]">Kosongkan bila foto belum tersedia; unggah file foto akan ditambahkan pada modul W10.</p>
                        @error('foto')
                            <p id="foto-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="deskripsi" class="block text-xs font-medium text-[#6B7685]">Deskripsi dan ciri barang</label>
                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            rows="4"
                            required
                            maxlength="2000"
                            placeholder="Tuliskan warna, merek, lokasi ditemukan, dan ciri khusus barang"
                            @error('deskripsi') aria-invalid="true" aria-describedby="deskripsi-error" @enderror
                            class="mt-1 block w-full rounded-[10px] border bg-white p-3 text-sm text-[#1B2430] outline-none transition placeholder:text-[#6B7685] focus:ring-2 focus:ring-[#DDE3EC] @error('deskripsi') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p id="deskripsi-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-3 sm:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                        >
                            Simpan barang
                        </button>
                        <a
                            href="{{ route('admin.barang.index') }}"
                            class="inline-flex h-[41px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                        >
                            Batal
                        </a>
                    </div>
                </form>
            </section>

            <aside class="h-fit rounded-2xl border border-[#DDE3EC] bg-white p-5">
                <h2 class="text-base font-semibold text-[#1B2430]">Aturan pencatatan</h2>

                <ul class="mt-4 flex flex-col gap-3 text-[13px] text-[#6B7685]">
                    <li>Penemu tidak perlu memiliki akun; Admin mencatat nama penemu sesuai keterangan saat penyerahan.</li>
                    <li>Barang baru selalu berstatus <strong class="font-medium text-[#1B2430]">Tersedia</strong> agar langsung dapat dicari pemiliknya.</li>
                    <li>Tanggal ditemukan dipakai untuk menghitung umur simpan barang (batas pantau 1 tahun dan usulan pemusnahan 3 tahun).</li>
                </ul>
            </aside>
        </div>
    </div>
@endsection
