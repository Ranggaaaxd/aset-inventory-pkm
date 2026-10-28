<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Label Aset - Pakuan Karya Mandiri' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
            padding: 20px;
        }

        .no-print {
            max-width: 900px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .labels-grid {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 20px;
        }

        .label-card {
            background: #ffffff;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 14px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .label-header {
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
            margin-bottom: 10px;
            text-align: center;
        }

        .label-header h1 {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 0;
            color: #000000;
        }

        .label-header p {
            font-size: 9px;
            font-weight: 600;
            color: #4b5563;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .label-body {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qr-section {
            flex-shrink: 0;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
            background: #ffffff;
        }

        .qr-section img {
            width: 90px;
            height: 90px;
            display: block;
        }

        .qr-code-text {
            font-family: monospace;
            font-size: 8px;
            font-weight: 700;
            color: #111827;
            margin-top: 3px;
            display: block;
        }

        .details-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 11px;
        }

        .detail-row {
            line-height: 1.3;
        }

        .detail-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6b7280;
            display: block;
        }

        .detail-value {
            font-weight: 600;
            color: #111827;
        }

        .detail-value.code {
            font-family: monospace;
            font-size: 13px;
            font-weight: 800;
            color: #1d4ed8;
        }

        .detail-value.title {
            font-size: 12px;
            font-weight: 700;
        }

        .label-footer {
            border-top: 1px solid #d1d5db;
            margin-top: 10px;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            color: #6b7280;
        }

        .label-footer strong {
            color: #111827;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .labels-grid {
                max-width: 100%;
                margin: 0;
                gap: 12px;
                grid-template-columns: repeat(2, 1fr);
            }

            .label-card {
                border: 2px solid #000000 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            @page {
                margin: 8mm;
                size: auto;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="font-size: 13px; color: #6b7280;">Siapkan stiker / kertas cetak, lalu klik tombol cetak di bawah.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Stiker Label
            </button>
        </div>
    </div>

    <div class="labels-grid">
        @foreach($assets as $asset)
            @php
                $qr = $qrCodes[$asset->id] ?? null;
            @endphp
            <div class="label-card">
                <div class="label-header">
                    <h1>PAKUAN KARYA MANDIRI</h1>
                    <p>SISTEM INVENTARIS & ASET KANTOR</p>
                </div>

                <div class="label-body">
                    <div class="qr-section">
                        @if($qr)
                            <img src="{{ $qr }}" alt="QR Code {{ $asset->asset_code }}" />
                        @endif
                        <span class="qr-code-text">{{ $asset->asset_code }}</span>
                    </div>

                    <div class="details-section">
                        <div class="detail-row">
                            <span class="detail-label">Kode Aset</span>
                            <span class="detail-value code">{{ $asset->asset_code }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nama Barang</span>
                            <span class="detail-value title">{{ $asset->name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ruangan</span>
                            <span class="detail-value">{{ $asset->room?->name ?? '-' }}</span>
                        </div>
                        @if($asset->pic_name)
                        <div class="detail-row">
                            <span class="detail-label">PIC / Pemegang</span>
                            <span class="detail-value" style="color: #047857; font-weight: 700;">{{ $asset->pic_name }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Kategori</span>
                            <span class="detail-value">{{ $asset->category?->name ?? '-' }}</span>
                        </div>
                        @if($asset->brand)
                        <div class="detail-row">
                            <span class="detail-label">Merk / Brand</span>
                            <span class="detail-value">{{ $asset->brand }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="label-footer">
                    <span>Kondisi: <strong>{{ $asset->condition }}</strong></span>
                    <span>Properti Resmi Pakuan Karya Mandiri</span>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>

