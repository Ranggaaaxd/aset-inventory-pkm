<?php

namespace App\Filament\Resources\Rooms\RelationManagers;

use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Models\Asset;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Daftar Aset di Ruangan Ini';

    public function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema, lockRoomToOwner: true);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_code')
                    ->label('Kode Aset')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tersedia di Gudang' => 'info',
                        'Digunakan' => 'success',
                        'Dipinjam' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('pic_name')
                    ->label('Nama Pemegang/PIC')
                    ->formatStateUsing(fn (?string $state): string => blank($state) || $state === 'Barang Bersama'
                        ? '-'
                        : $state),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalSubmitAction(false),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->schema([
                        TextEntry::make('asset_code')
                            ->label('Kode Aset')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('name')
                            ->label('Nama Barang'),
                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->formatStateUsing(fn (?string $state, Asset $record): string => $record->category?->name ?? '-'),
                        TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Baik' => 'success',
                                'Rusak Ringan' => 'warning',
                                'Rusak Berat' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Tersedia di Gudang' => 'info',
                                'Digunakan' => 'success',
                                'Dipinjam' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('pic_name')
                            ->label('Nama Pemegang/PIC')
                            ->formatStateUsing(fn (?string $state): string => blank($state) || $state === 'Barang Bersama'
                                ? '-'
                                : $state),
                    ]),
            ]);
    }
}
