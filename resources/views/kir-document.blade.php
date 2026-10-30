<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Inventaris Ruangan - {{ $room->code }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            color: #172033;
            background: #eef1f5;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .toolbar { width: min(1000px, 100%); margin: 0 auto 16px; text-align: right; }
        .toolbar button {
            padding: 10px 16px;
            border: 0;
            border-radius: 4px;
            background: #15803d;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .document {
            width: min(1000px, 100%);
            min-height: 1050px;
            margin: auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .12);
        }
        .letterhead { padding-bottom: 14px; border-bottom: 3px double #172033; text-align: center; }
        .letterhead h1 { margin: 0; font-size: 20px; letter-spacing: 1px; }
        .letterhead p { margin: 3px 0 0; font-size: 11px; }
        h2 { margin: 26px 0 18px; font-size: 16px; text-align: center; text-decoration: underline; }
        .room-info { width: 100%; margin: 18px 0; border-collapse: collapse; }
        .room-info th, .room-info td { padding: 7px; border: 1px solid #596579; text-align: left; vertical-align: top; }
        .room-info th { width: 20%; background: #f1f5f9; }
        .assets { width: 100%; margin: 18px 0; border-collapse: collapse; }
        .assets th, .assets td { padding: 6px; border: 1px solid #596579; text-align: left; vertical-align: top; }
        .assets th { background: #e8eef5; text-align: center; }
        .assets .number, .assets .condition { text-align: center; }
        .signatures { margin-top: 44px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px; text-align: center; }
        .signature-space { height: 90px; }
        .signature-name { font-weight: 700; text-decoration: underline; }
        .print-date { margin-top: 26px; text-align: right; }
        @page { size: A4 portrait; margin: 12mm; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none; }
            .document { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <nav class="toolbar" aria-label="Aksi dokumen">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </nav>

    <main class="document">
        <header class="letterhead">
            <h1>PT PAKUAN KARYA MANDIRI</h1>
            <p>KARTU INVENTARIS DAN PENGENDALIAN ASET PERUSAHAAN</p>
        </header>

        <h2>KARTU INVENTARIS RUANGAN (KIR)</h2>

        <table class="room-info">
            <tbody>
                <tr><th>Kode Ruangan</th><td>{{ $room->code }}</td></tr>
                <tr><th>Nama Ruangan</th><td>{{ $room->name }}</td></tr>
                <tr><th>Lokasi Lantai</th><td>{{ $room->location_floor ?: '-' }}</td></tr>
                <tr><th>Deskripsi</th><td>{{ $room->description ?: '-' }}</td></tr>
            </tbody>
        </table>

        <table class="assets">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang &amp; Brand</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>PIC Pemegang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($room->assets as $asset)
                    <tr>
                        <td class="number">{{ $loop->iteration }}</td>
                        <td>{{ $asset->asset_code }}</td>
                        <td>{{ $asset->name }}{{ $asset->brand ? ' - '.$asset->brand : '' }}</td>
                        <td class="condition">{{ match ($asset->condition) {
                            'Rusak Ringan' => 'RR',
                            'Rusak Berat' => 'RB',
                            default => $asset->condition,
                        } }}</td>
                        <td>{{ $asset->status }}</td>
                        <td>{{ $asset->pic_name ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center">Belum ada aset yang tercatat di ruangan ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <p class="print-date">Tanggal Cetak: {{ $printedAt->format('d/m/Y') }}</p>
        <footer class="signatures">
            <div>
                <strong>Penanggung Jawab Ruangan</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(................................)</div>
            </div>
            <div>
                <strong>Bagian Umum &amp; Logistik (GA)</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(................................)</div>
            </div>
        </footer>
    </main>
</body>
</html>
