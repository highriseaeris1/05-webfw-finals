@extends('layouts.app')

@section('title', 'Masuk | Lost and Found Fasilkom')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-[410px]">
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
                <h1 class="text-lg font-semibold text-[#1B2430]">Masuk panel staf</h1>
                <p class="mt-1 text-sm text-[#6B7685]">Khusus Admin dan Pengawas Fasilkom UNSIKA.</p>

                @if (session('status'))
                    <div class="mt-4 rounded-[10px] border border-[#B8E0C8] bg-[#E3F4EA] px-3 py-2 text-sm text-[#1E7B45]" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @error('auth')
                    <div class="mt-4 rounded-[10px] border border-[#F3C1C1] bg-[#FDE7E7] px-3 py-2 text-sm text-[#B3261E]" role="alert">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('login.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="identifier" class="block text-xs font-medium text-[#6B7685]">Email atau username</label>
                        <input
                            id="identifier"
                            name="identifier"
                            type="text"
                            value="{{ old('identifier') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan email atau username"
                            @error('identifier') aria-invalid="true" aria-describedby="identifier-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('identifier') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('identifier')
                            <p id="identifier-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-medium text-[#6B7685]">Kata sandi</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('password') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('password')
                            <p id="password-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="flex h-[41px] w-full items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Masuk
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-[#6B7685]">
                <a href="{{ url('/') }}" class="font-medium text-[#1F3A5F] underline">Kembali ke beranda</a>
            </p>
            <p class="mt-3 text-center text-xs text-[#6B7685]">2026 Fasilkom UNSIKA</p>
        </div>
    </main>
@endsection
