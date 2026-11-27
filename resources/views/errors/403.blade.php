@extends('layouts.app')

@section('title', 'Akses Ditolak | Lost and Found Fasilkom')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-[460px]">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#1F3A5F] text-xs font-semibold tracking-wide text-white">
                    LF
                </div>
                <div>
                    <p class="text-base font-semibold text-[#1B2430]">Lost and Found Fasilkom</p>
                    <p class="text-xs text-[#6B7685]">Panel staf</p>
                </div>
            </div>

            <div class="rounded-2xl border border-[#DDE3EC] bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#B3261E]">Error 403</p>
                <h1 class="mt-2 text-lg font-semibold text-[#1B2430]">Akses ditolak</h1>
                <p class="mt-1 text-sm text-[#6B7685]">
                    Anda tidak memiliki izin untuk membuka halaman ini. Halaman khusus Admin hanya dapat diakses Admin, dan halaman khusus Pengawas hanya dapat diakses Pengawas.
                </p>

                <a
                    href="{{ route('dashboard') }}"
                    class="mt-5 flex h-[41px] w-full items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                >
                    Kembali ke panel
                </a>
            </div>

            <p class="mt-6 text-center text-xs text-[#6B7685]">2026 Fasilkom UNSIKA</p>
        </div>
    </main>
@endsection
