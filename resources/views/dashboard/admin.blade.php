@extends('layouts.panel')

@section('title', 'Dashboard Admin | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Dashboard admin</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Ringkasan barang temuan, permintaan pengambilan, dan akun staf.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Barang tersedia', 'value' => $barangTersedia],
                ['label' => 'Permintaan diajukan', 'value' => $permintaanDiajukan],
                ['label' => 'Terambil bulan ini', 'value' => $terambilBulanIni],
                ['label' => 'Barang > 1 tahun', 'value' => $barangLebihSatuTahun],
            ] as $metric)
                <div class="rounded-[10px] border border-[#DDE3EC] bg-white p-3">
                    <p class="text-xs text-[#6B7685]">{{ $metric['label'] }}</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[#1B2430]">{{ $metric['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-[#1B2430]">Ringkasan transaksi permintaan</h2>
                        <p class="mt-0.5 text-xs text-[#6B7685]">Pengajuan masuk hari ini dan sebaran seluruh status.</p>
                    </div>
                    <span class="rounded-[10px] border border-[#DDE3EC] bg-[#F4F6FA] px-3 py-1.5 text-xs font-medium text-[#1B2430]">
                        Hari ini: {{ $permintaanHariIni }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
                    @foreach ([
                        ['label' => 'Total', 'value' => $totalPermintaan],
                        ['label' => 'Diajukan', 'value' => $permintaanPerStatus['diajukan']],
                        ['label' => 'Disetujui', 'value' => $permintaanPerStatus['disetujui']],
                        ['label' => 'Diambil', 'value' => $permintaanPerStatus['diambil']],
                        ['label' => 'Ditolak', 'value' => $permintaanPerStatus['ditolak']],
                    ] as $item)
                        <div class="rounded-[10px] border border-[#DDE3EC] bg-[#F4F6FA] p-3">
                            <p class="text-[11px] text-[#6B7685]">{{ $item['label'] }}</p>
                            <p class="mt-0.5 text-lg font-semibold text-[#1B2430]">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5">
                <h2 class="text-base font-semibold text-[#1B2430]">Data master dan akun staf</h2>
                <p class="mt-0.5 text-xs text-[#6B7685]">Kondisi kategori, barang temuan, dan akun pengelola panel.</p>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
                    @foreach ([
                        ['label' => 'Kategori', 'value' => $totalKategori],
                        ['label' => 'Total barang', 'value' => $totalBarang],
                        ['label' => 'Total akun', 'value' => $totalPengguna],
                        ['label' => 'Admin aktif', 'value' => $adminAktif],
                        ['label' => 'Pengawas aktif', 'value' => $pengawasAktif],
                    ] as $item)
                        <div class="rounded-[10px] border border-[#DDE3EC] bg-[#F4F6FA] p-3">
                            <p class="text-[11px] text-[#6B7685]">{{ $item['label'] }}</p>
                            <p class="mt-0.5 text-lg font-semibold text-[#1B2430]">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a
                        href="{{ route('admin.barang.index') }}"
                        class="inline-flex h-[34px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Kelola barang
                    </a>
                    <a
                        href="{{ route('admin.kategori.index') }}"
                        class="inline-flex h-[34px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Kelola kategori
                    </a>
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex h-[34px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Kelola pengguna
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
