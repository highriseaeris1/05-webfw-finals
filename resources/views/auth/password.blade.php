@extends('layouts.app')

@section('title', 'Ubah Kata Sandi | Lost and Found Fasilkom')

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
                <h1 class="text-lg font-semibold text-[#1B2430]">Ubah kata sandi</h1>
                <p class="mt-1 text-sm text-[#6B7685]">Masukkan kata sandi lama, lalu tentukan kata sandi baru.</p>

                @if (session('status'))
                    <div class="mt-4 rounded-[10px] border border-[#B8E0C8] bg-[#E3F4EA] px-3 py-2 text-sm text-[#1E7B45]" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-medium text-[#6B7685]">Kata sandi lama</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            required
                            autofocus
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi lama"
                            @error('current_password') aria-invalid="true" aria-describedby="current-password-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('current_password') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        @error('current_password')
                            <p id="current-password-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-medium text-[#6B7685]">Kata sandi baru</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Masukkan kata sandi baru"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            class="mt-1 block h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:ring-2 focus:ring-[#DDE3EC] @error('password') border-[#B3261E] focus:border-[#B3261E] @else border-[#DDE3EC] focus:border-[#1F3A5F] @enderror"
                        >
                        <p class="mt-1 text-xs text-[#6B7685]">Minimal 8 karakter.</p>
                        @error('password')
                            <p id="password-error" class="mt-1 text-xs text-[#B3261E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-[#6B7685]">Konfirmasi kata sandi baru</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi baru"
                            class="mt-1 block h-[42px] w-full rounded-[10px] border border-[#DDE3EC] bg-white px-3 text-sm text-[#1B2430] outline-none transition focus:border-[#1F3A5F] focus:ring-2 focus:ring-[#DDE3EC]"
                        >
                    </div>

                    <button
                        type="submit"
                        class="flex h-[41px] w-full items-center justify-center rounded-[10px] bg-[#1F3A5F] px-4 text-sm font-medium text-white transition hover:bg-[#2C4A73] focus:outline-none focus:ring-2 focus:ring-[#1F3A5F]"
                    >
                        Simpan kata sandi
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-[#6B7685]">
                <a href="{{ route('dashboard') }}" class="font-medium text-[#1F3A5F] underline">Kembali ke panel</a>
            </p>
            <p class="mt-3 text-center text-xs text-[#6B7685]">2026 Fasilkom UNSIKA</p>
        </div>
    </main>
@endsection
