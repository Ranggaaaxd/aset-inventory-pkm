<?php

namespace App\Filament\Resources\AssetLoans\Pages;

use App\Filament\Resources\AssetLoans\AssetLoanResource;
use App\Models\AssetLoan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssetLoans extends ListRecords
{
    protected static string $resource = AssetLoanResource::class;

    public function getTabs(): array
    {
        return [
            'sedang-dipinjam' => Tab::make('Sedang Dipinjam')
                ->icon('heroicon-o-clock')
                ->badge(AssetLoan::query()->where('status', 'dipinjam')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'dipinjam')),
            'terlambat' => Tab::make('Terlambat')
                ->icon('heroicon-o-exclamation-triangle')
                ->badge(AssetLoan::query()
                    ->where('status', 'dipinjam')
                    ->where('expected_return_date', '<', now()->toDateString())
                    ->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', 'dipinjam')
                    ->where('expected_return_date', '<', now()->toDateString())),
            'riwayat-pengembalian' => Tab::make('Riwayat Pengembalian')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'dikembalikan')),
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-queue-list')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
