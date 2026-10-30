<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Supply;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|array|null $columns = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalAssets = Asset::count();
        $totalAssetValue = Asset::sum('price') ?? 0;
        $totalSupplyTypes = Supply::count();
        $lowStockCount = Supply::whereColumn('stock', '<=', 'min_stock')->count();
        $activeLoans = AssetLoan::query()->where('status', 'dipinjam')->count();

        return [
            Stat::make('Total Aset Kantor', number_format($totalAssets, 0, ',', '.').' Unit')
                ->description('Jumlah seluruh aset terdaftar')
                ->descriptionIcon(Heroicon::OutlinedRectangleStack)
                ->icon(Heroicon::OutlinedRectangleStack)
                ->color('primary'),

            Stat::make('Total Nilai Aset', 'Rp '.number_format($totalAssetValue, 0, ',', '.'))
                ->description('Estimasi nilai keseluruhan aset')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('success'),

            Stat::make('Total Jenis ATK', number_format($totalSupplyTypes, 0, ',', '.').' Jenis')
                ->description('Total ragam jenis perlengkapan')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentList)
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('info'),

            Stat::make('Peringatan ATK Menipis', number_format($lowStockCount, 0, ',', '.').' Item')
                ->description($lowStockCount > 0 ? 'Stok mencapai batas minimum' : 'Stok barang aman')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger')
                ->descriptionColor('danger'),

            Stat::make('Aset Sedang Dipinjam', number_format($activeLoans, 0, ',', '.').' Unit')
                ->description('Jumlah aset yang sedang dipinjam')
                ->descriptionIcon(Heroicon::OutlinedArrowPath)
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning'),
        ];
    }
}
