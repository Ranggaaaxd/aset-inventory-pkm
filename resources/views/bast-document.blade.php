<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST Inventaris - {{ $asset->asset_code }}</title>
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
        .toolbar {
            width: min(850px, 100%);
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
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
        .letterhead {
            padding-bottom: 16px;
            border-bottom: 3px double #172033;
            text-align: center;
        }
        .letterhead h1 { margin: 0; font-size: 20px; letter-spacing: 1px; }
        .letterhead p { margin: 3px 0 0; font-size: 11px; }
        h2 {
            margin: 30px 0 18px;
            font-size: 16px;
            text-align: center;
            text-decoration: underline;
        }
        p { margin: 12px 0; }
        .item-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .item-table th, .item-table td {
            padding: 9px;
            border: 1px solid #596579;
            text-align: left;
            vertical-align: top;
        }
        .item-table th { width: 28%; background: #f1f5f9; }
        .asset-photo {
            display: block;
            max-width: 280px;
            max-height: 220px;
            margin-top: 6px;
            object-fit: contain;
        }
        .photo-placeholder { color: #64748b; font-style: italic; }
        .signatures {
            margin-top: 46px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        .signature-space { height: 95px; }
        .signature-name { font-weight: 700; text-decoration: underline; }
        @page { size: A4 portrait; margin: 15mm; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none; }
            .document {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
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
            <p>BERITA ACARA SERAH TERIMA INVENTARIS PERUSAHAAN</p>
        </header>

        <h2>BERITA ACARA SERAH TERIMA INVENTARIS KANTOR (BAST)</h2>
        <p>
            Pada tanggal {{ $printedAt->format('d/m/Y') }}, telah dilakukan serah terima
            inventaris kantor antara:
        </p>
        <p>
            <strong>Pihak Pertama</strong>: Pengelola Logistik PT Pakuan Karya Mandiri,
            selanjutnya disebut sebagai <strong>Yang Menyerahkan</strong>.
        </p>
        <p>
            <strong>Pihak Kedua</strong>: {{ $asset->pic_name ?: 'Penerima / Pengguna Barang' }},
            selanjutnya disebut sebagai <strong>Yang Menerima</strong>.
        </p>
        <p>Rincian barang yang diserahterimakan adalah sebagai berikut:</p>

        <table class="item-table">
            <tbody>
                <tr>
                    <th>Kode Aset</th>
                    <td>{{ $asset->asset_code }}</td>
                </tr>
                <tr>
                    <th>Nama Aset</th>
                    <td>{{ $asset->name }}</td>
                </tr>
                <tr>
                    <th>Spesifikasi / Brand</th>
                    <td>{{ $asset->brand ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Kondisi</th>
                    <td>{{ $asset->condition }}</td>
                </tr>
                <tr>
                    <th>Foto Barang</th>
                    <td>
                        @if ($imageUrl)
                            <img class="asset-photo" src="{{ $imageUrl }}" alt="Foto {{ $asset->name }}">
                        @else
                            <span class="photo-placeholder">Foto aset belum tersedia.</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <p>
            Pihak Kedua menyatakan telah menerima aset tersebut dalam kondisi sebagaimana
            tercantum di atas. Pihak Kedua berkewajiban menggunakan aset sesuai kebutuhan
            pekerjaan, menjaga dan merawat aset dengan baik, serta segera melaporkan kehilangan
            atau kerusakan kepada perusahaan. Aset tetap merupakan milik PT Pakuan Karya Mandiri
            dan wajib dikembalikan apabila diminta atau ketika hubungan kerja berakhir.
        </p>
        <p>
            Berita acara ini dibuat dengan sebenar-benarnya untuk digunakan sebagaimana mestinya.
            Dicetak pada {{ $printedAt->format('d/m/Y') }}.
        </p>

        <footer class="signatures">
            <div>
                <strong>Yang Menyerahkan</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(Pengelola Logistik)</div>
            </div>
            <div>
                <strong>Yang Menerima</strong>
                <div class="signature-space"></div>
                <div class="signature-name">({{ $asset->pic_name ?: 'PIC / Karyawan' }})</div>
            </div>
            <div>
                <strong>Mengetahui</strong>
                <div class="signature-space"></div>
                <div class="signature-name">(HRD / Direktur)</div>
            </div>
        </footer>
    </main>
</body>
</html>
