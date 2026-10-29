<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\StockMutation;
use Illuminate\Contracts\View\View;

class OfficialReportController extends Controller
{
    public function assets(): View
    {
        $assets = Asset::with(['category', 'room'])
            ->orderBy('asset_code')
            ->get();

        $rows = $assets->map(fn (Asset $asset): array => [
            $asset->asset_code,
            $asset->name,
            $asset->size ?? '-',
            (int) $asset->quantity,
            $asset->status,
            $asset->category?->name ?? '-',
            $asset->room?->name ?? '-',
            $asset->condition,
            'Rp ' . number_format((float) ($asset->price ?? 0), 0, ',', '.'),
            'Rp ' . number_format($asset->current_book_value, 0, ',', '.'),
            $asset->pic_name ?: 'Barang Bersama',
        ])->all();

        return view('reports.official', [
            'title' => 'Rekapitulasi Inventaris Aset Kantor',
            'printedAt' => now()->format('d/m/Y H:i'),
            'period' => null,
            'columns' => ['Kode', 'Nama Aset', 'Ukuran', 'Jumlah', 'Status Penempatan', 'Kategori', 'Ruangan', 'Kondisi', 'Harga Perolehan', 'Nilai Buku', 'PIC'],
            'rows' => $rows,
            'summaries' => [],
        ]);
    }

    public function stockMutations(): View
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $mutations = StockMutation::with(['supply', 'room'])
            ->whereBetween('mutation_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('mutation_date')
            ->orderBy('id')
            ->get();

        $rows = $mutations->map(fn (StockMutation $mutation): array => [
            $mutation->mutation_date?->format('d/m/Y') ?? '-',
            $mutation->supply?->item_code ?? '-',
            $mutation->supply?->name ?? '-',
            $mutation->type === 'in' ? 'Masuk' : 'Keluar',
            number_format($mutation->quantity, 0, ',', '.') . ' ' . ($mutation->supply?->unit ?? 'pcs'),
            number_format($mutation->stock_before, 0, ',', '.'),
            number_format($mutation->stock_after, 0, ',', '.'),
            $mutation->room?->name ?? '-',
        ])->all();

        return view('reports.official', [
            'title' => 'Rekapitulasi Pengeluaran & Pemasukan ATK Bulanan',
            'printedAt' => now()->format('d/m/Y H:i'),
            'period' => $startOfMonth->format('m/Y'),
            'columns' => ['Tanggal', 'Kode Barang', 'Nama ATK', 'Jenis Mutasi', 'Jumlah', 'Stok Sebelum', 'Stok Sesudah', 'Ruangan'],
            'rows' => $rows,
            'summaries' => [
                ['label' => 'Total barang masuk', 'value' => number_format($mutations->where('type', 'in')->sum('quantity'), 0, ',', '.') . ' unit'],
                ['label' => 'Total barang keluar', 'value' => number_format($mutations->where('type', 'out')->sum('quantity'), 0, ',', '.') . ' unit'],
                ['label' => 'Jumlah transaksi', 'value' => number_format($mutations->count(), 0, ',', '.') . ' transaksi'],
            ],
        ]);
    }
}
