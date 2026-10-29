<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\StockMutation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    /**
     * Export assets to XLSX or CSV.
     *
     * @param Collection|Builder|null $query
     * @param string $format 'xlsx' or 'csv'
     * @return BinaryFileResponse
     */
    public static function exportAssets($query = null, string $format = 'xlsx'): BinaryFileResponse
    {
        if ($query instanceof Builder) {
            $assets = $query->with(['category', 'room'])->get();
        } elseif ($query instanceof Collection) {
            $assets = $query->loadMissing(['category', 'room']);
        } else {
            $assets = Asset::with(['category', 'room'])->orderBy('id', 'asc')->get();
        }

        $headers = [
            'No',
            'Kode Aset',
            'Nama Barang / Aset',
            'Ukuran',
            'Jumlah',
            'Status Penempatan',
            'Kategori',
            'Ruangan',
            'PIC / Pemegang',
            'Kontak PIC',
            'Kondisi',
            'Merk / Brand',
            'Tgl Pembelian',
            'Nilai Perolehan (Rp)',
            'Nilai Buku Saat Ini (Rp)',
            'Catatan',
            'Tgl Terdaftar',
        ];

        $rows = [];
        $no = 1;
        foreach ($assets as $asset) {
            $rows[] = [
                $no++,
                $asset->asset_code,
                $asset->name,
                $asset->size ?? '-',
                (int) $asset->quantity,
                $asset->status,
                $asset->category?->name ?? '-',
                $asset->room?->name ?? '-',
                $asset->pic_name ?: 'Barang Bersama',
                $asset->pic_contact ?? '-',
                $asset->condition,
                $asset->brand ?? '-',
                $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-',
                $asset->price !== null ? (float) $asset->price : 0,
                (float) $asset->current_book_value,
                $asset->notes ?? '-',
                $asset->created_at ? $asset->created_at->format('d/m/Y H:i') : '-',
            ];
        }

        $dateStr = now()->format('Y-m-d_His');
        $fileName = "Rekap-Aset-PKM-{$dateStr}.{$format}";

        return self::downloadFile($fileName, $headers, $rows, $format, 'Data Aset Kantor');
    }

    /**
     * Export stock mutations to XLSX or CSV.
     *
     * @param Collection|Builder|null $query
     * @param string $format 'xlsx' or 'csv'
     * @return BinaryFileResponse
     */
    public static function exportStockMutations($query = null, string $format = 'xlsx'): BinaryFileResponse
    {
        if ($query instanceof Builder) {
            $mutations = $query->with(['supply.category', 'room'])->get();
        } elseif ($query instanceof Collection) {
            $mutations = $query->loadMissing(['supply.category', 'room']);
        } else {
            $mutations = StockMutation::with(['supply.category', 'room'])->orderBy('mutation_date', 'desc')->get();
        }

        $headers = [
            'No',
            'Tgl Mutasi',
            'Kode Barang',
            'Nama Perlengkapan / ATK',
            'Kategori',
            'Tipe Mutasi',
            'Jumlah',
            'Satuan',
            'Stok Sebelum',
            'Stok Setelah',
            'Ruangan Tujuan',
            'Catatan',
            'Waktu Input',
        ];

        $rows = [];
        $no = 1;
        foreach ($mutations as $m) {
            $typeLabel = $m->type === 'in' ? 'Masuk (IN)' : 'Keluar (OUT)';
            $rows[] = [
                $no++,
                $m->mutation_date ? $m->mutation_date->format('d/m/Y') : '-',
                $m->supply?->item_code ?? '-',
                $m->supply?->name ?? '-',
                $m->supply?->category?->name ?? '-',
                $typeLabel,
                (int) $m->quantity,
                $m->supply?->unit ?? 'pcs',
                (int) $m->stock_before,
                (int) $m->stock_after,
                $m->room?->name ?? '-',
                $m->notes ?? '-',
                $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-',
            ];
        }

        $dateStr = now()->format('Y-m-d_His');
        $fileName = "Rekap-Mutasi-Stok-PKM-{$dateStr}.{$format}";

        return self::downloadFile($fileName, $headers, $rows, $format, 'Mutasi Stok');
    }

    /**
     * Generate file and return binary file download response with auto-deletion.
     */
    protected static function downloadFile(string $fileName, array $headers, array $rows, string $format, string $sheetTitle): BinaryFileResponse
    {
        $exportDir = storage_path('app/exports');
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $filePath = $exportDir . DIRECTORY_SEPARATOR . uniqid('export_') . '.' . $format;

        if ($format === 'csv') {
            $handle = fopen($filePath, 'w+');
            // Write UTF-8 BOM so Excel opens CSV with proper encoding
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);

            $mime = 'text/csv';
        } else {
            SimpleXlsxExporter::create($filePath, $headers, $rows, $sheetTitle);
            $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => $mime,
        ])->deleteFileAfterSend(true);
    }
}
