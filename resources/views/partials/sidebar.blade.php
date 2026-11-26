@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();

    $menu = $isAdmin
        ? [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Permintaan'],
            ['label' => 'Barang'],
            ['label' => 'Kategori'],
            ['label' => 'Umur barang'],
            ['label' => 'Riwayat'],
            ['label' => 'Pengguna'],
        ]
        : [
            ['label' => 'Dashboard', 'route' => 'pengawas.dashboard'],
            ['label' => 'Pemusnahan'],
            ['label' => 'Riwayat'],
            ['label' => 'Laporan'],
        ];
@endphp

<aside class="flex w-full shrink-0 flex-col gap-6 bg-[#1F3A5F] px-4 py-5 md:min-h-screen md:w-60">
    <div class="flex items-center gap-2.5">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2C4A73] text-[11px] font-semibold tracking-wide text-white">
            LF
        </div>
        <div>
            <p class="text-sm font-semibold text-white">Lost and Found</p>
            <p class="text-[11px] text-[#C9D4E5]">{{ $isAdmin ? 'Panel admin' : 'Panel pengawas' }}</p>
        </div>
    </div>

    <nav class="flex flex-1 flex-col gap-1" aria-label="Menu {{ $isAdmin ? 'admin' : 'pengawas' }}">
        @foreach ($menu as $item)
            @php
                $active = isset($item['route']) && request()->routeIs($item['route']);
            @endphp

            @if (isset($item['route']))
                <a
                    href="{{ route($item['route']) }}"
                    @if ($active) aria-current="page" @endif
                    @class([
                        'flex items-center rounded-lg px-3 py-2.5 text-[13px] transition',
                        'bg-[#2C4A73] font-medium text-white' => $active,
                        'text-[#C9D4E5] hover:bg-[#2C4A73]/60 hover:text-white' => ! $active,
                    ])
                >
                    {{ $item['label'] }}
                </a>
            @else
                <span
                    aria-disabled="true"
                    title="Segera hadir"
                    class="flex cursor-not-allowed items-center rounded-lg px-3 py-2.5 text-[13px] text-[#C9D4E5]/50"
                >
                    {{ $item['label'] }}
                </span>
            @endif
        @endforeach
    </nav>

    <div class="mt-auto flex flex-col gap-2 border-t border-white/10 pt-4">
        <div class="flex items-center gap-2 text-[13px] text-white">
            <svg class="h-4 w-4 shrink-0 text-[#C9D4E5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
            </svg>
            <span class="truncate">{{ $user->name }}</span>
        </div>

        <a href="{{ route('password.edit') }}" class="text-xs text-[#C9D4E5] underline underline-offset-2 transition hover:text-white">
            Ubah kata sandi
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-[#C9D4E5] underline underline-offset-2 transition hover:text-white">
                Keluar
            </button>
        </form>
    </div>
</aside>
