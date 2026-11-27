@extends('layouts.panel')

@section('title', 'Dashboard Admin | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Dashboard admin</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Ringkasan akun staf dan aktivitas panel.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['label' => 'Total pengguna', 'value' => $totalPengguna],
                ['label' => 'Admin aktif', 'value' => $adminAktif],
                ['label' => 'Pengawas aktif', 'value' => $pengawasAktif],
            ] as $metric)
                <div class="rounded-[10px] border border-[#DDE3EC] bg-white p-3">
                    <p class="text-xs text-[#6B7685]">{{ $metric['label'] }}</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[#1B2430]">{{ $metric['value'] }}</p>
                </div>
            @endforeach
        </div>

        <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5">
            <h2 class="text-base font-semibold text-[#1B2430]">Modul pengelolaan</h2>
            <p class="mt-1 text-[13px] text-[#6B7685]">
                Menu Permintaan, Barang, Kategori, Umur barang, Riwayat, dan Pengguna sudah disiapkan di sidebar dan akan aktif pada tahap berikutnya.
            </p>
        </section>
    </div>
@endsection
