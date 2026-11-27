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
        }
        th { background: #eef2f7; color: #334155; }
        tbody tr:last-child td { border-bottom: 0; }
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
    <p class="subtitle">Kelola permintaan pengambilan barang temuan. Halaman ini hanya menampilkan data.</p>

    @php
        $statusLabels = [
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'diambil' => 'Diambil',
            'ditolak' => 'Ditolak',
        ];
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
                </tr>
            </thead>
            <tbody>
                @foreach ($permintaan as $item)
                    <tr>
                        <td>{{ $item->kode_resi }}</td>
                        <td>{{ $item->nomor_permintaan }}</td>
                        <td>{{ $item->nama_pemohon }}</td>
                        <td>{{ $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $statusLabels[$item->status] ?? $item->status }}</td>
                        <td>{{ $item->pemroses?->name ?? '-' }}</td>
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
