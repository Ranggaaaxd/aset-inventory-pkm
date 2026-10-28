<?php

namespace App\Filament\Resources\StockMutations\Tables;

use App\Services\ExportService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class StockMutationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supply.name')
                    ->searchable(),
                TextColumn::make('room.name')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                        'in' => 'IN',
                        'out' => 'OUT',
                        default => strtoupper($state),
                    }),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_before')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_after')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mutation_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export_excel')
                        ->label('Ekspor ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(fn ($livewire) => ExportService::exportStockMutations($livewire->getFilteredTableQuery(), 'xlsx')),
                    Action::make('export_csv')
                        ->label('Ekspor ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->color('info')
                        ->action(fn ($livewire) => ExportService::exportStockMutations($livewire->getFilteredTableQuery(), 'csv')),
                ])
                ->label('Ekspor Mutasi Stok')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('primary')
                ->button(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_selected_excel')
                        ->label('Ekspor Terpilih ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action(fn (Collection $records) => ExportService::exportStockMutations($records, 'xlsx')),
                    BulkAction::make('export_selected_csv')
                        ->label('Ekspor Terpilih ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->action(fn (Collection $records) => ExportService::exportStockMutations($records, 'csv')),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
