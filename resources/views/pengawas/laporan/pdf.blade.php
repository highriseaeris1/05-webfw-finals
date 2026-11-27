@php
    $statusLabel = [
        'diajukan' => 'Diajukan',
        'disetujui' => 'Disetujui',
        'diambil' => 'Diambil',
        'ditolak' => 'Ditolak',
    ];

    $periode = \Illuminate\Support\Carbon::parse($tanggalAwal)->format('d/m/Y')
        .' s.d. '.
        \Illuminate\Support\Carbon::parse($tanggalAkhir)->format('d/m/Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Permintaan</title>
    <style>
        @page {
            margin: 22px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #1b2430;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #1f3a5f;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        .header .system {
            color: #6b7685;
            font-size: 10px;
        }

        h1 {
            font-size: 18px;
            margin: 3px 0 0;
        }

        .meta {
            color: #6b7685;
            font-size: 9px;
            margin-top: 6px;
        }

        .summary {
            border-collapse: collapse;
            margin-bottom: 14px;
            width: 100%;
        }

        .summary td {
            border: 1px solid #dde3ec;
            padding: 6px 8px;
            width: 20%;
        }

        .summary .label {
            color: #6b7685;
            font-size: 9px;
        }

        .summary .value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px;
        }

        table.detail {
            border-collapse: collapse;
            width: 100%;
        }

        table.detail th {
            background: #eef2f7;
            border: 1px solid #dde3ec;
            font-size: 9px;
            padding: 5px 6px;
            text-align: left;
        }

        table.detail td {
            border: 1px solid #dde3ec;
            padding: 5px 6px;
            vertical-align: top;
        }

        table.detail tr {
            page-break-inside: avoid;
        }

        .empty {
            color: #6b7685;
            padding: 12px;
            text-align: center;
        }

        .footer {
            color: #6b7685;
            font-size: 9px;
            margin-top: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="system">Sistem Informasi Lost and Found Fasilkom UNSIKA</div>
        <h1>Laporan Rekap Permintaan Pengambilan</h1>
        <div class="meta">
            Periode: <strong>{{ $periode }}</strong>
            &nbsp;|&nbsp; Dicetak: {{ $dicetakPada->format('d/m/Y H:i') }}
            &nbsp;|&nbsp; Oleh: {{ $dicetakOleh }}
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total permintaan</div>
                <div class="value">{{ $ringkasan['total'] }}</div>
            </td>
            <td>
                <div class="label">Diajukan</div>
                <div class="value">{{ $ringkasan['diajukan'] }}</div>
            </td>
            <td>
                <div class="label">Disetujui</div>
                <div class="value">{{ $ringkasan['disetujui'] }}</div>
            </td>
            <td>
                <div class="label">Diambil</div>
                <div class="value">{{ $ringkasan['diambil'] }}</div>
            </td>
            <td>
                <div class="label">Ditolak</div>
                <div class="value">{{ $ringkasan['ditolak'] }}</div>
            </td>
        </tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th style="width: 88px;">Kode resi</th>
                <th style="width: 68px;">No. permintaan</th>
                <th style="width: 100px;">Pemohon</th>
                <th style="width: 78px;">NPM/NIK</th>
                <th style="width: 86px;">Tanggal pengajuan</th>
                <th style="width: 58px;">Status</th>
                <th style="width: 86px;">Diproses oleh</th>
                <th style="width: 86px;">Tanggal diproses</th>
                <th>Alasan admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permintaan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kode_resi }}</td>
                    <td>{{ $item->nomor_permintaan }}</td>
                    <td>{{ $item->nama_pemohon }}</td>
                    <td>{{ $item->npm_nik }}</td>
                    <td>{{ $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $statusLabel[$item->status] ?? ucfirst($item->status) }}</td>
                    <td>{{ $item->pemroses?->name ?? '-' }}</td>
                    <td>{{ $item->tanggal_diproses?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $item->alasan_admin ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="10">Tidak ada permintaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak dari Sistem Informasi Lost and Found Fasilkom UNSIKA.
    </div>
</body>
</html>
