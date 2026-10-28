<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use App\Services\ExportService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('room.name')
                    ->searchable(),
                TextColumn::make('pic_name')
                    ->label('PIC / Pemegang')
                    ->searchable()
                    ->placeholder('Barang Bersama')
                    ->description(fn (Asset $record): ?string => $record->pic_contact)
                    ->sortable(),
                TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('current_book_value')
                    ->label('Nilai Buku Saat Ini')
                    ->money('IDR', locale: 'id'),
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
                SelectFilter::make('room_id')
                    ->relationship('room', 'name')
                    ->label('Ruangan')
                    ->preload(),
                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                    ]),
                SelectFilter::make('ownership')
                    ->label('Status Kepemilikan (PIC)')
                    ->options([
                        'has_pic' => 'Ada PIC (Aset Pribadi)',
                        'shared' => 'Barang Bersama / Tanpa PIC',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (($data['value'] ?? null) === 'has_pic') {
                            $query->whereNotNull('pic_name')->where('pic_name', '!=', '');
                        } elseif (($data['value'] ?? null) === 'shared') {
                            $query->where(function (Builder $q) {
                                $q->whereNull('pic_name')->orWhere('pic_name', '');
                            });
                        }
                    }),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export_excel')
                        ->label('Ekspor ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(fn ($livewire) => ExportService::exportAssets($livewire->getFilteredTableQuery(), 'xlsx')),
                    Action::make('export_csv')
                        ->label('Ekspor ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->color('info')
                        ->action(fn ($livewire) => ExportService::exportAssets($livewire->getFilteredTableQuery(), 'csv')),
                ])
                ->label('Ekspor Data Aset')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('primary')
                ->button(),
            ])
            ->recordActions([
                Action::make('print_label')
                    ->label('Cetak Label')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('info')
                    ->modalHeading(fn (Asset $record): string => 'Cetak Stiker Label - ' . $record->asset_code)
                    ->modalWidth(Width::Medium)
                    ->modalContent(fn (Asset $record) => view('filament.resources.assets.print-label-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_selected_excel')
                        ->label('Ekspor Terpilih ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action(fn (Collection $records) => ExportService::exportAssets($records, 'xlsx')),
                    BulkAction::make('export_selected_csv')
                        ->label('Ekspor Terpilih ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->action(fn (Collection $records) => ExportService::exportAssets($records, 'csv')),
                    BulkAction::make('print_selected_labels')
                        ->label('Cetak Label Terpilih')
                        ->icon(Heroicon::OutlinedPrinter)
                        ->color('info')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->route('assets.print-batch', ['ids' => $ids]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
