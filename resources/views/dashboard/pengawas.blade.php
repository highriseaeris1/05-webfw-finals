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

                <div class="mt-4 rounded-[10px] border border-dashed border-[#DDE3EC] bg-[#F4F6FA] px-4 py-6 text-center">
                    <p class="text-sm font-medium text-[#1B2430]">Belum tersedia</p>
                    <p class="mt-1 text-xs text-[#6B7685]">Laporan rekap per periode akan dibuat pada tahap laporan.</p>
                </div>
            </aside>
        </div>
    </div>
@endsection
