<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - PT Pakuan Karya Mandiri</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            background: #eef1f5;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .toolbar {
            width: min(1120px, 100%);
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .toolbar button {
            border: 0;
            border-radius: 4px;
            padding: 10px 16px;
            background: #164477;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .toolbar .close { background: #596579; }
        .report {
            width: min(1120px, 100%);
            min-height: 700px;
            margin: 0 auto;
            padding: 36px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .12);
        }
        .letterhead {
            padding-bottom: 14px;
            border-bottom: 3px double #172033;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0 0 8px;
            font-size: 20px;
            letter-spacing: 1px;
        }
        .letterhead h2 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .metadata {
            margin: 16px 0 20px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #596579;
            padding: 7px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #e8edf3;
            text-align: center;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .empty { padding: 24px; text-align: center; }
        .summaries {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 18px;
            font-weight: 700;
        }
        .signatures {
            margin: 56px 0 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
        }
        .signature { min-height: 130px; }
        .signature p { margin: 0; }
        .signature-space { height: 82px; }
        .signature strong { text-decoration: underline; }
        @page { size: A4 landscape; margin: 12mm; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none; }
            .report {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <nav class="toolbar" aria-label="Aksi laporan">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <button class="close" type="button" onclick="window.close()">Tutup</button>
    </nav>

    <main class="report">
        <header class="letterhead">
            <h1>PT PAKUAN KARYA MANDIRI</h1>
            <h2>{{ $title }}</h2>
        </header>

        <div class="metadata">
            <span>
                @if ($period)
                    Periode: {{ $period }}
                @else
                    Periode: Seluruh data aset terdaftar
                @endif
            </span>
            <span>Tanggal cetak: {{ $printedAt }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="empty" colspan="{{ count($columns) }}">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($summaries)
            <section class="summaries" aria-label="Ringkasan transaksi">
                @foreach ($summaries as $summary)
                    <span>{{ $summary['label'] }}: {{ $summary['value'] }}</span>
                @endforeach
            </section>
        @endif

        <footer class="signatures">
            <div class="signature">
                <p>Dibuat Oleh (Admin/Pengelola)</p>
                <div class="signature-space"></div>
                <strong>(........................................)</strong>
            </div>
            <div class="signature">
                <p>Mengetahui (Pimpinan/Direktur)</p>
                <div class="signature-space"></div>
                <strong>(........................................)</strong>
            </div>
        </footer>
    </main>
</body>
</html>
