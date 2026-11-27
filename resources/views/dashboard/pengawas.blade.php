@extends('layouts.panel')

@section('title', 'Dashboard Pengawas | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Dashboard pengawas</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Pantau pengelolaan barang dan setujui usulan pemusnahan.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (['Usulan pemusnahan', 'Diambil bulan ini', 'Barang masuk bulan ini', 'Dimusnahkan tahun ini'] as $label)
                <div class="rounded-[10px] border border-[#DDE3EC] bg-white p-3">
                    <p class="text-xs text-[#6B7685]">{{ $label }}</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[#1B2430]">&mdash;</p>
                    <p class="mt-1 text-[11px] text-[#6B7685]">Belum tersedia</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="flex flex-col gap-6">
                <section class="flex flex-col gap-3">
                    <h2 class="text-base font-semibold text-[#1B2430]">Usulan pemusnahan</h2>

                    <div class="rounded-2xl border border-[#DDE3EC] bg-white px-6 py-8 text-center">
                        <p class="text-sm font-medium text-[#1B2430]">Belum ada data</p>
                        <p class="mt-1 text-xs text-[#6B7685]">Modul pemusnahan barang belum tersedia. Usulan akan muncul di sini setelah modul dibuat.</p>
                    </div>
                </section>

                <section class="flex flex-col gap-3">
                    <h2 class="text-base font-semibold text-[#1B2430]">Riwayat pengambilan (hanya lihat)</h2>

                    <div class="rounded-2xl border border-[#DDE3EC] bg-white px-6 py-8 text-center">
                        <p class="text-sm font-medium text-[#1B2430]">Belum ada data</p>
                        <p class="mt-1 text-xs text-[#6B7685]">Riwayat pengambilan akan tersedia setelah modul transaksi dibuat.</p>
                    </div>
                </section>
            </div>

            <aside class="h-fit rounded-2xl border border-[#DDE3EC] bg-white p-5">
                <h2 class="text-base font-semibold text-[#1B2430]">Laporan rekap</h2>
                <p class="mt-1 text-xs text-[#6B7685]">Pilih periode, lalu cetak atau ekspor.</p>

                <form method="GET" action="{{ route('pengawas.laporan.index') }}" class="mt-4 flex flex-col gap-3">
                    <div>
                        <label for="laporan-tanggal-awal" class="block text-xs font-medium text-[#6B7685]">Tanggal awal</label>
                        <input
                            id="laporan-tanggal-awal"
                            name="tanggal_awal"
                            type="date"
                            value="{{ now()->startOfMonth()->toDateString() }}"
                            class="mt-1 block h-[40px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                        >
                    </div>

                    <div>
                        <label for="laporan-tanggal-akhir" class="block text-xs font-medium text-[#6B7685]">Tanggal akhir</label>
                        <input
                            id="laporan-tanggal-akhir"
                            name="tanggal_akhir"
                            type="date"
                            value="{{ now()->toDateString() }}"
                            class="mt-1 block h-[40px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                        >
                    </div>

                    <button
                        type="submit"
                        class="inline-flex h-[40px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Lihat laporan
                    </button>

                    <button
                        type="submit"
                        formaction="{{ route('pengawas.laporan.pdf') }}"
                        class="inline-flex h-[40px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Cetak PDF
                    </button>

                    <button
                        type="submit"
                        formaction="{{ route('pengawas.laporan.excel') }}"
                        class="inline-flex h-[40px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Ekspor Excel
                    </button>
                </form>
            </aside>
        </div>
    </div>
@endsection
