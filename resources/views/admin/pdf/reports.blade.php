<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Laporan TrashReport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #16a34a;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #475569;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TrashReport - Rekapitulasi Laporan Masuk</h1>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Kode Laporan</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Pelapor</th>
                <th width="15%">Wilayah</th>
                <th width="20%">Alamat</th>
                <th width="10%">Status</th>
                <th width="10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $laporan)
                <tr>
                    <td>{{ $laporan->kode_laporan }}</td>
                    <td>{{ $laporan->dilaporkan_pada->format('d/m/Y H:i') }}</td>
                    <td>{{ $laporan->user->name ?? '-' }}</td>
                    <td>{{ $laporan->wilayah->nama ?? 'Umum' }}</td>
                    <td>{{ $laporan->alamat }}</td>
                    <td>{{ $laporan->status }}</td>
                    <td>{{ $laporan->petugas->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
