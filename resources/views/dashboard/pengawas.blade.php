@extends('layouts.panel')

@section('title', 'Dashboard Pengawas | Lost and Found Fasilkom')

@section('content')
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Dashboard pengawas</h1>
            <p class="mt-1 text-[13px] text-[#6B7685]">Pantau pengelolaan barang dan setujui usulan pemusnahan.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Usulan pemusnahan', 'value' => $usulanPemusnahan],
                ['label' => 'Diambil bulan ini', 'value' => $diambilBulanIni],
                ['label' => 'Barang masuk bulan ini', 'value' => $barangMasukBulanIni],
                ['label' => 'Dimusnahkan tahun ini', 'value' => $dimusnahkanTahunIni],
            ] as $metric)
                <div class="rounded-[10px] border border-[#DDE3EC] bg-white p-3">
                    <p class="text-xs text-[#6B7685]">{{ $metric['label'] }}</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[#1B2430]">{{ $metric['value'] }}</p>
                </div>
            @endforeach
        </div>

        <section class="rounded-2xl border border-[#DDE3EC] bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-semibold text-[#1B2430]">Ringkasan permintaan pengambilan</h2>
                    <p class="mt-0.5 text-xs text-[#6B7685]">Rekap transaksi seluruh status dan pengajuan hari ini.</p>
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

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="flex flex-col gap-6">
                <section class="flex flex-col gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-[#1B2430]">Kandidat pemusnahan (&gt; 3 tahun)</h2>
                        <p class="mt-0.5 text-xs text-[#6B7685]">Barang berstatus tersedia yang melewati batas simpan 3 tahun; tombol persetujuan menyusul pada tahap W6.</p>
                    </div>

                    @if ($kandidatPemusnahan->isEmpty())
                        <div class="rounded-2xl border border-[#DDE3EC] bg-white px-6 py-8 text-center">
                            <p class="text-sm font-medium text-[#1B2430]">Tidak ada kandidat pemusnahan</p>
                            <p class="mt-1 text-xs text-[#6B7685]">Belum ada barang tersedia yang berumur lebih dari 3 tahun.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-[#DDE3EC] bg-white">
                            <table class="min-w-full text-left text-[13px]">
                                <thead class="bg-[#F4F6FA] text-xs font-medium text-[#6B7685]">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5">Nama barang</th>
                                        <th scope="col" class="px-4 py-2.5">Kategori</th>
                                        <th scope="col" class="px-4 py-2.5">Tanggal ditemukan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#DDE3EC]">
                                    @foreach ($kandidatPemusnahan as $item)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-[#1B2430]">{{ $item->nama_barang }}</td>
                                            <td class="px-4 py-3 text-[#6B7685]">{{ $item->kategori?->nama_kategori ?? '—' }}</td>
                                            <td class="px-4 py-3 text-[#1B2430]">{{ $item->tanggal_ditemukan->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <section class="flex flex-col gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-[#1B2430]">Riwayat pengambilan (hanya lihat)</h2>
                        <p class="mt-0.5 text-xs text-[#6B7685]">Lima permintaan terbaru yang telah diserahkan kepada pemohon.</p>
                    </div>

                    @if ($riwayatDiambilTerbaru->isEmpty())
                        <div class="rounded-2xl border border-[#DDE3EC] bg-white px-6 py-8 text-center">
                            <p class="text-sm font-medium text-[#1B2430]">Belum ada pengambilan selesai</p>
                            <p class="mt-1 text-xs text-[#6B7685]">Permintaan berstatus diambil akan tampil di sini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-[#DDE3EC] bg-white">
                            <table class="min-w-full text-left text-[13px]">
                                <thead class="bg-[#F4F6FA] text-xs font-medium text-[#6B7685]">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5">Kode resi</th>
                                        <th scope="col" class="px-4 py-2.5">Pemohon</th>
                                        <th scope="col" class="px-4 py-2.5">Tanggal diambil</th>
                                        <th scope="col" class="px-4 py-2.5">Diproses oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#DDE3EC]">
                                    @foreach ($riwayatDiambilTerbaru as $riwayat)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-[#1B2430]">{{ $riwayat->kode_resi }}</td>
                                            <td class="px-4 py-3 text-[#1B2430]">{{ $riwayat->nama_pemohon }}</td>
                                            <td class="px-4 py-3 text-[#6B7685]">{{ $riwayat->tanggal_diproses?->format('d M Y H:i') ?? '—' }}</td>
                                            <td class="px-4 py-3 text-[#6B7685]">{{ $riwayat->pemroses?->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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
