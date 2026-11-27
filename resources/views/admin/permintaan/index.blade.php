<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Permintaan - Lost and Found Fasilkom</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            background: #f4f6fa;
            color: #1b2430;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }
        h1 { margin: 0 0 4px; font-size: 24px; }
        .subtitle { margin: 0 0 24px; color: #5b6778; }
        .flash {
            margin-bottom: 16px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
        }
        .flash.success { background: #e3f4ea; border: 1px solid #b8e0c8; color: #1e7b45; }
        .flash.error { background: #fde7e7; border: 1px solid #f3c1c1; color: #b3261e; }
        .filter {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            margin-bottom: 24px;
            padding: 16px;
            background: #ffffff;
            border-radius: 12px;
        }
        .filter label {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 13px;
            color: #5b6778;
        }
        .filter input,
        .filter select {
            min-width: 160px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            color: #1b2430;
            background: #ffffff;
        }
        .filter input[name="q"] { min-width: 260px; }
        .button,
        .reset {
            display: inline-block;
            padding: 9px 14px;
            border: 1px solid #1f3a5f;
            border-radius: 8px;
            font: inherit;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .button { background: #1f3a5f; color: #ffffff; }
        .reset { background: #ffffff; color: #1f3a5f; }
        .button.small,
        .reset.small { padding: 6px 12px; font-size: 12px; line-height: 15px; }
        .summary { margin-bottom: 12px; color: #5b6778; font-size: 14px; }
        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            background: #ffffff;
            border-radius: 12px;
        }
        th,
        td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }
        th { background: #eef2f7; color: #334155; }
        tbody tr:last-child td { border-bottom: 0; }
        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 500;
            line-height: 13px;
        }
        .badge-success { background: #e3f4ea; color: #1e7b45; }
        .badge-warning { background: #fff1d6; color: #8a5a00; }
        .badge-info { background: #e5eefb; color: #1f4fa3; }
        .badge-danger { background: #fde7e7; color: #b3261e; }
        .badge-neutral { background: #f4f6fa; color: #6b7685; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-start; }
        .actions form { margin: 0; }
        .tolak summary { cursor: pointer; font-size: 12px; font-weight: 600; color: #b3261e; }
        .tolak textarea {
            display: block;
            margin-top: 6px;
            width: 220px;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            font-size: 12px;
        }
        .muted { color: #5b6778; font-size: 12px; }
        .empty {
            padding: 24px;
            background: #ffffff;
            border-radius: 12px;
            color: #5b6778;
        }
        .pagination { margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Daftar Permintaan</h1>
    <p class="subtitle">Kelola permintaan pengambilan barang temuan. Tombol aksi hanya tersedia untuk Admin.</p>

    @if (session('status'))
        <div class="flash success" role="status">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="flash error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash error" role="alert">{{ $errors->first() }}</div>
    @endif

    @php
        $statusLabels = collect(\App\Enums\StatusPermintaan::cases())
            ->mapWithKeys(fn ($status) => [$status->value => $status->label()]);
        $bisaAksi = auth()->check() && auth()->user()->isAdmin();
    @endphp

    <form class="filter" method="GET" action="{{ route('admin.permintaan.index') }}">
        <label>
            Cari kode resi atau nama pemohon
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                maxlength="100"
                placeholder="Contoh: LF-20261109 atau Rina"
            >
        </label>

        <label>
            Status
            <select name="status">
                <option value="">Semua status</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Tanggal awal
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
        </label>

        <label>
            Tanggal akhir
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
        </label>

        <button class="button" type="submit">Terapkan</button>
        <a class="reset" href="{{ route('admin.permintaan.index') }}">Reset</a>
    </form>

    @if ($permintaan->isEmpty())
        <div class="empty">Belum ada permintaan yang sesuai dengan filter.</div>
    @else
        <p class="summary">Menampilkan {{ $permintaan->total() }} permintaan.</p>

        <table>
            <thead>
                <tr>
                    <th>Kode resi</th>
                    <th>Nomor permintaan</th>
                    <th>Pemohon</th>
                    <th>Tanggal pengajuan</th>
                    <th>Status</th>
                    <th>Diproses oleh</th>
                    @if ($bisaAksi)
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($permintaan as $item)
                    <tr>
                        <td>{{ $item->kode_resi }}</td>
                        <td>{{ $item->nomor_permintaan }}</td>
                        <td>{{ $item->nama_pemohon }}</td>
                        <td>{{ $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $item->status->badgeVariant() }}">{{ $item->status->label() }}</span>
                        </td>
                        <td>{{ $item->pemroses?->name ?? '-' }}</td>

                        @if ($bisaAksi)
                            <td class="actions">
                                @if ($item->status === \App\Enums\StatusPermintaan::Diajukan)
                                    <form method="POST" action="{{ route('admin.permintaan.setujui', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button small" type="submit">Setujui</button>
                                    </form>

                                    <details class="tolak">
                                        <summary>Tolak</summary>
                                        <form method="POST" action="{{ route('admin.permintaan.tolak', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <textarea name="alasan_admin" rows="2" minlength="5" maxlength="1000" required placeholder="Alasan penolakan"></textarea>
                                            <button class="reset small" type="submit">Kirim</button>
                                        </form>
                                    </details>
                                @elseif ($item->status === \App\Enums\StatusPermintaan::Disetujui)
                                    <form method="POST" action="{{ route('admin.permintaan.diambil', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button small" type="submit">Tandai diambil</button>
                                    </form>
                                @elseif ($item->status === \App\Enums\StatusPermintaan::Diambil)
                                    <span class="muted">{{ $item->tanggal_diproses?->format('d/m/Y H:i') ?? 'Sudah diambil' }}</span>
                                @else
                                    <span class="muted" title="{{ $item->alasan_admin }}">{{ \Illuminate\Support\Str::limit($item->alasan_admin ?? 'Ditolak', 40) }}</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $permintaan->links() }}
        </div>
    @endif
</body>
</html>
