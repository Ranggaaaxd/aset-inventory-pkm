<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAP Penghapusan Aset - {{ $asset->asset_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            color: #172033;
            background: #eef1f5;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }
        .toolbar { width: min(850px, 100%); margin: 0 auto 16px; text-align: right; }
        .toolbar button {
            padding: 10px 16px;
            border: 0;
            border-radius: 4px;
            background: #164477;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .document {
            width: min(850px, 100%);
            min-height: 1050px;
            margin: auto;
            padding: 48px 56px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .12);
        }
        .letterhead { padding-bottom: 16px; border-bottom: 3px double #172033; text-align: center; }
        .letterhead h1 { margin: 0; font-size: 20px; letter-spacing: 1px; }
        .letterhead p { margin: 3px 0 0; font-size: 11px; }
        h2 { margin: 30px 0 18px; font-size: 16px; text-align: center; text-decoration: underline; }
        p { margin: 12px 0; }
        table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        th, td { padding: 9px; border: 1px solid #596579; text-align: left; vertical-align: top; }
        th { width: 30%; background: #f1f5f9; }
        .signatures { margin-top: 54px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; text-align: center; }
        .signature-space { height: 100px; }
        .signature-name { font-weight: 700; text-decoration: underline; }
        @page { size: A4 portrait; margin: 15mm; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none; }
            .document { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
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
            <p>DOKUMEN RESMI PENGHAPUSAN ASET INVENTARIS</p>
        </header>

        <h2>BERITA ACARA PENGHAPUSAN ASET INVENTARIS</h2>
        <p>
            Pada tanggal {{ $disposal->disposal_date->format('d/m/Y') }}, telah dilakukan
            pelepasan dan penghapusan aset inventaris perusahaan berdasarkan persetujuan
            {{ $disposal->approved_by }} dengan rincian sebagai berikut:
        </p>

        <table>
            <tbody>
                <tr><th>Kode Aset</th><td>{{ $asset->asset_code }}</td></tr>
                <tr><th>Nama Aset</th><td>{{ $asset->name }}</td></tr>
                <tr><th>Kategori</th><td>{{ $asset->category?->name ?? '-' }}</td></tr>
                <tr><th>Ruangan Terakhir</th><td>{{ $asset->room?->name ?? '-' }}</td></tr>
                <tr><th>Merk / Brand</th><td>{{ $asset->brand ?: '-' }}</td></tr>
                <tr><th>Kondisi Terakhir</th><td>{{ $asset->condition }}</td></tr>
                <tr><th>Tanggal Pelepasan</th><td>{{ $disposal->disposal_date->format('d/m/Y') }}</td></tr>
                <tr><th>Metode Pelepasan</th><td>{{ $disposal->disposal_method }}</td></tr>
                <tr>
                    <th>Nilai Penjualan</th>
                    <td>{{ $disposal->disposal_method === 'Dijual / Dilelang' ? 'Rp '.number_format($disposal->disposal_amount, 0, ',', '.') : '-' }}</td>
                </tr>
                <tr><th>Alasan Penghapusan</th><td>{{ $disposal->reason }}</td></tr>
                <tr><th>Pejabat Penyetuju</th><td>{{ $disposal->approved_by }}</td></tr>
            </tbody>
        </table>

        <p>
            Dengan ditandatanganinya berita acara ini, aset tersebut dinyatakan telah dilepas
            dari daftar inventaris PT Pakuan Karya Mandiri sesuai metode dan alasan yang
            tercantum di atas. Berita acara ini dibuat untuk digunakan sebagaimana mestinya.
        </p>
        <p style="text-align: right">Dicetak pada {{ $printedAt->format('d/m/Y') }}</p>

        <footer class="signatures">
            <div>
                <strong>Pengelola Aset</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(................................)</div>
            </div>
            <div>
                <strong>Keuangan</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(................................)</div>
            </div>
            <div>
                <strong>Direktur</strong>
                <div class="signature-space"></div>
                <div class="signature-name">({{ $disposal->approved_by }})</div>
            </div>
        </footer>
    </main>
</body>
</html>
