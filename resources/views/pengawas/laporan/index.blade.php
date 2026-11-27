@extends('layouts.panel')

@section('title', 'Laporan Rekap | Lost and Found Fasilkom')

@section('content')
    @php
        $periode = \Illuminate\Support\Carbon::parse($tanggalAwal)->format('d/m/Y')
            .' s.d. '.
            \Illuminate\Support\Carbon::parse($tanggalAkhir)->format('d/m/Y');

        $tautanEkspor = [
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
        ];
    @endphp

    <div class="flex flex-col gap-6">
        <header class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-[22px] font-semibold leading-tight text-[#1B2430]">Laporan rekap</h1>
                <p class="mt-1 text-[13px] text-[#6B7685]">
                    Rekap permintaan pengambilan barang pada periode {{ $periode }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('pengawas.laporan.pdf', $tautanEkspor) }}"
                    class="inline-flex h-[41px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Cetak PDF
                </a>
                <a
                    href="{{ route('pengawas.laporan.excel', $tautanEkspor) }}"
                    class="inline-flex h-[41px] items-center justify-center rounded-[10px] border border-[#DDE3EC] bg-white px-4 text-sm font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Ekspor Excel
                </a>
            </div>
        </header>

        <form
            method="GET"
            action="{{ route('pengawas.laporan.index') }}"
            class="rounded-2xl border border-[#DDE3EC] bg-white p-4 sm:p-5"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                <div>
                    <label for="tanggal_awal" class="block text-xs font-medium text-[#6B7685]">Tanggal awal</label>
                    <input
                        id="tanggal_awal"
                        name="tanggal_awal"
                        type="date"
                        value="{{ $tanggalAwal }}"
                        class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] sm:w-[180px] @error('tanggal_awal') border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                    >
                    @error('tanggal_awal')
                        <p class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_akhir" class="block text-xs font-medium text-[#6B7685]">Tanggal akhir</label>
                    <input
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        type="date"
                        value="{{ $tanggalAkhir }}"
                        class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] sm:w-[180px] @error('tanggal_akhir') border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                    >
                    @error('tanggal_akhir')
                        <p class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex h-[42px] items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Tampilkan
                </button>
            </div>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Total permintaan', 'value' => $ringkasan['total']],
                ['label' => 'Diajukan', 'value' => $ringkasan['diajukan']],
                ['label' => 'Disetujui', 'value' => $ringkasan['disetujui']],
                ['label' => 'Diambil', 'value' => $ringkasan['diambil']],
                ['label' => 'Ditolak', 'value' => $ringkasan['ditolak']],
            ] as $kartu)
                <div class="rounded-[10px] border border-[#DDE3EC] bg-white p-3">
                    <p class="text-xs text-[#6B7685]">{{ $kartu['label'] }}</p>
                    <p class="mt-0.5 text-2xl font-semibold text-[#1B2430]">{{ $kartu['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="overflow-x-auto rounded-xl border border-[#DDE3EC] bg-white">
            <table class="min-w-full text-left text-[13px]">
                <thead class="bg-[#F4F6FA] text-xs font-medium text-[#6B7685]">
                    <tr>
                        <th scope="col" class="px-5 py-2.5">Kode resi</th>
                        <th scope="col" class="px-5 py-2.5">No. permintaan</th>
                        <th scope="col" class="px-5 py-2.5">Pemohon</th>
                        <th scope="col" class="px-5 py-2.5">NPM/NIK</th>
                        <th scope="col" class="px-5 py-2.5">Tanggal pengajuan</th>
                        <th scope="col" class="px-5 py-2.5">Status</th>
                        <th scope="col" class="px-5 py-2.5">Diproses oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DDE3EC]">
                    @forelse ($permintaan as $item)
                        <tr>
                            <td class="px-5 py-3.5 font-semibold text-[#1B2430]">{{ $item->kode_resi }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->nomor_permintaan }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->nama_pemohon }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->npm_nik }}</td>
                            <td class="px-5 py-3.5 text-[#1B2430]">
                                {{ $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <x-badge :variant="$item->status->badgeVariant()">
                                    {{ $item->status->label() }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3.5 text-[#1B2430]">{{ $item->pemroses?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-[#6B7685]">
                                Tidak ada permintaan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($permintaan->hasPages())
            <div>
                {{ $permintaan->links() }}
            </div>
        @endif

        <p class="text-xs text-[#6B7685]">
            Menampilkan {{ $permintaan->total() }} permintaan. Data diambil berdasarkan tanggal pengajuan.
        </p>
    </div>
@endsection
