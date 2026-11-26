@extends('layouts.app')

@section('title', 'Panel Staf | Lost and Found Fasilkom')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl rounded-2xl border border-[#DDE3EC] bg-white p-6 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-[#6B7685]">Panel staf</p>
            <h1 class="mt-1 text-xl font-semibold text-[#1B2430]">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-[#6B7685]">
                Anda masuk sebagai <span class="font-medium text-[#1F3A5F]">{{ auth()->user()->role->label() }}</span>.
            </p>

            <div class="mt-5 rounded-[10px] border border-[#DDE3EC] bg-[#F4F6FA] px-4 py-3 text-sm text-[#6B7685]">
                Halaman ini sementara untuk memastikan login berhasil. Menu dan dashboard per peran akan dibuat pada tahap berikutnya.
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-[#DDE3EC] pt-4">
                <p class="text-xs text-[#6B7685]">Kelola akun staf Anda.</p>
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('password.edit') }}"
                        class="flex h-[36px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA]"
                    >
                        Ubah kata sandi
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex h-[36px] items-center justify-center rounded-[8px] border border-[#DDE3EC] bg-white px-3 text-xs font-medium text-[#1F3A5F] transition hover:border-[#1F3A5F] hover:bg-[#F4F6FA] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                        >
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
