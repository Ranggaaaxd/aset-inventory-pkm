<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'perlu-servis-perbaikan' => Tab::make('Perlu Servis & Perbaikan')
                ->icon('heroicon-o-wrench-screwdriver')
                ->badge(fn (): int => $this->getMaintenanceQuery()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->modifyMaintenanceQuery($query)),
        ];
    }

    private function getMaintenanceQuery(): Builder
    {
        return $this->modifyMaintenanceQuery(Asset::query());
    }

    private function modifyMaintenanceQuery(Builder $query): Builder
    {
        $deadline = now()->addDays(7)->toDateString();

        return $query->where(function (Builder $query) use ($deadline): void {
            $query
                ->whereDate('next_maintenance_date', '<=', $deadline)
                ->orWhereIn('condition', ['Rusak Ringan', 'Rusak Berat']);
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
